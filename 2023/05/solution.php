<?php

$input = file_get_contents(__DIR__ . '/input.txt');
$blocks = explode("\r\n\r\n", $input);

$seeds = explode(":", $blocks[0])[1];
$seeds = array_map('intval', explode(' ', trim($seeds)));

array_shift($blocks);

foreach ($blocks as $i => $block) {
    $ranges = [];
    $lines  = explode("\r\n", trim($block));
    array_shift($lines);

    foreach ($lines as $line) {
        $ranges[] = array_map('intval', explode(' ', trim($line)));
    }

    $blocks[$i] = $ranges;
}

call_user_func(function (array $seeds) use ($blocks) {
    foreach ($blocks as $ranges) {
        $new = [];
        foreach ($seeds as $seed) {
            $found = false;
            foreach ($ranges as $range) {
                [$destRangeStart, $sourceRangeStart, $rangeLength] = $range;
                if ($sourceRangeStart <= $seed && $seed < $sourceRangeStart + $rangeLength) {
                    $new[] = $seed - $sourceRangeStart + $destRangeStart;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $new[] = $seed;
            }
        }
        $seeds = $new;
    }

    $result = min($seeds);

    echo sprintf('Day 5 (Part 1): %d', $result) . PHP_EOL;
}, $seeds);


$seeds = array_chunk($seeds, 2);
foreach ($seeds as $i => [$seedRangeStart, $seedRangeLength]) {
    $seeds[$i] = [$seedRangeStart, $seedRangeStart + $seedRangeLength];
}

call_user_func(function () use ($blocks, $seeds) {
    foreach ($blocks as $ranges) {
        $new = [];
        while (count($seeds) > 0) {
            [$seedRangeStart, $seedRangeEnd] = array_pop($seeds);
            $found = false;

            foreach ($ranges as [$destRangeStart, $sourceRangeStart, $rangeLength]) {
                $maxRangeStart = max($seedRangeStart, $sourceRangeStart);
                $minRangeEnd   = min($seedRangeEnd, $sourceRangeStart + $rangeLength);

                if ($maxRangeStart < $minRangeEnd) {
                    $new[] = [$maxRangeStart - $sourceRangeStart + $destRangeStart, $minRangeEnd - $sourceRangeStart + $destRangeStart];
                    if ($maxRangeStart > $seedRangeStart) {
                        $seeds[] = [$seedRangeStart, $maxRangeStart];
                    }
                    if ($seedRangeEnd > $minRangeEnd) {
                        $seeds[] = [$minRangeEnd, $seedRangeEnd];
                    }
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $new[] = [$seedRangeStart, $seedRangeEnd];
            }
        }

        $seeds = $new;
    }

    $result = min(array_merge(...$seeds));

    echo sprintf('Day 5 (Part 2): %d', $result) . PHP_EOL;
});