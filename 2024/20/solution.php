<?php

$lines = file(__DIR__ . '/input.txt',FILE_IGNORE_NEW_LINES);
$grid = array_map('str_split', $lines);

$rows = count($grid);
$cols = count($grid[0]);

for ($c = 0, $r = 0; $r < $rows; $r++) {
    if (false !== ($c = array_search('S', $grid[$r]))) {
        break;
    }
}

$dists = array_fill(0, $rows, array_fill(0, $cols, -1));
$dists[$r][$c] = 0;

while ($grid[$r][$c] !== 'E') {
    foreach ([[$r + 1, $c], [$r - 1, $c], [$r, $c + 1], [$r, $c - 1]] as [$nr, $nc]) {
        if ($nr < 0 || $nc < 0 || $nr >= $rows || $nc >= $cols || $grid[$nr][$nc] === '#' || $dists[$nr][$nc] !== -1) {
            continue;
        }

        $dists[$nr][$nc] = $dists[$r][$c] + 1;
        $r = $nr;
        $c = $nc;
    }
}

call_user_func(function () use ($dists, $grid, $rows, $cols) {
    $result = 0;

    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($grid[$r][$c] === '#') {
                continue;
            }

            foreach ([[$r + 2, $c], [$r + 1, $c + 1], [$r, $c + 2], [$r - 1, $c + 1]] as [$nr, $nc]) {
                if ($nr < 0 || $nc < 0 || $nr >= $rows || $nc >= $cols || $grid[$nr][$nc] === '#') {
                    continue;
                }

                if (abs($dists[$r][$c] - $dists[$nr][$nc]) >= 102) {
                    $result++;
                }
            }
        }
    }

    echo sprintf('Day 20 (Part 1): %s', $result) . PHP_EOL;
});

call_user_func(function () use ($dists, $grid, $rows, $cols) {
    $result = 0;

    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($grid[$r][$c] === '#') {
                continue;
            }

            for ($radius = 2; $radius < 21; $radius++) {
                for ($dr = 0; $dr < $radius + 1; $dr++) {
                    $seen = [];
                    $dc = $radius - $dr;

                    foreach ([[$r + $dr, $c + $dc], [$r + $dr, $c - $dc], [$r - $dr, $c + $dc], [$r - $dr, $c - $dc]] as [$nr, $nc]) {
                        if ($nr < 0 || $nc < 0 || $nr >= $rows || $nc >= $cols || $grid[$nr][$nc] === '#' || in_array([$nr, $nc], $seen)) {
                            continue;
                        }

                        $seen[] = [$nr, $nc];

                        if ($dists[$r][$c] - $dists[$nr][$nc] >= 100 + $radius) {
                            $result++;
                        }
                    }
                }
            }
        }
    }

    echo sprintf('Day 20 (Part 2): %d', $result) . PHP_EOL;
});