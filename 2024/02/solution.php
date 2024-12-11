<?php

class RedNosedReports
{
    private array $reports = [];

    private function readInput(): void
    {
        if ($this->reports) {
            return;
        }

        $this->reports = file(__DIR__ . '/input.txt');
    }

    private function isReportSafe(array $levels): bool
    {
        for ($i = 1, $direction = null; $i < count($levels); $i++) {
            $diff = $levels[$i] - $levels[$i - 1];

            if ($diff < -3 || $diff > 3 || $diff === 0) {
                return false;
            }

            $currentDirection = $diff > 0 ? 1 : -1;

            if ($direction === null) {
                $direction = $currentDirection;
            } elseif ($currentDirection !== $direction) {
                return false;
            }
        }

        return true;
    }

    private function isReportSafeWithDamper(array $levels): bool
    {
        for ($i = 0; $i < count($levels); $i++) {
            $modified = array_values(array_diff_key($levels, [$i => $levels[$i]]));

            if ($this->isReportSafe($modified)) {
                return true;
            }
        }

        return false;
    }

    private function parseLevels(string $report): array
    {
        return array_map('intval', explode(' ', $report));
    }

    public function calculateSafeReports(): void
    {
        $this->readInput();

        $safe = 0;

        foreach ($this->reports as $report) {
            if ($this->isReportSafe($this->parseLevels($report))) {
                $safe++;
            }
        }

        echo sprintf('Day 2 (Part 1): %d', $safe) . PHP_EOL;
    }

    public function calculateSafeReportsWithDamper(): void
    {
        $this->readInput();

        $safe = 0;

        foreach ($this->reports as $report) {
            $levels = $this->parseLevels($report);

            if ($this->isReportSafe($levels) || $this->isReportSafeWithDamper($levels)) {
                $safe++;
            }
        }

        echo sprintf('Day 2 (Part 2): %d', $safe) . PHP_EOL;
    }
}

$solution = new RedNosedReports();
$solution->calculateSafeReports();
$solution->calculateSafeReportsWithDamper();
