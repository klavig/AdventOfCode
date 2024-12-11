<?php

class CeresSearch
{
    private array $letters = [];

    private function readInput(): void
    {
        if ($this->letters) {
            return;
        }

        $this->letters = array_map('str_split', file(__DIR__ . '/input.txt'));
    }

    private function collectLetters(int $currentY, int $currentX, int $length): array
    {
        $letters = [
            'left'         => [],
            'right'        => [],
            'top'          => [],
            'bottom'       => [],
            'top_left'     => [],
            'top_right'    => [],
            'bottom_left'  => [],
            'bottom_right' => [],
        ];

        for ($i = 0; $i < $length; $i++) {
            $letters['left'][]         = $this->letters[$currentY][$currentX - $i] ?? '';
            $letters['right'][]        = $this->letters[$currentY][$currentX + $i] ?? '';
            $letters['top'][]          = $this->letters[$currentY - $i][$currentX] ?? '';
            $letters['bottom'][]       = $this->letters[$currentY + $i][$currentX] ?? '';
            $letters['top_left'][]     = $this->letters[$currentY - $i][$currentX - $i] ?? '';
            $letters['top_right'][]    = $this->letters[$currentY - $i][$currentX + $i] ?? '';
            $letters['bottom_left'][]  = $this->letters[$currentY + $i][$currentX - $i] ?? '';
            $letters['bottom_right'][] = $this->letters[$currentY + $i][$currentX + $i] ?? '';
        }

        return $letters;
    }

    public function calculateXmasWordCount(): void
    {
        $this->readInput();

        $count       = 0;
        $word        = 'XMAS';
        $words       = [$word, strrev($word)];
        $length      = strlen($word);
        $firstLetter = substr($word, 0, 1);

        foreach ($this->letters as $currentY => $rows) {
            foreach ($rows as $currentX => $letter) {
                if ($letter === $firstLetter) {
                    foreach ($this->collectLetters($currentY, $currentX, $length) as $letters) {
                        if (in_array(implode('', $letters), $words)) {
                            $count++;
                        }
                    }
                }
            }
        }

        echo sprintf('Day 4 (Part 1): %d', $count) . PHP_EOL;
    }

    public function calculateXmasCrossWordCount(): void
    {
        $this->readInput();

        $count        = 0;
        $word         = 'MAS';
        $words        = [$word, strrev($word)];
        $middleLetter = substr($word, 1, 1);

        foreach ($this->letters as $currentY => $rows) {
            foreach ($rows as $currentX => $letter) {
                if ($letter === $middleLetter) {
                    $letters = $this->collectLetters($currentY, $currentX, 2);

                    if (
                        in_array($letters['top_left'][1] . $letter . $letters['bottom_right'][1], $words) &&
                        in_array($letters['top_right'][1] . $letter . $letters['bottom_left'][1], $words)
                    ) {
                        $count++;
                    }
                }
            }
        }

        echo sprintf('Day 4 (Part 2): %d', $count) . PHP_EOL;
    }
}

$solution = new CeresSearch();
$solution->calculateXmasWordCount();
$solution->calculateXmasCrossWordCount();