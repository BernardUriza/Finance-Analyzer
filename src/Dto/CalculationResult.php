<?php

namespace App\Dto;

class CalculationResult
{
    public float $totalIncome = 0;
    public float $totalExpenses = 0;
    public float $netIncome = 0;
    public float $savingsRate = 0;

    public float $totalFinancialAssets = 0;
    public float $totalPhysicalAssets = 0;
    public float $totalLiabilities = 0;
    public float $netFinancialAssets = 0;
    public float $netTotalAssets = 0;

    public float $expectedAnnualGain = 0;
    public float $annualDepreciation = 0;
    public float $annualInterestCost = 0;
    public float $netChangeInNetWorth = 0;

    public float $debtServiceRatio = 0;
    public float $housingCostRatio = 0;

    /** @var array<int, array{name: string, status: string, description: string, action: string}> */
    public array $priorities = [];
}
