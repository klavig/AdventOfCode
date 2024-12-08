<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

call_user_func(function (array $lines) {
    $cs   = [];
    $grid = array_map(fn (string $line) => str_split($line), $lines);

    foreach ($grid as $r => $row) {
        foreach ($row as $c => $ch) {
            if (is_numeric($ch) || $ch === '.') {
                continue;
            }

            foreach ([$r - 1, $r, $r + 1] as $cr) {
                foreach ([$c - 1, $c, $c + 1] as $cc) {
                    if ($cr < 0 || $cr >= count($grid) || $cc < 0 || $cc >= count($grid[$cr]) || !is_numeric($grid[$cr][$cc])) {
                        continue;
                    }

                    while ($cc > 0 && is_numeric($grid[$cr][$cc - 1])) {
                        $cc--;
                    }

                    if (!in_array([$cr, $cc], $cs)) {
                        $cs[] = [$cr, $cc];
                    }
                }
            }
        }
    }

    $ns = [];
    foreach ($cs as [$r, $c]) {
        $s = '';
        while ($c < count($grid[$r]) && is_numeric($grid[$r][$c])) {
            $s .= $grid[$r][$c];
            $c++;
        }
        $ns[] = intval($s);
    }

    $total = array_sum($ns);

    echo sprintf('Day 3 (Part 1): %d', $total) . PHP_EOL;
}, $lines);

call_user_func(function (array $lines) {
    $total = 0;
    $grid = array_map(fn (string $line) => str_split($line), $lines);

    foreach ($grid as $r => $row) {
        foreach ($row as $c => $ch) {
            if ($ch !== '*') {
                continue;
            }

            $cs = [];

            foreach ([$r - 1, $r, $r + 1] as $cr) {
                foreach ([$c - 1, $c, $c + 1] as $cc) {
                    if ($cr < 0 || $cr >= count($grid) || $cc < 0 || $cc >= count($grid[$cr]) || !is_numeric($grid[$cr][$cc])) {
                        continue;
                    }

                    while ($cc > 0 && is_numeric($grid[$cr][$cc - 1])) {
                        $cc--;
                    }

                    if (!in_array([$cr, $cc], $cs)) {
                        $cs[] = [$cr, $cc];
                    }
                }
            }

            if (count($cs) !== 2) {
                continue;
            }

            $ns = [];
            foreach ($cs as [$cr, $cc]) {
                $s = '';
                while ($cc < count($grid[$cr]) && is_numeric($grid[$cr][$cc])) {
                    $s .= $grid[$cr][$cc];
                    $cc++;
                }
                $ns[] = intval($s);
            }

            $total += $ns[0] * $ns[1];
        }
    }

    echo sprintf('Day 3 (Part 2): %d', $total) . PHP_EOL;
}, $lines);