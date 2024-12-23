<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

function step(int $secret): int
{
    $secret = ($secret ^ ($secret << 6)) & 0xFFFFFF;
    $secret = ($secret ^ ($secret >> 5)) & 0xFFFFFF;

    return ($secret ^ ($secret << 11)) & 0xFFFFFF;
}

call_user_func(function () use ($lines) {
    $result = 0;

    foreach ($lines as $line) {
        $number = intval($line);
        for ($i = 0; $i < 2000; $i++) {
            $number = step($number);
        }
        $result += $number;
    }

    echo sprintf('Day 22 (Part 1): %s', $result) . PHP_EOL;
});

call_user_func(function () use ($lines) {
    $seqs = [];

    foreach ($lines as $line) {
        $number = intval($line);
        $seen = [];
        $prev = $number % 10;
        $diffs = [];
        for ($i = 0; $i < 2000; $i++) {
            $number = step($number);
            $current = $number % 10;
            $diffs[] = $prev - $current;
            $prev = $current;
            if (count($diffs) >= 4) {
                $seq = $diffs;
                array_shift($diffs);
                if (in_array($seq, $seen)) {
                    continue;
                }
                $seen[] = $seq;
                $key = json_encode($seq);
                if (!isset($seqs[$key])) {
                    $seqs[$key] = 0;
                }
                $seqs[$key] += $prev;
            }
        }
    }

    $result = max(array_values($seqs));

    echo sprintf('Day 22 (Part 2): %s', $result) . PHP_EOL;
});