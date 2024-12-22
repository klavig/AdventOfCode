<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

function computeSeqs(array $keypad): array
{
    $positions = [];
    for ($r = 0; $r < count($keypad); $r++) {
        for ($c = 0; $c < count($keypad[$r]); $c++) {
            if ($keypad[$r][$c] !== null) {
                $positions[$keypad[$r][$c]] = [$r, $c];
            }
        }
    }
    $seqs = [];

    foreach ($positions as $x => $start) {
        foreach ($positions as $y => $end) {
            if ($x === $y) {
                $seqs["$x,$y"] = ['A'];

                continue;
            }

            $possibilities = [];
            $optimal       = INF;

            $q = new SplQueue();
            $q->enqueue([$start, '']);

            while (!$q->isEmpty()) {
                [[$r, $c], $moves] = $q->dequeue();

                $neighbors = [[$r - 1, $c, '^'], [$r + 1, $c, 'v'], [$r, $c - 1, '<'], [$r, $c + 1, '>']];

                foreach ($neighbors as [$nr, $nc, $nm]) {
                    if (
                        $nr < 0 ||
                        $nc < 0 ||
                        $nr >= count($keypad) ||
                        $nc >= count($keypad[0]) ||
                        !isset($keypad[$nr][$nc])
                    ) {
                        continue;
                    }

                    if ($keypad[$nr][$nc] === $y) {
                        if ($optimal < strlen($moves) + 1) {
                            break 2;
                        }

                        $optimal = strlen($moves) + 1;

                        $possibilities[] = $moves . $nm . 'A';
                    } else {
                        $q->enqueue([[$nr, $nc], $moves . $nm]);
                    }
                }
            }

            $seqs["$x,$y"] = $possibilities;
        }
    }

    return $seqs;
}

function product(array $arrays): array
{
    $product = $arrays[0];

    for ($i = 1; $i < count($arrays); $i++) {
        $new = [];

        foreach ($product as $p) {
            foreach ($arrays[$i] as $el) {
                $new[] = $p . $el;
            }
        }

        $product = $new;
    }

    return $product;
}

function solve(string $string, array $seqs): array
{
    $options = [];

    foreach (zip("A" . $string, $string) as [$x, $y]) {
        $options[] = $seqs["$x,$y"];
    }

    return product($options);
}

function zip(...$strings): array
{
    $result = [];
    $length = min(array_map('strlen', $strings));

    for ($i = 0; $i < $length; $i++) {
        $result[] = array_map(fn ($string) => $string[$i], $strings);
    }

    return $result;
}

function computeLength(array $arrowSeqs, array $arrowLengths, string $seq, int $depth = 2): int
{
    static $cache = [];

    if ($depth == 1) {
        return array_sum(array_map(fn (array $xy) => $arrowLengths[implode(',', $xy)], zip('A' . $seq, $seq)));
    }

    if (isset($cache[$seq][$depth])) {
        return $cache[$seq][$depth];
    }

    $length = 0;
    foreach (zip('A' . $seq, $seq) as $xy) {
        [$x, $y] = $xy;

        $length += min(array_map(
            fn ($subseq) => computeLength($arrowSeqs, $arrowLengths, $subseq, $depth - 1),
            $arrowSeqs["$x,$y"]
        ));
    }

    $cache[$seq][$depth] = $length;

    return $length;
}

$keypad = [[7, 8, 9], [4, 5, 6], [1, 2, 3], [null, 0, 'A']];
$arrows = [[null, '^', 'A'], ['<', 'v', '>']];

$keypadSeqs = computeSeqs($keypad);
$arrowSeqs  = computeSeqs($arrows);

$arrowLengths = array_map(fn ($value) => strlen($value[0]), $arrowSeqs);

call_user_func(function () use ($lines, $keypadSeqs, $arrowSeqs, $arrowLengths) {
    $result = 0;

    foreach ($lines as $line) {
        $seqs   = solve($line, $keypadSeqs);
        $length = min(array_map(fn (string $seq) => computeLength($arrowSeqs, $arrowLengths, $seq), $seqs));

        $result += $length * intval(substr($line, 0, -1));
    }

    echo sprintf('Day 21 (Part 1): %s', $result) . PHP_EOL;
});

call_user_func(function () use ($lines, $keypadSeqs, $arrowSeqs, $arrowLengths) {
    $result = 0;

    foreach ($lines as $line) {
        $seqs   = solve($line, $keypadSeqs);
        $length = min(array_map(fn (string $seq) => computeLength($arrowSeqs, $arrowLengths, $seq, 25), $seqs));

        $result += $length * intval(substr($line, 0, -1));
    }

    echo sprintf('Day 21 (Part 2): %s', $result) . PHP_EOL;
});