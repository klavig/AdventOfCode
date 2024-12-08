<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

call_user_func(function (array $lines) {
    $total = 0;

    foreach ($lines as $i => $line) {
        $line   = trim(substr($line, strpos($line, ':') + 1));
        $groups = array_map('trim', explode(';', $line));

        $valid = true;
        foreach ($groups as $group) {
            $map = ['blue' => 0, 'green' => 0, 'red' => 0];

            $cubes = array_map('trim', explode(',', $group));

            foreach ($cubes as $cube) {
                [$amount, $color] = explode(' ', $cube);

                $map[$color] = intval($amount);
            }

            if ($map['red'] > 12 || $map['green'] > 13 || $map['blue'] > 14) {
                $valid = false;
                break;
            }
        }

        if ($valid) {
            $total += $i + 1;
        }
    }

    echo sprintf('Day 2 (Part 1): %d', $total) . PHP_EOL;
}, $lines);

call_user_func(function (array $lines) {
    $total = 0;

    foreach ($lines as $line) {
        $line   = trim(substr($line, strpos($line, ':') + 1));
        $groups = array_map('trim', explode(';', $line));
        $max    = ['blue' => 0, 'green' => 0, 'red' => 0];

        foreach ($groups as $group) {
            $map   = array_fill_keys(array_keys($max), 0);
            $cubes = array_map('trim', explode(',', $group));

            foreach ($cubes as $cube) {
                [$amount, $color] = explode(' ', $cube);

                $map[$color] = intval($amount);
            }

            foreach (array_keys($max) as $color) {
                $max[$color] = max($max[$color], $map[$color]);
            }
        }

        $total += $max['red'] * $max['green'] * $max['blue'];
    }

    echo sprintf('Day 2 (Part 2): %d', $total) . PHP_EOL;
}, $lines);