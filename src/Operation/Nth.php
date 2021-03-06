<?php

declare(strict_types=1);

namespace loophp\collection\Operation;

use Closure;
use Generator;
use Iterator;

/**
 * @psalm-template TKey
 * @psalm-template TKey of array-key
 * @psalm-template T
 */
final class Nth extends AbstractOperation
{
    /**
     * @psalm-return Closure(int): Closure(int): Closure(Iterator<TKey, T>): Generator<TKey, T>
     */
    public function __invoke(): Closure
    {
        return static function (int $step): Closure {
            return static function (int $offset) use ($step): Closure {
                return
                    /**
                     * @psalm-param Iterator<TKey, T> $iterator
                     * @psalm-return Generator<TKey, T>
                     */
                    static function (Iterator $iterator) use ($step, $offset): Generator {
                        $position = 0;

                        foreach ($iterator as $key => $value) {
                            if ($position++ % $step !== $offset) {
                                continue;
                            }

                            yield $key => $value;
                        }
                    };
            };
        };
    }
}
