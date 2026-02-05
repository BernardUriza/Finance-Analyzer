<?php

namespace App\Dto;

class InsightCollection
{
    /** @var Insight[] */
    private array $insights = [];

    public function add(Insight $insight): void
    {
        $this->insights[] = $insight;
    }

    /** @return Insight[] */
    public function all(): array
    {
        return $this->insights;
    }

    /** @return Insight[] */
    public function bySeverity(string $severity): array
    {
        return array_filter($this->insights, fn(Insight $i) => $i->severity === $severity);
    }

    public function count(): int
    {
        return count($this->insights);
    }
}
