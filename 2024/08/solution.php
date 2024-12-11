<?php

class ResonantCoolinearity
{
    private array $grid;
    private int $rows;
    private int $cols;
    private array $antennas = [];

    public function __construct()
    {
        $this->grid = [];
        foreach (file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES) as $line) {
            $this->grid[] = str_split(trim($line));
        }

        $this->rows = count($this->grid);
        $this->cols = count($this->grid[0]);

        $this->collectAntennaLocations();
    }

    private function collectAntennaLocations(): void
    {
        foreach ($this->grid as $y => $cols) {
            foreach ($cols as $x => $frequency) {
                if ($frequency === '.') {
                    continue;
                }

                $this->antennas[$frequency][] = [$y, $x];
            }
        }
    }

    public function countAntinodeUniqueLocations(): void
    {
        $antiNodes = [];

        foreach ($this->antennas as $antennas) {
            for ($i = 0; $i < count($antennas); $i++) {
                for ($j = $i + 1; $j < count($antennas); $j++) {
                    [$y1, $x1] = $antennas[$i];
                    [$y2, $x2] = $antennas[$j];

                    $antiNode = [2 * $y1 - $y2, 2 * $x1 - $x2];
                    if (!in_array($antiNode, $antiNodes)) {
                        $antiNodes[] = $antiNode;
                    }

                    $antiNode = [2 * $y2 - $y1, 2 * $x2 - $x1];
                    if (!in_array($antiNode, $antiNodes)) {
                        $antiNodes[] = $antiNode;
                    }
                }
            }
        }

        $antiNodes = array_filter($antiNodes, function (array $antiNode) {
            return $antiNode[0] >= 0 && $antiNode[0] < $this->rows && $antiNode[1] >= 0 && $antiNode[1] < $this->cols;
        });

        echo sprintf('Day 8 (Part 1): %d', count($antiNodes)) . PHP_EOL;
    }

    public function countAnyAntinodeUniqueLocations(): void
    {
        $antiNodes = [];

        foreach ($this->antennas as $antennas) {
            $length = count($antennas);
            for ($i = 0; $i < $length; $i++) {
                for ($j = 0; $j < $length; $j++) {
                    if ($i === $j) {
                        continue;
                    }

                    [$y1, $x1] = $antennas[$i];
                    [$y2, $x2] = $antennas[$j];

                    $deltaY = $y2 - $y1;
                    $deltaX = $x2 - $x1;

                    $y = $y1;
                    $x = $x1;

                    while ($y >= 0 && $y < $this->rows && $x >= 0 && $x < $this->cols) {
                        $antiNode = [$y, $x];
                        if (!in_array($antiNode, $antiNodes)) {
                            $antiNodes[] = $antiNode;
                        }

                        $y += $deltaY;
                        $x += $deltaX;
                    }
                }
            }
        }

        echo sprintf('Day 8 (Part 2): %d', count($antiNodes)) . PHP_EOL;
    }
}

$solution = new ResonantCoolinearity();
$solution->countAntinodeUniqueLocations();
$solution->countAnyAntinodeUniqueLocations();