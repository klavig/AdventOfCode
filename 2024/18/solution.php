<?php

$lines  = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);
$coords = array_map(fn (string $line) => array_map('intval', explode(",", trim($line))), $lines);

function connected(array $coords, int $s, int $n, bool $distance = true): int|bool {
    $grid = array_fill(0, $s + 1, array_fill(0, $s + 1, 0));

    foreach (array_slice($coords, 0, $n) as [$c, $r]) {
        $grid[$r][$c] = 1;
    }

    $q = new SplQueue();
    $q->enqueue([0, 0, 0]);

    $seen = [json_encode([0, 0]) => true];

    while (!$q->isEmpty()) {
        [$r, $c, $d] = $q->dequeue();

        foreach ([[$r + 1, $c], [$r, $c + 1], [$r - 1, $c], [$r, $c - 1]] as [$nr, $nc]) {
            if ($nr < 0 || $nc < 0 || $nr > $s || $nc > $s || $grid[$nr][$nc] === 1 || isset($seen[json_encode([$nr, $nc])])) {
                continue;
            }

            if ($nr === $nc && $nr === $s) {
                if ($distance) {
                    return $d + 1;
                }

                return true;
            }

            $seen[json_encode([$nr, $nc])] = true;

            $q->enqueue([$nr, $nc, $d + 1]);
        }
    }

    if ($distance) {
        return 0;
    }

    return false;
}

call_user_func(function () use ($coords) {
    $s = 70;
    $n = 1024;
    $result = connected($coords, $s, $n);

    echo sprintf('Day 18 (Part 1): %s', $result) . PHP_EOL;
});

call_user_func(function () use ($coords) {
    $s = 70;
    $lo = 0;
    $hi = count($coords) - 1;

    while ($lo < $hi) {
        $mi = ($lo + $hi) / 2;
        if (connected($coords, $s, $mi + 1, false)) {
            $lo = $mi + 1;
        } else {
            $hi = $mi;
        }
    }

    $result = implode(',', $coords[$lo]);

    echo sprintf('Day 18 (Part 2): %s', $result) . PHP_EOL;
});