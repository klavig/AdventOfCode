<?php

class BridgeRepair
{
    private array $equations = [];

    public function __construct()
    {
        foreach (file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES) as $line) {
            [$answer, $numbers] = explode(':', $line);

            $answer  = intval($answer);
            $numbers = array_map('intval', explode(' ', trim($numbers)));

            $this->equations[] = ['answer' => $answer, 'numbers' => $numbers];
        }
    }

    private function evaluateExpression(int $answer, array $numbers, array $operators): int
    {
        $result = $numbers[0];

        for ($i = 0; $i < count($operators); $i++) {
            match ($operators[$i]) {
                '+'  => $result += $numbers[$i + 1],
                '*'  => $result *= $numbers[$i + 1],
                '||' => $result = intval($result . $numbers[$i + 1]),
            };
        }

        return $result === $answer;
    }

    private function canBeSolved(
        int   $answer,
        array $numbers,
        array $operators,
        array $currentOperators = [],
        int   $index = 0
    ): bool {
        if ($index === count($numbers) - 1) {
            return $this->evaluateExpression($answer, $numbers, $currentOperators);
        }

        foreach ($operators as $operator) {
            $currentOperators[$index] = $operator;

            if ($this->canBeSolved($answer, $numbers, $operators, $currentOperators, $index + 1)) {
                return true;
            }
        }

        return false;
    }

    public function totalCalibrationResult(): void
    {
        $result    = 0;
        $operators = ['+', '*'];

        foreach ($this->equations as $equation) {
            if ($this->canBeSolved($equation['answer'], $equation['numbers'], $operators)) {
                $result += $equation['answer'];
            }
        }

        echo sprintf('Day 7 (Part 1): %d', $result) . PHP_EOL;
    }

    public function totalCalibrationResultWithConcat(): void
    {
        $result    = 0;
        $operators = ['+', '*', '||'];

        foreach ($this->equations as $equation) {
            if ($this->canBeSolved($equation['answer'], $equation['numbers'], $operators)) {
                $result += $equation['answer'];
            }
        }

        echo sprintf('Day 7 (Part 2): %d', $result) . PHP_EOL;
    }
}

$solution = new BridgeRepair();
$solution->totalCalibrationResult();
$solution->totalCalibrationResultWithConcat();