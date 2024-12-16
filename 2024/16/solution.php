<?php

$grid = array_map('str_split', file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES));

$rows = count($grid);
$cols = count($grid[0]);

for ($r = 0; $r < $rows; $r++) {
    for ($c = 0; $c < $cols; $c++) {
        if ($grid[$r][$c] == "S") {
            break 2;
        }
    }
}

call_user_func(function () use ($grid, $rows, $cols, $r, $c) {
    $cost = 0;
    $seen = [];

    $seen[sprintf('%d,%d,%d,%d', $r, $c, 0, 1)] = true;

    $pq = new SplPriorityQueue();
    $pq->insert([0, $r, $c, 0, 1], 0);

    while (!$pq->isEmpty()) {
        [$cost, $r, $c, $dr, $dc] = $pq->extract();

        $seen[sprintf('%d,%d,%d,%d', $r, $c, $dr, $dc)] = true;

        if ($grid[$r][$c] === 'E') {
            break;
        }

        foreach ([
            [$cost + 1, $r + $dr, $c + $dc, $dr, $dc],
            [$cost + 1000, $r, $c, $dc, -$dr],
            [$cost + 1000, $r, $c, -$dc, $dr],
        ] as [$nc, $cr, $cc, $ndr, $ndc]) {
            if ($grid[$cr][$cc] == "#" || isset($seen[sprintf('%d,%d,%d,%d', $cr, $cc, $ndr, $ndc)])) {
                continue;
            }

            $pq->insert([$nc, $cr, $cc, $ndr, $ndc], -$nc);
        }
    }

    echo sprintf('Day 16 (Part 1): %d', $cost) . PHP_EOL;
});


call_user_func(function () use ($grid, $rows, $cols, $r, $c) {
    $lc = [];
    $bt = [];
    $bc = INF;
    $es = [];

    $pq = new SplPriorityQueue();
    $pq->insert([0, $r, $c, 0, 1], 0);

    while (!$pq->isEmpty()) {
        [$cost, $r, $c, $dr, $dc] = $pq->extract();

        $lowest_key = implode(',', $cs = [$r, $c, $dr, $dc]);

        if ($cost > ($lc[$lowest_key] ?? INF)) {
            continue;
        }

        if ($grid[$r][$c] == 'E') {
            if ($cost > $bc) {
                break;
            }

            $bc = $cost;

            $es[] = $cs;
        }

        $pm = [
            [$cost + 1, $r + $dr, $c + $dc, $dr, $dc],
            [$cost + 1000, $r, $c, $dc, -$dr],
            [$cost + 1000, $r, $c, -$dc, $dr]
        ];

        foreach ($pm as $ns) {
            [$nc, $cr, $cc, $ndr, $ndc] = $ns;

            if (!isset($grid[$cr][$cc]) || $grid[$cr][$cc] === '#') {
                continue;
            }

            $nsk = implode(',', [$cr, $cc, $ndr, $ndc]);

            if ($nc > ($lc[$nsk] ?? INF)) {
                continue;
            }

            if ($nc < ($lc[$nsk] ?? INF)) {
                $bt[$nsk] = [];

                $lc[$nsk] = $nc;
            }

            $bt[$nsk][] = $cs;

            $pq->insert([$nc, $cr, $cc, $ndr, $ndc], -$nc);
        }
    }

    $seen = [];
    $s = new SplQueue();

    foreach ($es as $state) {
        $s->enqueue($state);

        $seen[] = implode(',', $state);
    }

    while (!$s->isEmpty()) {
        $k = implode(',', $s->dequeue());

        foreach ($bt[$k] ?? [] as $last) {
            if (in_array($lk = implode(',', $last), $seen)) {
                continue;
            }

            $seen[] = $lk;
            $s->enqueue($last);
        }
    }

    $count = count(array_unique(array_map(fn (string $state) => implode(',', array_slice(explode(',', $state), 0, 2)), $seen)));

    echo sprintf('Day 16 (Part 2): %d', $count) . PHP_EOL;
});