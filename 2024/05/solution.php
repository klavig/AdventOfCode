<?php

class PrintQueue
{
    private array $rules = [];
    private array $queues = [];

    private function readInput(): void
    {
        if ($this->rules && $this->queues) {
            return;
        }

        foreach (file(__DIR__ . '/input.txt') as $line) {
            if ("\r\n" === $line) {
                continue;
            }

            if (str_contains($line, '|')) {
                [$x, $y] = array_map('intval', explode('|', $line));

                $this->rules[$x][] = $y;

                continue;
            }

            $this->queues[] = array_map('intval', explode(',', $line));
        }
    }

    private function isQueueInCorrectOrder(array $queue): bool
    {
        $count = count($queue);

        for ($i = 0; $i < $count - 1; $i++) {
            $pageNumber = $queue[$i];

            if (
                !isset($this->rules[$pageNumber]) ||
                (isset($queue[$i + 1]) && !in_array($queue[$i + 1], $this->rules[$pageNumber]))
            ) {
                return false;
            }

            if (
                isset($queue[$i - 1]) && (
                    !isset($this->rules[$queue[$i - 1]]) ||
                    !in_array($queue[$i + 1], $this->rules[$queue[$i - 1]])
                )
            ) {
                return false;
            }
        }

        return true;
    }

    public function addUpCorrectlyOrderedPageNumbers(): void
    {
        $this->readInput();

        $total = 0;

        foreach ($this->queues as $queue) {
            if ($this->isQueueInCorrectOrder($queue)) {
                $total += $queue[floor(count($queue) / 2)];
            }
        }

        echo sprintf('Day 5 (Part 1): %d', $total) . PHP_EOL;
    }

    public function addUpIncorrectlyOrderedPageNumbers(): void
    {
        $this->readInput();

        $total = 0;

        foreach ($this->queues as $queue) {
            if (!$this->isQueueInCorrectOrder($queue)) {
                usort($queue, function (int $a, int $b) {
                    return in_array($a, $this->rules[$b] ?? []) ? 1 : -1;
                });

                $total += $queue[floor(count($queue) / 2)];
            }
        }

        echo sprintf('Day 5 (Part 2): %d', $total) . PHP_EOL;
    }
}

$solution = new PrintQueue();
$solution->addUpCorrectlyOrderedPageNumbers();
$solution->addUpIncorrectlyOrderedPageNumbers();