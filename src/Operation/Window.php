<?php

declare(strict_types=1);

namespace loophp\collection\Operation;

use ArrayIterator;
use Closure;
use Generator;
use Iterator;

/**
 * @psalm-template TKey
 * @psalm-template TKey of array-key
 * @psalm-template T
 */
final class Window extends AbstractOperation
{
    /**
     * @psalm-return Closure(int...): Closure(Iterator<TKey, T>): Generator<int, list<T>>
     */
    public function __invoke(): Closure
    {
        return static function (int ...$lengths): Closure {
            return
                /**
                 * @psalm-param Iterator<TKey, T> $iterator
                 * @psalm-param ArrayIterator<int, int> $length
                 *
                 * @psalm-return Generator<int, list<T>>
                 */
                static function (Iterator $iterator) use ($lengths): Generator {
                    /** @psalm-var Iterator<int, int> $lengths */
                    $lengths = Cycle::of()(new ArrayIterator($lengths));

                    for ($i = 0; iterator_count($iterator) > $i; ++$i) {
                        /** @psalm-var Generator<TKey, T> $slice */
                        $slice = Slice::of()($i)($lengths->current())($iterator);

                        yield iterator_to_array($slice);

                        $lengths->next();
                    }
                };
        };
    }
}
