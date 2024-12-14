<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

const WIDTH = 101;
const HEIGHT = 103;

$robots = [];
foreach ($lines as $line) {
    sscanf($line, 'p=%d,%d v=%d,%d', $px, $py, $vx, $vy);
    $robots[] = [$px, $py, $vx, $vy];
}

function mod(float|int $a, float|int $b): float|int
{
    return $a - ($b * floor($a / $b));
}

function total(array $robots, int $seconds): int
{
    $result = [];
    foreach ($robots as [$px, $py, $vx, $vy]) {
        $result[] = [mod($px + $vx * $seconds, WIDTH), mod($py + $vy * $seconds, HEIGHT)];
    }

    $tl = $bl = $tr = $br = 0;

    $vm = floor((HEIGHT - 1) / 2);
    $hm = floor((WIDTH - 1) / 2);

    foreach ($result as [$px, $py]) {
        if ($px === $hm || $py === $vm) {
            continue;
        }
        if ($px < $hm) {
            if ($py < $vm) {
                $tl++;
            } else {
                $bl++;
            }
        } else {
            if ($py < $vm) {
                $tr++;
            } else {
                $br++;
            }
        }
    }

    return $tl * $bl * $tr * $br;
}

call_user_func(function () use ($robots) {
    $total = total($robots, 100);

    echo sprintf('Day 14 (Part 1): %d', $total) . PHP_EOL;
});

call_user_func(function () use ($robots) {
    $best = 0;
    $min = PHP_FLOAT_MAX;

    for ($seconds = 0; $seconds < WIDTH * HEIGHT; $seconds++) {
        if (($total = total($robots, $seconds)) < $min) {
            $min  = $total;
            $best = $seconds;
        }
    }

    echo sprintf('Day 14 (Part 2): %d', $best) . PHP_EOL;
});
