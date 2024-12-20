<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

$patterns = array_unique(explode(", ", $lines[0]));
$length   = max(array_map('strlen', $patterns));

enum AnalyzeType {
    case CAN_OBTAIN;
    case COUNT_POSSIBILITIES;
}

function analyze(string $design, array $patterns, int $length, AnalyzeType $returnType, array &$cache): bool|int
{
    if ($design === '') {
        return match($returnType) {
            AnalyzeType::CAN_OBTAIN => true,
            AnalyzeType::COUNT_POSSIBILITIES => 1,
        };
    }

    if (isset($cache[$design])) {
        return $cache[$design];
    }

    $count = 0;

    for ($i = 0; $i <= min(strlen($design), $length); $i++) {
        if (in_array(substr($design, 0, $i), $patterns)) {
            $result = analyze(substr($design, $i), $patterns, $length, $returnType, $cache);

            switch ($returnType) {
                case AnalyzeType::CAN_OBTAIN:
                    if ($result) {
                        return $cache[$design] = true;
                    }
                    break;
                case AnalyzeType::COUNT_POSSIBILITIES:
                    $count += $result;
                    break;
            }
        }
    }

    $cache[$design] = match ($returnType) {
        AnalyzeType::CAN_OBTAIN          => false,
        AnalyzeType::COUNT_POSSIBILITIES => $count,
    };

    return $cache[$design];
}

call_user_func(function () use ($lines, $patterns, $length) {
    $result = 0;
    $cache = [];

    foreach (array_slice($lines, 2) as $design) {
        $result += analyze($design, $patterns, $length, AnalyzeType::CAN_OBTAIN, $cache) ? 1 : 0;
    }

    echo sprintf('Day 19 (Part 1): %s', $result) . PHP_EOL;
});

call_user_func(function () use ($lines, $patterns, $length) {
    $result = 0;
    $cache = [];

    foreach (array_slice($lines, 2) as $design) {
        $result += analyze($design, $patterns, $length, AnalyzeType::COUNT_POSSIBILITIES, $cache);
    }

    echo sprintf('Day 19 (Part 2): %d', $result) . PHP_EOL;
});