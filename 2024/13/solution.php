<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

$blocks = [];
for ($i = 0; $i < count($lines); $i += 4) {
    sscanf($lines[$i], 'Button A: X+%d, Y+%d', $ax, $ay);
    sscanf($lines[$i + 1], 'Button B: X+%d, Y+%d', $bx, $by);
    sscanf($lines[$i + 2], 'Prize: X=%d, Y=%d', $px, $py);

    $blocks[] = [$ax, $ay, $bx, $by, $px, $py];
}

call_user_func(function () use ($blocks) {
    $total = 0;

    foreach ($blocks as [$ax, $ay, $bx, $by, $px, $py]) {
        $ca = ($px * $by - $py * $bx) / ($ax * $by - $ay * $bx);
        $cb = ($px - $ax * $ca) / $bx;

        if (fmod($ca, 1) == 0 && fmod($cb, 1) == 0) {
            if ($ca <= 100 && $cb <= 100) {
                $total += intval($ca * 3 + $cb);
            }
        }
    }

    echo sprintf('Day 13 (Part 1): %d', $total) . PHP_EOL;
});

call_user_func(function () use ($blocks) {
    $total = 0;

    foreach ($blocks as [$ax, $ay, $bx, $by, $px, $py]) {
        $px += 10000000000000;
        $py += 10000000000000;
        $ca = ($px * $by - $py * $bx) / ($ax * $by - $ay * $bx);
        $cb = ($px - $ax * $ca) / $bx;
        if (fmod($ca, 1) == 0 && fmod($cb, 1) == 0) {
            $total += intval($ca * 3 + $cb);
        }
    }

    echo sprintf('Day 13 (Part 2): %d', $total) . PHP_EOL;
});
