<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

call_user_func(function (array $lines) {
    $total = 0;

    foreach ($lines as $line) {
        $digits = array_filter(str_split($line), fn (string $character) => is_numeric($character));

        $total += intval(reset($digits) . end($digits));
    }

    echo sprintf('Day 1 (Part 1): %d', $total) . PHP_EOL;
}, $lines);

call_user_func(function (array $lines) {
    $total = 0;
    $names = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];

    foreach ($lines as $line) {
        $matches = [];
        preg_match_all('~(?=(' . implode('|', $names) . '|\d))~', $line, $matches);

        $digits = array_map(
            function (string $number) use ($names) {
                return in_array($number, $names) ? array_search($number, $names) + 1 : $number;
            },
            $matches[1]
        );

        $total += intval(reset($digits) . end($digits));
    }

    echo sprintf('Day 1 (Part 2): %d', $total) . PHP_EOL;
}, $lines);