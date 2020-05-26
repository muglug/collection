<?php

declare(strict_types=1);

namespace loophp\collection\Iterator;

use Generator;
use Iterator;

/**
 * Class IterableIterator.
 *
 * @implements Iterator<mixed>
 */
final class IterableIterator implements Iterator
{
    /**
     * @var \loophp\collection\Iterator\ClosureIterator<mixed>
     */
    private $iterator;

    /**
     * IterableIterator constructor.
     *
     * @param iterable<mixed> $iterable
     */
    public function __construct(iterable $iterable)
    {
        $this->iterator = new ClosureIterator(
            static function () use ($iterable): Generator {
                foreach ($iterable as $key => $value) {
                    yield $key => $value;
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     *
     * @return int|string
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * {@inheritdoc}
     *
     * @return \loophp\collection\Iterator\IterableIterator
     */
    public function next()
    {
        $this->iterator->next();

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return \loophp\collection\Iterator\IterableIterator
     */
    public function rewind()
    {
        $this->iterator->rewind();

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function valid()
    {
        return $this->iterator->valid();
    }
}