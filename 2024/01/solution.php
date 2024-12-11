<?php

class HistorianHisteria
{
    private array $left  = [];
    private array $right = [];

    private function readInput(): void
    {
        if ($this->left && $this->right) {
            return;
        }

        $this->left = $this->right = [];

        foreach (file(__DIR__ . '/input.txt') as $line) {
            sscanf($line, "%d   %d", $left, $right);

            $this->left[]  = $left;
            $this->right[] = $right;
        }
    }

    private function sortLists(): void
    {
        sort($this->left);
        sort($this->right);
    }

    public function calculateTotalDistanceBetweenLists(): void
    {
        $this->readInput();
        $this->sortLists();

        $distance = 0;

        for ($i = 0; $i < count($this->left); $i++) {
            $distance += abs($this->left[$i] - $this->right[$i]);
        }

        echo sprintf('Day 1 (Part 1): %d', $distance) . PHP_EOL;
    }

    public function calculateSimilarityScore(): void
    {
        $this->readInput();

        $this->left = array_unique($this->left);

        $this->sortLists();

        $score = 0;

        for ($i = 0; $i < count($this->left); $i++) {
            $score += $this->left[$i] * count(array_intersect($this->right, [$this->left[$i]]));
        }

        echo sprintf('Day 1 (Part 2): %d', $score) . PHP_EOL;
    }
}

$solution = new HistorianHisteria();
$solution->calculateTotalDistanceBetweenLists();
$solution->calculateSimilarityScore();
