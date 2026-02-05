<?php

namespace App\Dto;

class Insight
{
    public function __construct(
        public readonly string $type,
        public readonly string $title,
        public readonly string $message,
        public readonly string $severity = 'info', // info, success, warning, danger
    ) {}
}
