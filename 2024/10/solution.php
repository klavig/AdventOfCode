<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

$grid = [];
foreach ($lines as $line) {
    $grid[] = array_map('intval', str_split(trim($line)));
}

$rows = count($grid);
$cols = count($grid[0]);

$trailheads = [];
for ($r = 0; $r < $rows; $r++) {
    for ($c = 0; $c < $cols; $c++) {
        if ($grid[$r][$c] === 0) {
            $trailheads[] = [$r, $c];
        }
    }
}

call_user_func(function () use ($grid, $rows, $cols, $trailheads) {
    $score = function (array $grid, int $r, int $c) use ($rows, $cols): int {
        $q = [[$r, $c]];
        $seen = [[$r, $c]];
        $summits = 0;

        while (count($q) > 0) {
            [$cr, $cc] = array_shift($q);
            foreach ([[$cr - 1, $cc], [$cr, $cc + 1], [$cr + 1, $cc], [$cr, $cc - 1]] as [$nr, $nc]) {
                if ($nr < 0 || $nc < 0 || $nr >= $rows || $nc >= $cols || $grid[$nr][$nc] !== $grid[$cr][$cc] + 1 || in_array([$nr, $nc], $seen)) {
                    continue;
                }
                $seen[] = [$nr, $nc];
                if ($grid[$nr][$nc] === 9) {
                    $summits++;
                } elseif (!in_array([$nr, $nc], $q)) {
                    $q[] = [$nr, $nc];
                }
            }
        }

        return $summits;
    };

    $result = 0;
    foreach ($trailheads as [$r, $c]) {
        $result += $score($grid, $r, $c);
    }

    echo sprintf('Day 10 (Part 1): %d', $result) . PHP_EOL;
});

call_user_func(function () use ($grid, $rows, $cols, $trailheads) {
    $score = function (array $grid, int $r, int $c) use ($rows, $cols): int {
        $q = [[$r, $c]];
        $rc = json_encode([$r, $c]);
        $seen = [$rc => 1];
        $trails = 0;

        while (count($q) > 0) {
            [$cr, $cc] = array_shift($q);
            $crc = json_encode([$cr, $cc]);
            if ($grid[$cr][$cc] === 9) {
                $trails += $seen[$crc];
            }
            foreach ([[$cr - 1, $cc], [$cr, $cc + 1], [$cr + 1, $cc], [$cr, $cc - 1]] as [$nr, $nc]) {
                if ($nr < 0 || $nc < 0 || $nr >= $rows || $nc >= $cols || $grid[$nr][$nc] !== $grid[$cr][$cc] + 1) {
                    continue;
                }
                $nrc = json_encode([$nr, $nc]);
                if (isset($seen[$nrc])) {
                    $seen[$nrc] += $seen[$crc];
                    continue;
                }
                $seen[$nrc] = $seen[$crc];
                if (!in_array([$nr, $nc], $q)) {
                    $q[] = [$nr, $nc];
                }
            }
        }

        return $trails;
    };

    $result = 0;
    foreach ($trailheads as [$r, $c]) {
        $result += $score($grid, $r, $c);
    }

    echo sprintf('Day 10 (Part 2): %d', $result) . PHP_EOL;
}, $lines);