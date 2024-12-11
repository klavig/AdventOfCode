<?php

class GuardGallivant
{
    private const array DIRECTIONS = ['^' => 0, '>' => 1, 'v' => 2, '<' => 3];
    private const array MOVES      = [[-1, 0], [0, 1], [1, 0], [0, -1]];
    private const int   MAX_STEPS  = 100000;

    private array         $originalGrid;
    private int           $rows;
    private int           $cols;
    private int           $startRow;
    private int           $startCol;
    private int           $startDirection;
    private array         $path = [];
    private SplFixedArray $seen;

    public function __construct()
    {
        $this->originalGrid = $this->readInput();
        $this->rows         = count($this->originalGrid);
        $this->cols         = $this->rows > 0 ? count($this->originalGrid[0]) : 0;

        [$this->startRow, $this->startCol, $directionChar] = $this->findGuard();
        $this->startDirection                                 = self::DIRECTIONS[$directionChar];
        $this->originalGrid[$this->startRow][$this->startCol] = '.';

        $this->seen = new \SplFixedArray($this->rows * $this->cols);
        for ($i = 0; $i < $this->seen->getSize(); $i++) {
            $this->seen[$i] = -1;
        }
    }

    private function readInput(): array
    {
        $lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

        return array_map('str_split', $lines);
    }

    private function findGuard(): array
    {
        for ($r = 0; $r < $this->rows; $r++) {
            for ($c = 0; $c < $this->cols; $c++) {
                $ch = $this->originalGrid[$r][$c];
                if (isset(self::DIRECTIONS[$ch])) {
                    return [$r, $c, $ch];
                }
            }
        }

        throw new RuntimeException("Guard not found in the grid.");
    }

    private function move(array $grid, int $startRow, int $startCol, int $startDir): array
    {
        $visitedPositions                                      = new \SplFixedArray($this->rows * $this->cols);
        $visitedPositions[$startRow * $this->cols + $startCol] = true;
        $visitCount                                            = 1;

        $stateBase  = $startRow * $this->cols + $startCol;
        $seenStates = [$stateBase << 2 | $startDir => true];

        $row = $startRow;
        $col = $startCol;
        $dir = $startDir;

        if (empty($this->path)) {
            $this->path[]                          = ['row' => $row, 'col' => $col, 'direction' => $dir];
            $this->seen[$row * $this->cols + $col] = 0;
            $trackPath                             = true;

            for ($i = 0; $i < $this->seen->getSize(); $i++) {
                if ($i !== ($row * $this->cols + $col)) {
                    $this->seen[$i] = -1;
                }
            }
        } else {
            $trackPath = false;
        }

        $moves   = self::MOVES;
        $moveRow = [$moves[0][0], $moves[1][0], $moves[2][0], $moves[3][0]];
        $moveCol = [$moves[0][1], $moves[1][1], $moves[2][1], $moves[3][1]];
        $cols    = $this->cols;
        $rows    = $this->rows;

        $lastValidStep = 0;

        for ($steps = 1; $steps <= self::MAX_STEPS; $steps++) {
            $nr = $row + $moveRow[$dir];
            $nc = $col + $moveCol[$dir];

            if ($nr < 0 || $nr >= $rows || $nc < 0 || $nc >= $cols) {
                if ($trackPath) {
                    $this->seen[$row * $cols + $col] = $lastValidStep;
                }

                return [
                    'count'  => $visitCount,
                    'looped' => false,
                    'seen'   => $visitedPositions,
                ];
            }

            if ($grid[$nr][$nc] === '#') {
                $dir = ($dir + 1) & 3;
                continue;
            }

            $row = $nr;
            $col = $nc;

            $index = $row * $cols + $col;
            if (!$visitedPositions[$index]) {
                $visitedPositions[$index] = true;
                $visitCount++;

                if ($trackPath) {
                    $lastValidStep = $steps;
                }
            }

            if ($trackPath) {
                if ($this->seen[$index] === -1 ||
                    $steps < $this->seen[$index] ||
                    ($steps - $this->seen[$index]) > $this->cols * $this->rows) {

                    $this->seen[$index] = $steps;

                    $this->path[] = ['row' => $row, 'col' => $col, 'direction' => $dir];

                    if ($this->isPartOfLoop($row, $col, $steps)) {
                        return [
                            'count'  => $visitCount,
                            'looped' => true,
                            'seen'   => $visitedPositions,
                        ];
                    }
                }
            }

            $state = $index << 2 | $dir;
            if (isset($seenStates[$state])) {
                return [
                    'count'  => $visitCount,
                    'looped' => true,
                    'seen'   => $visitedPositions,
                ];
            }
            $seenStates[$state] = true;
        }

        return [
            'count'  => $visitCount,
            'looped' => true,
            'seen'   => $visitedPositions,
        ];
    }

    private function isPartOfLoop(int $row, int $col, int $currentStep): bool
    {
        $posIndex     = $row * $this->cols + $col;
        $previousStep = $this->seen[$posIndex];

        if ($previousStep === -1) {
            return false;
        }

        $stepDifference = $currentStep - $previousStep;

        if ($stepDifference >= 4 && $stepDifference <= ($this->rows * $this->cols * 2)) {
            $uniqueCells = 0;
            $cellsSeen   = [];

            for ($i = $currentStep; $i >= $previousStep; $i--) {
                $pathIndex = $i % count($this->path);
                $cellIndex = $this->path[$pathIndex]['row'] * $this->cols + $this->path[$pathIndex]['col'];

                if (!isset($cellsSeen[$cellIndex])) {
                    $cellsSeen[$cellIndex] = true;
                    $uniqueCells++;

                    if ($uniqueCells >= 4) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function countDistinctGuardPositions(): void
    {
        $result = $this->move($this->originalGrid, $this->startRow, $this->startCol, $this->startDirection);

        $count = 0;

        for ($i = 0; $i < $result['seen']->getSize(); $i++) {
            if ($result['seen'][$i]) {
                $count++;
            }
        }

        echo sprintf('Day 6 (Part 1): %d', $count) . PHP_EOL;
    }

    public function countPositionsForObstruction(): void
    {
        $baseResult = $this->move($this->originalGrid, $this->startRow, $this->startCol, $this->startDirection);
        if ($baseResult['looped']) {
            return;
        }

        $loopablePositions = 0;
        $startPosIndex     = $this->startRow * $this->cols + $this->startCol;

        for ($posIndex = 0; $posIndex < $baseResult['seen']->getSize(); $posIndex++) {
            if (!$baseResult['seen'][$posIndex]) {
                continue;
            }

            $r = (int)($posIndex / $this->cols);
            $c = $posIndex % $this->cols;

            if ($this->originalGrid[$r][$c] !== '.' || $posIndex === $startPosIndex) {
                continue;
            }

            $this->originalGrid[$r][$c] = '#';

            $testResult = $this->move($this->originalGrid, $this->startRow, $this->startCol, $this->startDirection);

            $this->originalGrid[$r][$c] = '.';

            if ($testResult['looped']) {
                $loopablePositions++;
            }
        }

        echo sprintf('Day 6 (Part 2): %d', $loopablePositions) . PHP_EOL;
    }
}

$solution = new GuardGallivant();
$solution->countDistinctGuardPositions();
$solution->countPositionsForObstruction();