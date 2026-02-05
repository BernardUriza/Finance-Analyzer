<?php

namespace App\Service;

use App\Dto\CalculationResult;
use App\Entity\FinancialProfile;

class CalculationEngine
{
    public function calculate(FinancialProfile $profile): CalculationResult
    {
        $result = new CalculationResult();

        $income = $profile->getIncome();
        $expenses = $profile->getExpenses();

        // Ingresos y gastos
        $result->totalIncome = array_sum(array_map('floatval', $income));
        $result->totalExpenses = array_sum(array_map('floatval', $expenses));
        $result->netIncome = $result->totalIncome - $result->totalExpenses;
        $result->savingsRate = $result->totalIncome > 0
            ? ($result->netIncome / $result->totalIncome) * 100
            : 0;

        // Activos financieros
        $result->totalFinancialAssets = 0;
        $result->expectedAnnualGain = 0;
        foreach ($profile->getFinancialAssets() as $asset) {
            $amount = (float) ($asset['amount'] ?? 0);
            $rate = (float) ($asset['rate_of_return'] ?? 0);
            $result->totalFinancialAssets += $amount;
            $result->expectedAnnualGain += $amount * ($rate / 100);
        }

        // Activos físicos
        $result->totalPhysicalAssets = 0;
        $result->annualDepreciation = 0;
        foreach ($profile->getPhysicalAssets() as $asset) {
            $value = (float) ($asset['current_value'] ?? 0);
            $depreciation = (float) ($asset['annual_depreciation_rate'] ?? 0);
            $result->totalPhysicalAssets += $value;
            $result->annualDepreciation += $value * ($depreciation / 100);
        }

        // Deudas
        $result->totalLiabilities = 0;
        $result->annualInterestCost = 0;
        $totalMonthlyDebtPayments = 0;
        foreach ($profile->getLiabilities() as $liability) {
            $balance = (float) ($liability['balance'] ?? 0);
            $rate = (float) ($liability['interest_rate'] ?? 0);
            $monthlyPayment = (float) ($liability['monthly_payment'] ?? 0);
            $result->totalLiabilities += $balance;
            $result->annualInterestCost += $balance * ($rate / 100);
            $totalMonthlyDebtPayments += $monthlyPayment;
        }

        // Patrimonio neto
        $result->netFinancialAssets = $result->totalFinancialAssets - $result->totalLiabilities;
        $result->netTotalAssets = $result->totalFinancialAssets + $result->totalPhysicalAssets - $result->totalLiabilities;

        // Cambio neto en patrimonio (anual)
        $annualSavings = $result->netIncome * 12;
        $result->netChangeInNetWorth = $annualSavings + $result->expectedAnnualGain - $result->annualDepreciation - $result->annualInterestCost;

        // Ratios
        $result->debtServiceRatio = $result->totalIncome > 0
            ? ($totalMonthlyDebtPayments / $result->totalIncome) * 100
            : 0;
        $result->housingCostRatio = $result->totalIncome > 0
            ? ((float) ($expenses['housing'] ?? 0) / $result->totalIncome) * 100
            : 0;

        // Prioridades
        $result->priorities = $this->calculatePriorities($profile, $result, $totalMonthlyDebtPayments);

        return $result;
    }

    private function calculatePriorities(FinancialProfile $profile, CalculationResult $result, float $totalMonthlyDebtPayments): array
    {
        $priorities = [];

        // Prioridad 1: Ingresos > Gastos
        $incomeOk = $result->netIncome > 0;
        $priorities[] = [
            'name' => 'Flujo de efectivo positivo',
            'status' => $incomeOk ? 'complete' : 'action_needed',
            'description' => $incomeOk
                ? sprintf('Superávit mensual de $%s MXN', number_format($result->netIncome, 2))
                : sprintf('Déficit mensual de $%s MXN — reduce gastos o aumenta ingresos', number_format(abs($result->netIncome), 2)),
            'action' => $incomeOk ? 'Mantén la disciplina de gasto' : 'Recorta gastos o busca más ingresos ya',
        ];

        // Prioridad 2: Pagar deuda con interés alto (>7%)
        $highInterestDebts = array_filter(
            $profile->getLiabilities(),
            fn($l) => (float) ($l['interest_rate'] ?? 0) > 7
        );
        $highDebtOk = empty($highInterestDebts);
        $highDebtTotal = array_sum(array_map(fn($l) => (float) ($l['balance'] ?? 0), $highInterestDebts));
        $priorities[] = [
            'name' => 'Eliminar deuda cara',
            'status' => $highDebtOk ? 'complete' : 'action_needed',
            'description' => $highDebtOk
                ? 'Sin deuda con interés alto (>7%) — ¡excelente!'
                : sprintf('$%s MXN en deuda con interés alto (>7%%)', number_format($highDebtTotal, 2)),
            'action' => $highDebtOk ? 'Sigue así' : 'Paga agresivamente la deuda con mayor tasa primero (método avalancha)',
        ];

        // Prioridad 3: Fondo de emergencia (3-6 meses de gastos)
        $emergencyTarget = $result->totalExpenses * 6;
        $cashAssets = array_filter(
            $profile->getFinancialAssets(),
            fn($a) => in_array(strtolower($a['type'] ?? ''), ['savings', 'cash', 'emergency_fund', 'emergency fund', 'hisa', 'money_market'])
        );
        $cashTotal = array_sum(array_map(fn($a) => (float) ($a['amount'] ?? 0), $cashAssets));
        $emergencyOk = $emergencyTarget > 0 && $cashTotal >= ($result->totalExpenses * 3);
        $emergencyFull = $emergencyTarget > 0 && $cashTotal >= $emergencyTarget;
        $priorities[] = [
            'name' => 'Fondo de emergencia',
            'status' => $emergencyFull ? 'complete' : ($emergencyOk ? 'in_progress' : 'action_needed'),
            'description' => $emergencyTarget > 0
                ? sprintf('$%s / $%s MXN (meta: 3-6 meses de gastos)', number_format($cashTotal, 2), number_format($emergencyTarget, 2))
                : 'Configura tus gastos para calcular la meta del fondo de emergencia',
            'action' => $emergencyFull
                ? 'Fondo de emergencia completo'
                : ($emergencyOk ? 'Sigue ahorrando hasta llegar a 6 meses' : 'Empieza a ahorrar para cubrir mínimo 3 meses'),
        ];

        // Prioridad 4: Invertir
        $investmentAssets = array_filter(
            $profile->getFinancialAssets(),
            fn($a) => in_array(strtolower($a['type'] ?? ''), ['stocks', 'etf', 'index_fund', 'index fund', 'bonds', 'rrsp', 'tfsa', 'investment', 'mutual_fund', 'afore', 'crypto'])
        );
        $investmentTotal = array_sum(array_map(fn($a) => (float) ($a['amount'] ?? 0), $investmentAssets));
        $investOk = $investmentTotal > 0;
        $priorities[] = [
            'name' => 'Invertir para crecer',
            'status' => $investOk ? 'in_progress' : 'action_needed',
            'description' => $investOk
                ? sprintf('$%s MXN invertidos en %d cuenta(s)', number_format($investmentTotal, 2), count($investmentAssets))
                : 'No se encontraron cuentas de inversión',
            'action' => $investOk
                ? 'Sigue con aportaciones regulares — apunta al 15-20% de tu ingreso'
                : 'Abre una cuenta de inversión y empieza con fondos indexados o CETES',
        ];

        // Prioridad 5: Pagar deuda con interés bajo
        $lowInterestDebts = array_filter(
            $profile->getLiabilities(),
            fn($l) => (float) ($l['interest_rate'] ?? 0) <= 7 && (float) ($l['balance'] ?? 0) > 0
        );
        $lowDebtOk = empty($lowInterestDebts);
        $lowDebtTotal = array_sum(array_map(fn($l) => (float) ($l['balance'] ?? 0), $lowInterestDebts));
        $priorities[] = [
            'name' => 'Reducir deuda manejable',
            'status' => $lowDebtOk ? 'complete' : 'in_progress',
            'description' => $lowDebtOk
                ? '¡Sin deudas!'
                : sprintf('$%s MXN en deuda manejable (≤7%%)', number_format($lowDebtTotal, 2)),
            'action' => $lowDebtOk
                ? 'Felicidades — mantente libre de deudas'
                : 'Sigue con pagos mínimos; el dinero extra va primero a inversiones',
        ];

        return $priorities;
    }
}
