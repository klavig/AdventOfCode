<?php

const EOL = "\r\n";

[$top, $bottom] = explode(str_repeat(EOL, 2), file_get_contents(__DIR__ . '/input.txt'));

$grid  = array_map(fn (string $line) => str_split(trim($line)), explode(EOL, $top));
$rows  = count($grid);
$cols  = count($grid[0]);
$moves = str_split(implode('', explode(EOL, $bottom)));

call_user_func(function () use ($grid, $rows, $cols, $moves) {
    $c = 0;
    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($grid[$r][$c] === '@') {
                break 2;
            }
        }
    }

    foreach ($moves as $move) {
        $dr = ['^' => -1, 'v' => 1][$move] ?? 0;
        $dc = ['<' => -1, '>' => 1][$move] ?? 0;

        $targets = [[$r, $c]];

        [$cr, $cc] = $targets[0];

        $go = true;
        while (true) {
            $cr += $dr;
            $cc += $dc;

            switch ($grid[$cr][$cc]) {
                case '#':
                    $go = false;
                    break 2;
                case 'O':
                    $targets[] = [$cr, $cc];
                    break;
                case '.':
                    break 2;
            }
        }

        if (false === $go) {
            continue;
        }

        $grid[$r][$c] = '.';
        $grid[$r + $dr][$c + $dc] = '@';

        foreach (array_slice($targets, 1) as [$br, $bc]) {
            $grid[$br + $dr][$bc + $dc] = 'O';
        }

        $r += $dr;
        $c += $dc;
    }

    $total = 0;

    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($grid[$r][$c] !== 'O') {
                continue;
            }

            $total += 100 * $r + $c;
        }
    }

    echo sprintf('Day 15 (Part 1): %d', $total) . PHP_EOL;
});

$expanded = array_map(fn (string $line) => str_split(str_replace(['#', 'O', '.', '@'], ['##', '[]', '..', '@.'], trim($line))), explode(EOL, $top));
$rows     = count($expanded);
$cols     = count($expanded[0]);

call_user_func(function () use ($expanded, $rows, $cols, $moves) {
    $c = 0;
    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($expanded[$r][$c] === '@') {
                break 2;
            }
        }
    }

    foreach ($moves as $move) {
        $dr = ['^' => -1, 'v' => 1][$move] ?? 0;
        $dc = ['<' => -1, '>' => 1][$move] ?? 0;

        $go = true;

        $targets = [[$r, $c]];

        for ($i = 0; $i < count($targets); $i++) {
            [$cr, $cc] = $targets[$i];

            $nr = $cr + $dr;
            $nc = $cc + $dc;

            if (in_array([$nr, $nc], $targets)) {
                continue;
            }

            switch ($expanded[$nr][$nc]) {
                case '#':
                    $go = false;
                    break 2;
                case '[':
                    $targets[] = [$nr, $nc];
                    $targets[] = [$nr, $nc + 1];
                    break;
                case ']':
                    $targets[] = [$nr, $nc];
                    $targets[] = [$nr, $nc - 1];
                    break;
            }
        }

        if (false === $go) {
            continue;
        }

        $copy = $expanded;

        $expanded[$r][$c] = '.';
        $expanded[$r + $dr][$c + $dc] = '@';

        for ($i = 0; $i < count($targets); $i++) {
            [$br, $bc] = $targets[$i];

            $expanded[$br][$bc] = '.';
        }

        for ($i = 0; $i < count($targets); $i++) {
            [$br, $bc] = $targets[$i];

            $expanded[$br + $dr][$bc + $dc] = $copy[$br][$bc];
        }

        $r += $dr;
        $c += $dc;
    }

    $total = 0;

    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($expanded[$r][$c] === '[') {
                $total += 100 * $r + $c;
            }
        }
    }

    echo sprintf('Day 15 (Part 2): %d', $total) . PHP_EOL;
});
