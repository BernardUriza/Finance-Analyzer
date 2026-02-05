<?php

namespace App\Entity;

use App\Repository\FinancialProfileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FinancialProfileRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FinancialProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $name = 'My Profile';

    #[ORM\Column]
    private int $age = 30;

    #[ORM\Column(type: Types::JSON)]
    private array $income = [
        'employment' => 0,
        'investments' => 0,
        'other' => 0,
    ];

    #[ORM\Column(type: Types::JSON)]
    private array $expenses = [
        'housing' => 0,
        'transportation' => 0,
        'food' => 0,
        'utilities' => 0,
        'insurance' => 0,
        'healthcare' => 0,
        'entertainment' => 0,
        'personal' => 0,
        'other' => 0,
    ];

    /** @var array<int, array{type: string, label: string, amount: float, rate_of_return: float, planned_duration_years: int}> */
    #[ORM\Column(type: Types::JSON)]
    private array $financialAssets = [];

    /** @var array<int, array{type: string, label: string, current_value: float, annual_depreciation_rate: float}> */
    #[ORM\Column(type: Types::JSON)]
    private array $physicalAssets = [];

    /** @var array<int, array{type: string, label: string, balance: float, interest_rate: float, monthly_payment: float}> */
    #[ORM\Column(type: Types::JSON)]
    private array $liabilities = [];

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;
        return $this;
    }

    public function getIncome(): array
    {
        return $this->income;
    }

    public function setIncome(array $income): static
    {
        $this->income = $income;
        return $this;
    }

    public function getExpenses(): array
    {
        return $this->expenses;
    }

    public function setExpenses(array $expenses): static
    {
        $this->expenses = $expenses;
        return $this;
    }

    public function getFinancialAssets(): array
    {
        return $this->financialAssets;
    }

    public function setFinancialAssets(array $financialAssets): static
    {
        $this->financialAssets = $financialAssets;
        return $this;
    }

    public function getPhysicalAssets(): array
    {
        return $this->physicalAssets;
    }

    public function setPhysicalAssets(array $physicalAssets): static
    {
        $this->physicalAssets = $physicalAssets;
        return $this;
    }

    public function getLiabilities(): array
    {
        return $this->liabilities;
    }

    public function setLiabilities(array $liabilities): static
    {
        $this->liabilities = $liabilities;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
