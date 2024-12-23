<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);
$edges = array_map(fn (string $line) => explode('-', $line), $lines);

$conns = [];
foreach ($edges as [$x, $y]) {
    if (!isset($conns[$x])) {
        $conns[$x] = [];
    }
    if (!isset($conns[$y])) {
        $conns[$y] = [];
    }
    if (!in_array($y, $conns[$x])) {
        $conns[$x][] = $y;
    }
    if (!in_array($x, $conns[$y])) {
        $conns[$y][] = $x;
    }
}

function connected(array $cns): bool
{
    global $conns;

    foreach ($cns as $a) {
        foreach ($cns as $b) {
            if ($a !== $b && !in_array($b, $conns[$a] ?? [], true)) {
                return false;
            }
        }
    }
    return true;
}

function search(array $cns, array $current, int $depth, callable $callback): void
{
    foreach ($cns as $cn) {
        $set = array_merge($current, [$cn]);

        if (connected($set)) {
            $callback($set);
            $newCns = array_filter($cns, fn (string $c) => $c > $cn);
            search($newCns, $set, $depth + 1, $callback);
        }
    }
}

call_user_func(function () use ($conns) {
    $result = 0;

    foreach ($conns as $x => $connsX) {
        foreach ($connsX as $y) {
            foreach ($conns[$y] as $z) {
                if (
                    $x !== $z &&
                    in_array($x, $conns[$z]) &&
                    count(array_filter([$x, $y, $z], fn (string $cn) => str_starts_with($cn, 't'))) > 0
                ) {
                    $result++;
                }
            }
        }
    }

    $result /= 6;

    echo sprintf('Day 23 (Part 1): %d', $result) . PHP_EOL;
});

call_user_func(function () use ($conns) {

    $maxSet = [];

    search(array_keys($conns), [], 0, function (array $set) use (&$maxSet): void {
        if (count($set) > count($maxSet)) {
            $maxSet = $set;
        }
    });

    sort($maxSet);
    $result = implode(',', $maxSet);

    echo sprintf('Day 23 (Part 2): %s', $result) . PHP_EOL;
});