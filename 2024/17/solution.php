<?php

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES);

$program = '';
$a = $b = $c = 0;

foreach ($lines as $i => $line) {
    sscanf($line, 'Register A: %d', $a);
    sscanf($line, 'Register B: %d', $b);
    sscanf($line, 'Register C: %d', $c);

    if (0 === strlen($line)) {
        sscanf($lines[$i + 1], 'Program: %s', $program);
    }
}

$program = array_map('intval', explode(',', $program));

call_user_func(function () use ($a, $b, $c, $program) {
    $pointer = 0;
    $output = [];

    $combo = function(int $op) use (&$a, &$b, &$c): int {
        return match ($op) {
            0, 1, 2, 3 => $op,
            4          => $a,
            5          => $b,
            6          => $c,
            default    => die("unrecognized combo operand $op"),
        };
    };

    while ($pointer < count($program)) {
        $ins = $program[$pointer];
        $op = $program[$pointer + 1];

        switch ($ins) {
            case 0: // adv
                $a = $a >> $combo($op);
                break;
            case 1: // bxl
                $b ^= $op;
                break;
            case 2: // bst
                $b = $combo($op) % 8;
                break;
            case 3: // jnz
                if ($a != 0) {
                    $pointer = $op;
                    continue 2;
                }
                break;
            case 4: // bxc
                $b ^= $c;
                break;
            case 5: // out
                $output[] = $combo($op) % 8;
                break;
            case 6: // bdv
                $b = $a >> $combo($op);
                break;
            case 7: // cdv
                $c = $a >> $combo($op);
                break;
        }

        $pointer += 2;
    }

    $total = implode(',', $output);

    echo sprintf('Day 17 (Part 1): %s', $total) . PHP_EOL;
});

function find(array $target, int $ans): ?int {
    global $program;

    if (empty($target)) {
        return $ans;
    }

    for ($t = 0; $t < 8; $t++) {
        $a = ($ans << 3) | $t;
        $b = 0;
        $c = 0;
        $output = null;
        $adv3 = false;

        $combo = function (int $op) use (&$a, &$b, &$c): int {
            return match ($op) {
                0, 1, 2, 3 => $op,
                4          => $a,
                5          => $b,
                6          => $c,
                default    => die("unrecognized combo operand $op"),
            };
        };

        for ($pointer = 0; $pointer < count($program) - 2; $pointer += 2) {
            $ins = $program[$pointer];
            $op = $program[$pointer + 1];

            switch ($ins) {
                case 0: // adv
                    if (true === $adv3) {
                        die("program has multiple ADVs");
                    }
                    if ($op !== 3) {
                        die("program has ADV with operand other than 3");
                    }
                    $adv3 = true;
                    break;
                case 1: // bxl
                    $b ^= $op;
                    break;
                case 2: // bst
                    $b = $combo($op) % 8;
                    break;
                case 3: // jnz
                    die("program has JNZ inside expected loop body");
                case 4: // bxc
                    $b ^= $c;
                    break;
                case 5: // out
                    if (!is_null($output)) {
                        die("program has multiple OUT");
                    }
                    $output = $combo($op) % 8;
                    break;
                case 6: // bdv
                    $b = $a >> $combo($op);
                    break;
                case 7: // cdv
                    $c = $a >> $combo($op);
                    break;
            }

            if ($output === $target[count($target) - 1]) {
                $sub = find(array_slice($target, 0, -1), $a);
                if ($sub === null) continue;
                return $sub;
            }
        }
    }
    return null;
}

call_user_func(function () use ($program) {
    $total = find($program, 0);

    echo sprintf('Day 17 (Part 2): %d', $total) . PHP_EOL;
});