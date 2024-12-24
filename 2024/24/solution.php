<?php

declare(strict_types=1);

if (version_compare(PHP_VERSION, '8.4.0', '<')) {
    die(sprintf('This solution requires PHP 8.4.0 or higher. You are using PHP %s.', PHP_VERSION));
}

$lines = file(__DIR__ . '/input.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$wires = $gates = [];

foreach ($lines as $line) {
    if (str_contains($line, ':')) {
        [$wire, $value] = explode(':', $line);

        $wires[trim($wire)] = (int)trim($value);
    } elseif (preg_match('/(\w+) (AND|OR|XOR) (\w+) -> (\w+)/', $line, $matches)) {
        [, $input1, $operation, $input2, $output] = $matches;

        $gates[$output] = [$input1, $operation, $input2];
    }
}

while (true) {
    $progress = false;

    foreach ($gates as $output => [$input1, $operation, $input2]) {
        if (isset($wires[$output])) {
            continue;
        }

        if (isset($wires[$input1]) && isset($wires[$input2])) {
            $value1 = $wires[$input1];
            $value2 = $wires[$input2];

            $wires[$output] = computeGate($value1, $value2, $operation);

            $progress = true;
        }
    }

    if (!$progress) {
        break;
    }
}

function computeGate(int $input1, int $input2, string $operation): int
{
    return match ($operation) {
        'AND'   => $input1 & $input2,
        'OR'    => $input1 | $input2,
        'XOR'   => $input1 ^ $input2,
        default => die(sprintf('Unknown operation: %s', $operation)),
    };
}

function validXorInputs(string $output, string $input1, string $input2): bool
{
    $prefixes = ['x', 'y', 'z'];

    return startsWithAny($output, $prefixes) || startsWithAny($input1, $prefixes) ||
        startsWithAny($input2, $prefixes);
}

function invalidAndInputs(array $gates, string $output, string $input1, string $input2): bool
{
    if ($input1 === 'x00' || $input2 === 'x00') {
        return false;
    }

    return array_any($gates, fn (array $gate): bool => ($output === $gate[0] || $output === $gate[2]) && $gate[1] !== 'OR');

}

function xorFeedsOr(array $gates, string $output): bool
{
    return array_any($gates, fn (array $gate): bool => ($output === $gate[0] || $output === $gate[2]) && $gate[1] === 'OR');
}

function startsWithAny(string $string, array $prefixes): bool
{
    return array_any($prefixes, fn (string $prefix): bool => str_starts_with($string, $prefix));
}

call_user_func(function () use (&$wires) {
    ksort($wires);

    $binary = '';

    foreach ($wires as $wire => $value) {
        if (str_starts_with($wire, 'z')) {
            $binary = $value . $binary;
        }
    }

    $result = bindec($binary);

    echo sprintf('Day 24 (Part 1): %d', $result) . PHP_EOL;
});

call_user_func(function () use ($gates) {
    $wrong = [];
    $highestZ = max(array_filter(array_keys($gates), fn (string $key): bool => str_starts_with($key, 'z')));

    foreach ($gates as $output => [$input1, $operation, $input2]) {
        if (
            (str_starts_with($output, 'z') && $operation !== 'XOR' && $output !== $highestZ) ||
            ($operation === 'XOR' && !validXorInputs($output, $input1, $input2)) ||
            ($operation === 'AND' && invalidAndInputs($gates, $output, $input1, $input2)) ||
            ($operation === 'XOR' && xorFeedsOr($gates, $output))
        ) {
            $wrong[] = $output;
        }
    }

    sort($wrong);

    $result = implode(',', array_unique($wrong));

    echo sprintf('Day 24 (Part 2): %s', $result) . PHP_EOL;
});