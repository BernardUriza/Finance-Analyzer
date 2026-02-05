<?php

namespace App\Service;

use App\Dto\CalculationResult;
use App\Dto\Insight;
use App\Dto\InsightCollection;
use App\Entity\FinancialProfile;

class InsightsEngine
{
    public function analyze(FinancialProfile $profile, CalculationResult $calc): InsightCollection
    {
        $insights = new InsightCollection();

        $this->checkHousingCost($profile, $calc, $insights);
        $this->checkDebtServiceRatio($calc, $insights);
        $this->checkSavingsRate($calc, $insights);
        $this->checkInvestmentRisk($profile, $calc, $insights);
        $this->checkAgeBasedAdvice($profile, $calc, $insights);
        $this->checkCryptoAllocation($profile, $calc, $insights);
        $this->checkStocksVsHighInterestDebt($profile, $insights);
        $this->checkNetWorthTrend($calc, $insights);

        return $insights;
    }

    private function checkHousingCost(FinancialProfile $profile, CalculationResult $calc, InsightCollection $insights): void
    {
        if ($calc->totalIncome <= 0) {
            return;
        }

        $ratio = $calc->housingCostRatio;

        if ($ratio > 30) {
            $insights->add(new Insight(
                'housing',
                'Alerta de gasto en vivienda',
                sprintf(
                    'Tu gasto en vivienda es el %.1f%% de tus ingresos (el máximo recomendado es 30%%). Considera reducir $%s/mes.',
                    $ratio,
                    number_format(((float) ($profile->getExpenses()['housing'] ?? 0)) - ($calc->totalIncome * 0.30), 2)
                ),
                'warning'
            ));
        } elseif ($ratio > 0) {
            $insights->add(new Insight(
                'housing',
                'Vivienda bajo control',
                sprintf('El gasto en vivienda es %.1f%% de tus ingresos — dentro de la regla del 30%%.', $ratio),
                'success'
            ));
        }
    }

    private function checkDebtServiceRatio(CalculationResult $calc, InsightCollection $insights): void
    {
        if ($calc->totalIncome <= 0) {
            return;
        }

        $ratio = $calc->debtServiceRatio;

        if ($ratio > 40) {
            $insights->add(new Insight(
                'debt_service',
                'Carga de deuda crítica',
                sprintf('El ratio de servicio de deuda es %.1f%% — por encima del umbral de peligro del 40%%. Riesgo de estrés financiero.', $ratio),
                'danger'
            ));
        } elseif ($ratio > 20) {
            $insights->add(new Insight(
                'debt_service',
                'Pagos de deuda elevados',
                sprintf('El ratio de servicio de deuda es %.1f%%. Trata de bajarlo a menos del 20%% para tener más flexibilidad.', $ratio),
                'warning'
            ));
        } elseif ($ratio > 0) {
            $insights->add(new Insight(
                'debt_service',
                'Nivel de deuda manejable',
                sprintf('El ratio de servicio de deuda es %.1f%% — rango saludable.', $ratio),
                'success'
            ));
        }
    }

    private function checkSavingsRate(CalculationResult $calc, InsightCollection $insights): void
    {
        if ($calc->totalIncome <= 0) {
            return;
        }

        $rate = $calc->savingsRate;

        if ($rate < 0) {
            $insights->add(new Insight(
                'savings',
                'Tasa de ahorro negativa',
                'Estás gastando más de lo que ganas. Esto es insostenible y erosionará tu patrimonio.',
                'danger'
            ));
        } elseif ($rate < 10) {
            $insights->add(new Insight(
                'savings',
                'Tasa de ahorro baja',
                sprintf('Tu tasa de ahorro es %.1f%%. Apunta al menos al 15-20%% para construir patrimonio a largo plazo.', $rate),
                'warning'
            ));
        } elseif ($rate >= 20) {
            $insights->add(new Insight(
                'savings',
                'Excelente tasa de ahorro',
                sprintf('Tu tasa de ahorro de %.1f%% supera el 20%% recomendado. ¡Sigue así!', $rate),
                'success'
            ));
        } else {
            $insights->add(new Insight(
                'savings',
                'Tasa de ahorro decente',
                sprintf('Tu tasa de ahorro es %.1f%%. Buen avance — intenta llegar al 20%%.', $rate),
                'info'
            ));
        }
    }

    private function checkInvestmentRisk(FinancialProfile $profile, CalculationResult $calc, InsightCollection $insights): void
    {
        $cashAssets = array_filter(
            $profile->getFinancialAssets(),
            fn($a) => in_array(strtolower($a['type'] ?? ''), ['savings', 'cash', 'emergency_fund', 'emergency fund', 'hisa', 'money_market'])
        );
        $cashTotal = array_sum(array_map(fn($a) => (float) ($a['amount'] ?? 0), $cashAssets));
        $emergencyMin = $calc->totalExpenses * 3;

        $stockAssets = array_filter(
            $profile->getFinancialAssets(),
            fn($a) => in_array(strtolower($a['type'] ?? ''), ['stocks', 'etf', 'index_fund', 'index fund'])
        );
        $stockTotal = array_sum(array_map(fn($a) => (float) ($a['amount'] ?? 0), $stockAssets));

        if ($stockTotal > 0 && $cashTotal < $emergencyMin && $emergencyMin > 0) {
            $insights->add(new Insight(
                'risk',
                'Invirtiendo sin colchón de seguridad',
                sprintf(
                    'Tienes $%s en acciones pero solo $%s en ahorro de emergencia (necesitas mínimo $%s). Una caída del mercado podría forzarte a vender con pérdida.',
                    number_format($stockTotal, 2),
                    number_format($cashTotal, 2),
                    number_format($emergencyMin, 2)
                ),
                'warning'
            ));
        }
    }

    private function checkAgeBasedAdvice(FinancialProfile $profile, CalculationResult $calc, InsightCollection $insights): void
    {
        $age = $profile->getAge();

        if ($age < 30 && $calc->totalFinancialAssets > 0) {
            $insights->add(new Insight(
                'age',
                'El tiempo es tu mejor aliado',
                'Empezar a invertir antes de los 30 te da décadas de interés compuesto. Hasta aportaciones pequeñas crecen enormemente.',
                'info'
            ));
        }

        if ($age >= 30 && $age < 40) {
            $targetNetWorth = $calc->totalIncome * 12;
            if ($calc->netTotalAssets < $targetNetWorth && $calc->totalIncome > 0) {
                $insights->add(new Insight(
                    'age',
                    'Benchmark de patrimonio',
                    sprintf('A tu edad, tu patrimonio debería ser al menos 1x tu salario anual ($%s). Actualmente tienes $%s.', number_format($targetNetWorth, 2), number_format($calc->netTotalAssets, 2)),
                    'info'
                ));
            }
        }

        if ($age >= 40 && $age < 50) {
            $targetNetWorth = $calc->totalIncome * 12 * 3;
            if ($calc->netTotalAssets < $targetNetWorth && $calc->totalIncome > 0) {
                $insights->add(new Insight(
                    'age',
                    'Benchmark de patrimonio',
                    sprintf('A tu edad, tu patrimonio debería ser 3x tu salario anual ($%s). Actualmente tienes $%s.', number_format($targetNetWorth, 2), number_format($calc->netTotalAssets, 2)),
                    'info'
                ));
            }
        }

        if ($age >= 50) {
            $insights->add(new Insight(
                'age',
                'Enfoque en el retiro',
                'Maximiza aportaciones al Afore y fondos de retiro. Considera mover inversiones hacia opciones más conservadoras.',
                'info'
            ));
        }
    }

    private function checkCryptoAllocation(FinancialProfile $profile, CalculationResult $calc, InsightCollection $insights): void
    {
        $cryptoAssets = array_filter(
            $profile->getFinancialAssets(),
            fn($a) => strtolower($a['type'] ?? '') === 'crypto'
        );
        $cryptoTotal = array_sum(array_map(fn($a) => (float) ($a['amount'] ?? 0), $cryptoAssets));

        if ($cryptoTotal > 0 && $calc->totalFinancialAssets > 0) {
            $cryptoPercent = ($cryptoTotal / $calc->totalFinancialAssets) * 100;
            if ($cryptoPercent > 10) {
                $insights->add(new Insight(
                    'crypto',
                    'Alta exposición a crypto',
                    sprintf(
                        'Crypto representa el %.1f%% de tus activos financieros ($%s). La mayoría de asesores recomiendan mantener activos especulativos por debajo del 5-10%% del portafolio.',
                        $cryptoPercent,
                        number_format($cryptoTotal, 2)
                    ),
                    'warning'
                ));
            }
        }
    }

    private function checkStocksVsHighInterestDebt(FinancialProfile $profile, InsightCollection $insights): void
    {
        $highInterestDebts = array_filter(
            $profile->getLiabilities(),
            fn($l) => (float) ($l['interest_rate'] ?? 0) > 7
        );

        if (empty($highInterestDebts)) {
            return;
        }

        $maxDebtRate = max(array_map(fn($l) => (float) ($l['interest_rate'] ?? 0), $highInterestDebts));

        $stockAssets = array_filter(
            $profile->getFinancialAssets(),
            fn($a) => in_array(strtolower($a['type'] ?? ''), ['stocks', 'etf', 'index_fund', 'index fund'])
        );

        if (!empty($stockAssets)) {
            $avgReturn = 0;
            $totalStockAmount = 0;
            foreach ($stockAssets as $a) {
                $amount = (float) ($a['amount'] ?? 0);
                $avgReturn += $amount * (float) ($a['rate_of_return'] ?? 0);
                $totalStockAmount += $amount;
            }
            $avgReturn = $totalStockAmount > 0 ? $avgReturn / $totalStockAmount : 0;

            if ($maxDebtRate > $avgReturn) {
                $insights->add(new Insight(
                    'debt_vs_invest',
                    'La deuda cuesta más de lo que rinden las inversiones',
                    sprintf(
                        'Tu tasa de deuda más alta (%.1f%%) supera tu rendimiento promedio de inversiones (%.1f%%). Matemáticamente, pagar la deuda primero te da un "rendimiento" garantizado de %.1f%%.',
                        $maxDebtRate,
                        $avgReturn,
                        $maxDebtRate
                    ),
                    'warning'
                ));
            }
        }
    }

    private function checkNetWorthTrend(CalculationResult $calc, InsightCollection $insights): void
    {
        if ($calc->netChangeInNetWorth > 0) {
            $insights->add(new Insight(
                'trend',
                'Patrimonio en crecimiento',
                sprintf('Tu patrimonio neto crecerá aproximadamente $%s MXN este año. ¡Gran trayectoria!', number_format($calc->netChangeInNetWorth, 2)),
                'success'
            ));
        } elseif ($calc->netChangeInNetWorth < 0) {
            $insights->add(new Insight(
                'trend',
                'Patrimonio en declive',
                sprintf('Tu patrimonio neto se reducirá aproximadamente $%s MXN este año. Revisa tus gastos y estrategia de deuda.', number_format(abs($calc->netChangeInNetWorth), 2)),
                'danger'
            ));
        }
    }
}
