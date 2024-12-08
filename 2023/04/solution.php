<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

call_user_func(function (array $lines) {
    $total = 0;

    foreach ($lines as $line) {
        $line = trim(substr($line, strpos($line, ':')));
        [$a, $b] = array_map('trim', explode('|', $line));
        $a = array_map('intval', explode(' ', $a));
        $b = array_map('intval', explode(' ', $b));

        $j = count(array_filter(array_intersect($a, $b)));

        if ($j > 0) {
            $total += pow(2, $j - 1);
        }
    }

    echo sprintf('Day 4 (Part 1): %d', $total) . PHP_EOL;
}, $lines);

call_user_func(function (array $lines) {
    $map = [];

    foreach ($lines as $i => $line) {
        if (!isset($map[$i])) {
            $map[$i] = 1;
        }

        $line = trim(substr($line, strpos($line, ':')));
        [$a, $b] = array_map('trim', explode('|', $line));
        $a = array_map('intval', explode(' ', $a));
        $b = array_map('intval', explode(' ', $b));

        $j = count(array_filter(array_intersect($a, $b)));

        for ($n = $i + 1; $n < $i + $j + 1; $n++) {
            $map[$n] = ($map[$n] ?? 1) + $map[$i];
        }
    }

    $total = array_sum($map);

    echo sprintf('Day 4 (Part 2): %d', $total) . PHP_EOL;
}, $lines);