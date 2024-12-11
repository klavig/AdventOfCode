<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

call_user_func(function (array $lines) {
    $disk = [];
    $fid = 0;

    foreach (str_split($lines[0]) as $i => $char) {
        $x = intval($char);
        if ($i % 2 === 0) {
            $disk = array_merge($disk, ...array_fill(0, $x, [$fid]));
            $fid++;
        } else {
            $disk = array_merge($disk, ...array_fill(0, $x, [-1]));
        }
    }

    $blanks = [];
    foreach ($disk as $i => $x) {
        if ($x === -1) {
            $blanks[] = $i;
        }
    }

    foreach ($blanks as $i) {
        while (end($disk) === -1) {
            array_pop($disk);
        }
        if (count($disk) <= $i) {
            break;
        }
        $disk[$i] = array_pop($disk);
    }

    $result = 0;
    foreach ($disk as $i => $x) {
        $result += $i * $x;
    }

    echo sprintf('Day 9 (Part 1): %d', $result) . PHP_EOL;
}, $lines);

call_user_func(function (array $lines) {
    $files = [];
    $blanks = [];

    $fid = 0;
    $pos = 0;

    foreach (str_split($lines[0]) as $i => $char) {
        $x = intval($char);
        if ($i % 2 === 0) {
            if ($x === 0) {
                continue;
            }
            $files[$fid] = [$pos, $x];
            $fid++;
        } else {
            if ($x !== 0) {
                $blanks[] = [$pos, $x];
            }
        }
        $pos += $x;
    }

    while ($fid > 0) {
        $fid--;
        list($pos, $size) = $files[$fid];
        foreach ($blanks as $i => [$start, $length]) {
            if ($start >= $pos) {
                $blanks = array_slice($blanks, 0, $i);
                break;
            }
            if ($size <= $length) {
                $files[$fid] = [$start, $size];
                if ($size === $length) {
                    unset($blanks[$i]);
                } else {
                    $blanks[$i] = [$start + $size, $length - $size];
                }
                break;
            }
        }
    }

    $result = 0;
    foreach ($files as $fid => [$pos, $size]) {
        for ($x = $pos; $x < $pos + $size; $x++) {
            $result += $fid * $x;
        }
    }

    echo sprintf('Day 9 (Part 2): %d', $result) . PHP_EOL;
}, $lines);