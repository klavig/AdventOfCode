<?php

$lines  = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);
$stones = array_map('intval', explode(' ', $lines[0]));

call_user_func(function () use ($stones) {
    for ($i = 0; $i < 25; $i++) {
        $output = [];
        foreach ($stones as $stone) {
            if ($stone === 0) {
                $output[] = 1;
                continue;
            }
            $string = strval($stone);
            $length = strlen($string);
            if ($length % 2 === 0) {
                $output[] = intval(substr($string, 0, $length / 2));
                $output[] = intval(substr($string, $length / 2));
                continue;
            }
            $output[] = $stone * 2024;
        }
        $stones = $output;
    }

    $result = count($stones);

    echo sprintf('Day 11 (Part 1): %d', $result) . PHP_EOL;
});

call_user_func(function () use ($stones) {
    $cache = [];

    $count = function (int $stone, int $steps) use (&$cache, &$count) {
        $key = json_encode([$stone, $steps]);
        if (isset($cache[$key])) {
            return $cache[$key];
        }
        if ($steps === 0) {
            return $cache[$key] = 1;
        }
        if ($stone === 0) {
            return $cache[$key] = $count(1, $steps - 1);
        }
        $string = strval($stone);
        $length = strlen($string);
        if ($length % 2 === 0) {
            $left  = intval(substr($string, 0, $length / 2));
            $right = intval(substr($string, $length / 2));

            return $cache[$key] = $count($left, $steps - 1) + $count($right, $steps - 1);
        }

        return $cache[$key] = $count($stone * 2024, $steps - 1);
    };

    $result = 0;
    foreach ($stones as $stone) {
        $result += $count($stone, 75);
    }

    echo sprintf('Day 11 (Part 2): %d', $result) . PHP_EOL;
}, $lines);