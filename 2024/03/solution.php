<?php

class MullItOver
{
    private const string REGEX = 'mul\((?<lhs>[0-9]+),(?<rhs>[0-9]+)\)';

    private ?string $contents = null;

    private function readInput(): void
    {
        if ($this->contents) {
            return;
        }

        $this->contents = file_get_contents(__DIR__ . '/input.txt');
    }

    private function isMulDisabledAt(int $index): bool
    {
        return substr($this->contents, $index, 7) === 'don\'t()';
    }

    private function isMulEnabledAt(int $index): bool
    {
        return substr($this->contents, $index, 4) === 'do()';
    }

    private function isMulAt(int $index): bool
    {
        return substr($this->contents, $index, 3) === 'mul';
    }

    public function addUpMuls(): void
    {
        $this->readInput();

        $matches = [];
        preg_match_all(sprintf('~%s~', self::REGEX), $this->contents, $matches);

        $answer = 0;

        foreach ($matches['lhs'] as $i => $lhs) {
            $answer += $lhs * $matches['rhs'][$i];
        }

        echo sprintf('Day 3 (Part 1): %d', $answer) . PHP_EOL;
    }

    public function addUpEnabledMuls(): void
    {
        $this->readInput();

        $answer = 0;
        $multiply = true;

        for ($i = 0; $i < strlen($this->contents); $i++) {
            if ($this->isMulDisabledAt($i)) {
                $multiply = false;
                continue;
            }

            if ($this->isMulEnabledAt($i)) {
                $multiply = true;
                continue;
            }

            if ($this->isMulAt($i) && $multiply) {
                $matches = [];
                preg_match_all(sprintf('~^%s~', self::REGEX), substr($this->contents, $i), $matches);

                foreach ($matches['lhs'] as $j => $lhs) {
                    $answer += $lhs * $matches['rhs'][$j];
                }
            }
        }

        echo sprintf('Day 3 (Part 2): %d', $answer) . PHP_EOL;
    }
}

$solution = new MullItOver();
$solution->addUpMuls();
$solution->addUpEnabledMuls();