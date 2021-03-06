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
final class Keys extends AbstractOperation
{
    public function __invoke(): Closure
    {
        return
            /**
             * @psalm-param Iterator<TKey, T> $iterator
             *
             * @psalm-return Generator<int, TKey>
             */
            static function (Iterator $iterator): Generator {
                foreach ($iterator as $key => $value) {
                    yield $key;
                }
            };
    }
}
