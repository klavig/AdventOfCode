<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);
$grid  = array_map(fn (string $line) => str_split(trim($line)), $lines);
$rows  = count($grid);
$cols  = count($grid[0]);

$regions = $seen = [];

for ($r = 0; $r < $rows; $r++) {
    for ($c = 0; $c < $cols; $c++) {
        if (in_array([$r, $c], $seen)) {
            continue;
        }
        $region = [[$r, $c]];
        $seen[] = $region[0];
        $q = new SplQueue();
        $q->enqueue($region[0]);
        $crop = $grid[$r][$c];
        while (!$q->isEmpty()) {
            [$cr, $cc] = $q->dequeue();
            foreach (neighbours($cr, $cc) as [$nr, $nc]) {
                if ($nr < 0 || $nc < 0 || $nr >= $rows || $nc >= $cols || $grid[$nr][$nc] != $crop || in_array([$nr, $nc], $region)) {
                    continue;
                }
                $q->enqueue($region[] = [$nr, $nc]);
            }
        }
        foreach ($region as [$rr, $rc]) {
            if (!in_array([$rr, $rc], $seen)) {
                $seen[] = [$rr, $rc];
            }
        }
        $regions[] = $region;
    }
}

function neighbours(int $r, int $c): array
{
    return [[$r + 1, $c], [$r - 1, $c], [$r, $c - 1], [$r, $c + 1]];
}

function perimeter(array $region): int
{
    $output = 0;
    foreach ($region as [$r, $c]) {
        $output += 4;
        foreach (neighbours($r, $c) as [$nr, $nc]) {
            if (in_array([$nr, $nc], $region)) {
                $output -= 1;
            }
        }
    }
    return $output;
}

function sides(array $region): int
{
    $candidates = [];
    foreach ($region as [$r, $c]) {
        foreach ([[$r - 0.5, $c - 0.5], [$r + 0.5, $c - 0.5], [$r + 0.5, $c + 0.5], [$r - 0.5, $c + 0.5]] as [$cr, $cc]) {
            if (in_array([$cr, $cc], $candidates)) {
                continue;
            }
            $candidates[] = [$cr, $cc];
        }
    }
    $corners = 0;
    foreach ($candidates as [$cr, $cc]) {
        $config = [
            in_array([$cr - 0.5, $cc - 0.5], $region),
            in_array([$cr + 0.5, $cc - 0.5], $region),
            in_array([$cr + 0.5, $cc + 0.5], $region),
            in_array([$cr - 0.5, $cc + 0.5], $region)
        ];
        $corners = match (array_sum($config)) {
            1, 3    => $corners + 1,
            2       => $corners + ($config === [true, false, true, false] || $config === [false, true, false, true] ? 2 : 0),
            default => $corners,
        };
    }
    return $corners;
}

call_user_func(function () use ($regions) {
    $total = 0;
    foreach ($regions as $region) {
        $total += count($region) * perimeter($region);
    }

    echo sprintf('Day 12 (Part 1): %d', $total) . PHP_EOL;
});

call_user_func(function () use ($regions) {
    $total = 0;
    foreach ($regions as $region) {
        $total += count($region) * sides($region);
    }

    echo sprintf('Day 12 (Part 2): %d', $total) . PHP_EOL;
});
