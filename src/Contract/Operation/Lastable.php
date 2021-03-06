<?php

declare(strict_types=1);

namespace loophp\collection\Contract\Operation;

use loophp\collection\Contract\Collection;

/**
 * @psalm-template TKey
 * @psalm-template TKey of array-key
 * @psalm-template T
 */
interface Lastable
{
    /**
     * Get the last item from the collection passing the given truth test.
     *
     * @psalm-param null|callable(T, TKey):(bool) $callback
     *
     * @psalm-return \loophp\collection\Contract\Collection<TKey, T>
     */
    public function last(?callable $callback = null, int $size = 1): Collection;
}
