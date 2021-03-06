API
===

Static constructors
-------------------

empty
~~~~~

Create an empty Collection.

.. code-block:: php

    $collection = Collection::empty();

fromCallable
~~~~~~~~~~~~

Create a collection from a callable.

.. code-block:: php

    $callback = static function () {
        yield 'a';
        yield 'b';
        yield 'c';
    };

    $collection = Collection::fromCallable($callback);

fromIterable
~~~~~~~~~~~~

Create a collection from an iterable.

.. code-block:: php

    $collection = Collection::fromIterable(['a', 'b', 'c']);

fromResource
~~~~~~~~~~~~

Create a collection from a resource.

.. code-block:: php

    $stream = fopen('data://text/plain,' . $string, 'rb');

    $collection = Collection::fromResource($stream);

fromString
~~~~~~~~~~

Create a collection from a string.

.. code-block:: php

    $data = file_get_contents('http://loripsum.net/api');

    $collection = Collection::fromString($data);

iterate
~~~~~~~

Iterate over a callback and use the callback results to build a collection.

Signature: ``Collection::iterate(callable $callback, ...$parameters);``

.. warning:: The callback return values are reused as callback arguments at the next callback call.

.. warning:: When the callback return is an array, only the first value is yielded.

.. code-block:: php

    $fibonacci = static function ($a = 0, $b = 1): array {
        return [$b, $a + $b];
    };

    Collection::iterate($fibonacci)
        ->limit(10); // [1, 1, 2, 3, 5, 8, 13, 21, 34, 55]

Another example

.. code-block:: php

    $even = Collection::iterate(static function ($carry) {return $carry + 2;}, -2);
    $odd = Collection::iterate(static function ($carry) {return $carry + 2;}, -1);
    // Is the same as
    $even = Collection::range(0, \INF, 2);
    $odd = Collection::range(1, \INF, 2);

range
~~~~~

Build a collection from a range of values.

Signature: ``Collection::range(int $start = 0, $end = INF, $step = 1);``

.. code-block:: php

    $fibonacci = static function ($a = 0, $b = 1): array {
        return [$b, $a + $b];
    };

    $even = Collection::range(0, 20, 2); // [0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20]

Another example

.. code-block:: php

    $even = Collection::iterate(static function ($carry) {return $carry + 2;}, -2);
    $odd = Collection::iterate(static function ($carry) {return $carry + 2;}, -1);
    // Is the same as
    $even = Collection::range(0, \INF, 2);
    $odd = Collection::range(1, \INF, 2);

times
~~~~~

Create a collection by invoking a callback a given amount of times.

If no callback is provided, then it will create a simple list of incremented integers.

Signature: ``Collection::times($number = INF, ?callable $callback = null);``

.. code-block:: php

    $collection = Collection::times(10);

unfold
~~~~~~

Create a collection by yielding from a callback with a initial value.

Signature: ``Collection::unfold($init, callable $callback);``

.. code-block:: php

    // A list of Naturals from 1 to Infinity.
    $collection = Collection::unfold(1, fn($n) => $n + 1)
        ->normalize();

with
~~~~

Create a collection with the provided data.

Signature: ``Collection::with($data = [], ...$parameters);``

.. code-block:: php

    // With an iterable
    $collection = Collection::with(['a', 'b']);

    // With a string
    $collection = Collection::with('string');

    $callback = static function () {
        yield 'a';
        yield 'b';
        yield 'c';
    };

    // With a callback
    $collection = Collection::with($callback);

    // With a resource/stream
    $collection = Collection::with(fopen( __DIR__ . '/vendor/autoload.php', 'r'));

Methods (operations)
--------------------

Operations always returns a new collection object.

all
~~~

Interface: `Allable`_

append
~~~~~~

Add one or more items to a collection.

Interface: `Appendable`_

Signature: ``Collection::append(...$items);``

.. code-block:: php

    $collection = Collection::with(['1', '2', '3']);

    $collection
        ->append('4')
        ->append('5', '6');

apply
~~~~~

Execute a callback for each element of the collection without
altering the collection item itself.

If the callback does not return `true` then it stops.

Interface: `Applyable`_

Signature: ``Collection::apply(...$callbacks);``

.. code-block:: php

    $callback = static function ($value, $key): bool
        {
            var_dump('Value is: ' . $value . ', key is: ' . $key);

            return true;
        };

    $collection = Collection::with(['1', '2', '3']);

    $collection
        ->apply($callback);

associate
~~~~~~~~~

Transform keys and values of the collection independently and combine them.

Interface: `Associateable`_

Signature: ``Collection::associate(?callable $callbackForKeys = null, ?callable $callbackForValues = null);``

.. code-block:: php

    $input = range(1, 10);

    Collection::fromIterable($input)
        ->associate(
            static function ($key, $value) {
                return $key * 2;
            },
            static function ($key, $value) {
                return $value * 2;
            }
        );

    // [
    //   0 => 2,
    //   2 => 4,
    //   4 => 6,
    //   6 => 8,
    //   8 => 10,
    //   10 => 12,
    //   12 => 14,
    //   14 => 16,
    //   16 => 18,
    //   18 => 20,
    // ]

cache
~~~~~

Useful when using a resource as input and you need to run through the collection multiple times.

Interface: `Cacheable`_

Signature: ``Collection::cache(CacheItemPoolInterface $cache = null);``

.. code-block:: php

    $fopen = fopen(__DIR__ . '/vendor/autoload.php', 'r');

    $collection = Collection::withResource($fopen)
        ->cache();

chunk
~~~~~

Chunk a collection of item into chunks of items of a given size.

Interface: `Chunkable`_

Signature: ``Collection::chunk(int $size);``

.. code-block:: php

    $collection = Collection::with(range(0, 10));

    $collection->chunk(2);

collapse
~~~~~~~~

Collapse a collection of items into a simple flat collection.

Interface: `Collapseable`_

Signature: ``Collection::collapse();``

.. code-block:: php

    $collection = Collection::with([[1,2], [3, 4]]);

    $collection->collapse();

column
~~~~~~

Return the values from a single column in the input iterables.

Interface: `Columnable`_

Signature: ``Collection::column($index);``

.. code-block:: php

    $records = [
        [
            'id' => 2135,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ],
        [
            'id' => 3245,
            'first_name' => 'Sally',
            'last_name' => 'Smith',
        ],
        [
            'id' => 5342,
            'first_name' => 'Jane',
            'last_name' => 'Jones',
        ],
        [
            'id' => 5623,
            'first_name' => 'Peter',
            'last_name' => 'Doe',
        ],
    ];

    $result = Collection::with($records)
        ->column('first_name');

combinate
~~~~~~~~~

Get all the combinations of a given length of a collection of items.

Interface: `Combinateable`_

Signature: ``Collection::combinate(?int $length);``

.. code-block:: php

    $collection = Collection::with(['a', 'b', 'c', 'd'])
        ->combinate(3);

combine
~~~~~~~

Combine a collection of items with some other keys.

Interface: `Combineable`_

Signature: ``Collection::combine(...$keys);``

.. code-block:: php

    $collection = Collection::with(['a', 'b', 'c', 'd'])
        ->combine('w', 'x', 'y', 'z')

compact
~~~~~~~

Remove given values from the collection, if no values are provided, it removes only the null value.

Interface: `Compactable`_

Signature: ``Collection::compact(...$values);``

.. code-block:: php

    $collection = Collection::with(['a', 1 => 'b', null, false, 0, 'c'];)
        ->compact(); // ['a', 1 => 'b', 3 => false, 4 => 0, 5 => 'c']

    $collection = Collection::with(['a', 1 => 'b', null, false, 0, 'c'];)
        ->compact(null, 0); // ['a', 1 => 'b', 3 => false, 5 => 'c']

contains
~~~~~~~~

Interface: `Containsable`_

cycle
~~~~~

Cycle around a collection of items.

Interface: `Cycleable`_

Signature: ``Collection::cycle(int $length = 0);``

.. code-block:: php

    $collection = Collection::with(['a', 'b', 'c', 'd'])
        ->cycle(10)

diff
~~~~

It compares the collection against another collection or a plain array based on its values.
This method will return the values in the original collection that are not present in the given collection.

Interface: `Diffable`_

Signature: ``Collection::diff(...$values);``

.. code-block:: php

    $collection = Collection::with(['a', 'b', 'c', 'd', 'e'])
        ->diff('a', 'b', 'c', 'x'); // [3 => 'd', 4 => 'e']

diffKeys
~~~~~~~~

It compares the collection against another collection or a plain object based on its keys.
This method will return the key / value pairs in the original collection that are not present in the given collection.

Interface: `Diffkeysable`_

Signature: ``Collection::diffKeys(...$values);``

.. code-block:: php

    $collection = Collection::with(['a', 'b', 'c', 'd', 'e'])
        ->diffKeys(1, 2); // [0 => 'a', 3 => 'd', 4 => 'e']

distinct
~~~~~~~~

Remove duplicated values from a collection.

Interface: `Distinctable`_

Signature: ``Collection::distinct();``

.. code-block:: php

    $collection = Collection::with(['a', 'b', 'c', 'd', 'a'])
        ->distinct()

explode
~~~~~~~

Explode a collection into subsets based on a given value.

Interface: `Explodeable`_

Signature: ``Collection::explode(...$items);``

.. code-block:: php

    $string = 'I am just a random piece of text.';

    $collection = Collection::with($string)
        ->explode('o');

falsy
~~~~~

Interface: `Falsyable`_

filter
~~~~~~

Filter collection items based on one or more callbacks.

Interface: `Filterable`_

Signature: ``Collection::filter(callable ...$callbacks);``

.. code-block:: php

    $callback = static function($value): bool {
        return 0 === $value % 3;
    };

    $collection = Collection::with(range(1, 100))
        ->filter($callback);

first
~~~~~

Get the first items from the collection passing the given truth test.

Interface: `Firstable`_

Signature: ``Collection::first(?callable $callback = null, int $size = 1);``

.. code-block:: php

        $generator = static function (): Generator {
            yield 'a' => 'a';
            yield 'b' => 'b';
            yield 'c' => 'c';
            yield 'a' => 'd';
            yield 'b' => 'e';
            yield 'c' => 'f';
        };

        Collection::fromIterable($generator())
            ->first(
                static function ($value, $key) {
                    return 'b' === $key;
                }
            ); // ['b' => 'b']

        $output = static function (): Generator {
            yield 'b' => 'b';
            yield 'b' => 'e';
        };

        Collection::fromIterable($generator())
            ->first(
                static function ($value, $key) {
                    return 'b' === $key;
                },
                2
            ); // ['b' => 'b', 'b' => 'e']

flatten
~~~~~~~

Flatten a collection of items into a simple flat collection.

Interface: `Flattenable`_

Signature: ``Collection::flatten(int $depth = PHP_INT_MAX);``

.. code-block:: php

    $collection = Collection::with([0, [1, 2], [3, [4, [5, 6]]]])
        ->flatten();

flip
~~~~

Flip keys and items in a collection.

Interface: `Flipable`_

Signature: ``Collection::flip(int $depth = PHP_INT_MAX);``

.. code-block:: php

    $collection = Collection::with(['a', 'b', 'c', 'a'])
        ->flip();

.. tip:: array_flip() and Collection::flip() can behave different, check the following examples.

When using regular arrays, `array_flip()`_ can be used to remove duplicates (dedup-licate an array).

.. code-block:: php

    $dedupArray = array_flip(array_flip(['a', 'b', 'c', 'd', 'a']));

This example will return ``['a', 'b', 'c', 'd']``.

However, when using a collection:

.. code-block:: php

    $dedupCollection = Collection::with(['a', 'b', 'c', 'd', 'a'])
        ->flip()
        ->flip()
        ->all();

This example will return ``['a', 'b', 'c', 'd', 'a']``.

foldLeft
~~~~~~~~

Interface: `FoldLeftable`_

foldRight
~~~~~~~~~

Interface: `FoldRightable`_

forget
~~~~~~

Remove items having specific keys.

Interface: `Forgetable`_

Signature: ``Collection::forget(...$keys);``

.. code-block:: php

    $collection = Collection::with(range('a', 'z'))
        ->forget(5, 6, 10, 15);

frequency
~~~~~~~~~

Calculate the frequency of the values, frequencies are stored in keys.

Values can be anything (object, scalar, ... ).

Interface: `Frequencyable`_

Signature: ``Collection::frequency();``

.. code-block:: php

    $collection = Collection::with(['a', 'b', 'c', 'b', 'c', 'c')
        ->frequency()
        ->all(); // [1 => 'a', 2 => 'b', 3 => 'c'];

get
~~~

Interface: `Getable`_


group
~~~~~

Group items, the key used to group items can be customized in a callback.
By default it's the key is the item's key.

Interface: `Groupable`_

Signature: ``Collection::group(callable $callable = null);``

.. code-block:: php

    $callback = static function () {
            yield 1 => 'a';

            yield 1 => 'b';

            yield 1 => 'c';

            yield 2 => 'd';

            yield 2 => 'e';

            yield 3 => 'f';
    };

    $collection = Collection::with($callback)
        ->group();

has
~~~

Interface: `Hasable`_

head
~~~~

Interface: `Headable`_

Signature: ``Collection::head();``

.. code-block:: php

    $generator = static function (): \Generator {
            yield 1 => 'a';
            yield 1 => 'b';
            yield 1 => 'c';
            yield 2 => 'd';
            yield 2 => 'e';
            yield 3 => 'f';
    };

    Collection::fromIterable($generator())
        ->head(); // [1 => 'a']

ifThenElse
~~~~~~~~~~

Execute a callback when a condition is met.

Interface: `IfThenElseable`_

Signature: ``Collection::ifThenElse(callable $condition, callable $then, ?callable $else = null);``

.. code-block:: php

    $input = range(1, 5);

    $condition = static function (int $value): bool {
        return 0 === $value % 2;
    };

    $then = static function (int $value): int {
        return $value * $value;
    };

    $else = static function (int $value): int {
        return $value + 2;
    };

    Collection::fromIterable($input)
        ->ifThenElse($condition, $then); // [1, 4, 3, 16, 5]

    Collection::fromIterable($input)
        ->ifThenElse($condition, $then, $else) // [3, 4, 5, 16, 7]

implode
~~~~~~~

Interface: `Implodeable`_

init
~~~~

Returns the collection without its last item.

Interface: `Initable`_

Signature: ``Collection::init();``

.. code-block:: php

    Collection::with(range('a', 'e'))
        ->init(); // ['a', 'b', 'c', 'd']

intersect
~~~~~~~~~

Removes any values from the original collection that are not present in the given collection.

Interface: `Intersectable`_

Signature: ``Collection::intersect(...$values);``

.. code-block:: php

    $collection = Collection::with(range('a', 'e'))
        ->intersect('a', 'b', 'c'); // ['a', 'b', 'c']

intersectKeys
~~~~~~~~~~~~~

Removes any keys from the original collection that are not present in the given collection.

Interface: `Intersectkeysable`_

Signature: ``Collection::intersectKeys(...$values);``

.. code-block:: php

    $collection = Collection::with(range('a', 'e'))
        ->intersectKeys(0, 2, 4); // ['a', 'c', 'e']

intersperse
~~~~~~~~~~~

Insert a given value at every n element of a collection and indices are not preserved.

Interface: `Intersperseable`_

Signature: ``Collection::intersperse($element, int $every = 1, int $startAt = 0);``

.. code-block:: php

    $collection = Collection::with(range('a', 'z'))
        ->intersperse('foo', 3);

keys
~~~~

Get the keys of the items.

Interface: `Keysable`_

Signature: ``Collection::keys();``

.. code-block:: php

    $collection = Collection::with(range('a', 'z'))
        ->keys();

last
~~~~

Get the last items from the collection passing the given truth test.

Interface: `Lastable`_

Signature: ``Collection::last(?callable $callback = null, int $size = 1);``

.. code-block:: php

        $generator = static function (): Generator {
            yield 'a' => 'a';
            yield 'b' => 'b';
            yield 'c' => 'c';
            yield 'a' => 'd';
            yield 'b' => 'e';
            yield 'c' => 'f';
        };

        Collection::fromIterable($generator())
            ->last(
                static function ($value, $key) {
                    return 'b' === $key;
                }
            ); // ['b' => 'e']

        Collection::fromIterable($generator())
            ->last(
                static function ($value, $key) {
                    return 'b' === $key;
                },
                2
            ); // ['b' => 'e', 'b' => 'b']

limit
~~~~~

Limit the amount of values in the collection.

Interface: `Limitable`_

Signature: ``Collection::limit(int $limit);``

.. code-block:: php

    $fibonacci = static function ($a = 0, $b = 1): array {
        return [$b, $a + $b];
    };

    $collection = Collection::iterate($fibonacci)
        ->limit(10);

map
~~~

Apply one or more supplied callbacks to every item of a collection and use the return value.

.. warning:: Keys are preserved, use the "normalize" operation if you want to re-index the keys.

Interface: `Mapable`_

Signature: ``Collection::map(callable ...$callbacks);``

.. code-block:: php

    $mapper = static function($value, $key) {
        return $value * 2;
    };

    $collection = Collection::with(range(1, 100))
        ->map($mapper);

merge
~~~~~

Merge one or more collection of items onto a collection.

Interface: `Mergeable`_

Signature: ``Collection::merge(...$sources);``

.. code-block:: php

    $collection = Collection::with(range(1, 10))
        ->merge(['a', 'b', 'c'])

normalize
~~~~~~~~~

Replace, reorder and use numeric keys on a collection.

Interface: `Normalizeable`_

Signature: ``Collection::normalize();``

.. code-block:: php

    $collection = Collection::with(['a' => 'a', 'b' => 'b', 'c' => 'c'])
        ->normalize();

nth
~~~

Get every n-th element of a collection.

Interface: `Nthable`_

Signature: ``Collection::nth(int $step, int $offset = 0);``

.. code-block:: php

    $collection = Collection::with(range(10, 100))
        ->nth(3);

nullsy
~~~~~~

Interface: `Nullsyable`_

only
~~~~

Get items having corresponding given keys.

Interface: `Onlyable`_

Signature: ``Collection::only(...$keys);``

.. code-block:: php

    $collection = Collection::with(range(10, 100))
        ->only(3, 10, 'a', 9);

pack
~~~~

Wrap each items into an array containing 2 items: the key and the value.

Interface: `Packable`_

Signature: ``Collection::pack();``

.. code-block:: php

    $input = ['a' => 'b', 'c' => 'd', 'e' => 'f'];

    $c = Collection::fromIterable($input)
        ->pack();

     // [
     //   ['a', 'b'],
     //   ['c', 'd'],
     //   ['e', 'f'],
     // ]


pad
~~~

Pad a collection to the given length with a given value.

Interface: `Padable`_

Signature: ``Collection::pad(int $size, $value);``

.. code-block:: php

    $collection = Collection::with(range(1, 5))
        ->pad(10, 'foo');

pair
~~~~

Make an associative collection from pairs of values.

Interface: `Pairable`_

Signature: ``Collection::pair();``

.. code-block:: php

    $input = [
        [
            'key' => 'k1',
            'value' => 'v1',
        ],
        [
            'key' => 'k2',
            'value' => 'v2',
        ],
        [
            'key' => 'k3',
            'value' => 'v3',
        ],
        [
            'key' => 'k4',
            'value' => 'v4',
        ],
        [
            'key' => 'k4',
            'value' => 'v5',
        ],
    ];

    $c = Collection::fromIterable($input)
        ->unwrap()
        ->pair()
        ->group()
        ->all();

    // [
    //    [k1] => v1
    //    [k2] => v2
    //    [k3] => v3
    //    [k4] => [
    //        [0] => v4
    //        [1] => v5
    //    ]
    // ]

permutate
~~~~~~~~~

Find all the permutations of a collection.

Interface: `Permutateable`_

Signature: ``Collection::permutate(int $size, $value);``

.. code-block:: php

    $collection = Collection::with(['hello', 'how', 'are', 'you'])
        ->permutate();

pluck
~~~~~

Retrieves all of the values of a collection for a given key.

Interface: `Pluckable`_

Signature: ``Collection::pluck($pluck, $default = null);``

.. code-block:: php

    $fibonacci = static function ($a = 0, $b = 1): array {
        return [$b, $a + $b];
    };

    $collection = Collection::iterate($fibonacci)
        ->limit(10)
        ->pluck(0);

prepend
~~~~~~~

Push an item onto the beginning of the collection.

Interface: `Prependable`_

Signature: ``Collection::prepend(...$items);``

.. code-block:: php

    $collection = Collection::with(['4', '5', '6'])
        ->prepend('1', '2', '3');

product
~~~~~~~

Get the the cartesian product of items of a collection.

Interface: `Productable`_

Signature: ``Collection::product(iterable ...$iterables);``

.. code-block:: php

    $collection = Collection::with(['4', '5', '6'])
        ->product(['1', '2', '3'], ['a', 'b'], ['foo', 'bar']);

random
~~~~~~

It returns a random item from the collection.
An optional integer can be passed to random to specify how many items you would like to randomly retrieve.

Interface: `Randomable`_

Signature: ``Collection::random(int $size = 1);``

.. code-block:: php

    $collection = Collection::with(['4', '5', '6'])
        ->random(); // ['6']

reduce
~~~~~~

Interface: `Reduceable`_

reduction
~~~~~~~~~

Reduce a collection of items through a given callback.

Interface: `Reductionable`_

Signature: ``Collection::reduction(callable $callback, $initial = null);``

.. code-block:: php

    $multiplication = static function ($value1, $value2) {
        return $value1 * $value2;
    };

    $addition = static function ($value1, $value2) {
        return $value1 + $value2;
    };

    $fact = static function (int $number) use ($multiplication) {
        return Collection::range(1, $number + 1)
            ->reduce(
                $multiplication,
                1
            );
    };

    $e = static function (int $value) use ($fact): float {
        return $value / $fact($value);
    };

    $number_e_approximation = Collection::times()
        ->map($e)
        ->limit(10)
        ->reduction($addition);

reverse
~~~~~~~

Reverse order items of a collection.

Interface: `Reverseable`_

Signature: ``Collection::reverse();``

.. code-block:: php

    $collection = Collection::with(['a', 'b', 'c'])
        ->reverse();

rsample
~~~~~~~

Work in progress... sorry.

scale
~~~~~

Scale/normalize values.

Interface: `Scaleable`_

Signature: ``Collection::scale(float $lowerBound, float $upperBound, ?float $wantedLowerBound = null, ?float $wantedUpperBound = null, ?float $base = null);``

.. code-block:: php

    $collection = Collection::range(0, 10, 2)
        ->scale(0, 10);

    $collection = Collection::range(0, 10, 2)
        ->scale(0, 10, 5, 15, 3);

since
~~~~~

Skip items until callback is met.

Interface: `Sinceable`_

Signature: ``Collection::since(callable ...$callbacks);``

.. code-block:: php

    // Parse the composer.json of a package and get the require-dev dependencies.
    $collection = Collection::with(fopen(__DIR__ . '/composer.json', 'rb'))
        // Group items when EOL character is found.
        ->split(
            static function (string $character): bool {
                return "\n" === $character;
            }
        )
        // Implode characters to create a line string
        ->map(
            static function (array $characters): string {
                return implode('', $characters);
            }
        )
        // Skip items until the string "require-dev" is found.
        ->since(
            static function ($line) {
                return false !== strpos($line, 'require-dev');
            }
        )
        // Skip items after the string "}" is found.
        ->until(
            static function ($line) {
                return false !== strpos($line, '}');
            }
        )
        // Re-index the keys
        ->normalize()
        // Filter out the first line and the last line.
        ->filter(
            static function ($line, $index) {
                return 0 !== $index;
            },
            static function ($line) {
                return false === strpos($line, '}');
            }
        )
        // Trim remaining results and explode the string on ':'.
        ->map(
            static function ($line) {
                return trim($line);
            },
            static function ($line) {
                return explode(':', $line);
            }
        )
        // Take the first item.
        ->pluck(0)
        // Convert to array.
        ->all();

        print_r($collection);

skip
~~~~

Skip the n items of a collection.

Interface: `Skipable`_

Signature: ``Collection::skip(int ...$counts);``

.. code-block:: php

    $collection = Collection::with(range(10, 20))
        ->skip(2);

slice
~~~~~

Get a slice of a collection.

Interface: `Sliceable`_

Signature: ``Collection::slice(int $offset, ?int $length = null);``

.. code-block:: php

    $collection = Collection::with(range('a', 'z'))
        ->slice(5, 5);

sort
~~~~

Sort a collection using a callback. If no callback is provided, it will sort using natural order.

By default, it will sort by values and using a callback. If you want to sort by keys, you can pass a parameter to change
the behavior or use twice the flip operation. See the example below.

Interface: `Sortable`_

Signature: ``Collection::sort(?callable $callback = null);``

.. code-block:: php

    // Regular values sorting
    $collection = Collection::with(['z', 'y', 'x'])
        ->sort();

    // Regular values sorting
    $collection = Collection::with(['z', 'y', 'x'])
        ->sort(Operation\Sortable::BY_VALUES);

    // Regular values sorting with a custom callback
    $collection = Collection::with(['z', 'y', 'x'])
        ->sort(
                Operation\Sortable::BY_VALUES,
                static function ($left, $right): int {
                    // Do the comparison here.
                    return $left <=> $right;
                }
        );

    // Regular keys sorting (no callback is needed here)
    $collection = Collection::with(['z', 'y', 'x'])
        ->sort(
                Operation\Sortable::BY_KEYS
        );

    // Regular keys sorting using flip() operations.
    $collection = Collection::with(['z', 'y', 'x'])
        ->flip() // Exchange values and keys
        ->sort() // Sort the values (which are now the keys)
        ->flip(); // Flip again to put back the keys and values, sorted by keys.

split
~~~~~

Split a collection using a callback.

Interface: `Splitable`_

Signature: ``Collection::split(callable ...$callbacks);``

.. code-block:: php

    $splitter = static function ($value, $key) {
        return 0 === $value % 3;
    };

    $collection = Collection::with(range(0, 20))
        ->split($splitter);

tail
~~~~

Get the collection items except the first.

Interface: `Tailable`_

Signature: ``Collection::tail();``

.. code-block:: php

    Collection::with(['a', 'b', 'c'])
        ->tail(); // [1 => 'b', 2 => 'c']

transpose
~~~~~~~~~

Matrix transposition.

Interface: `Transposeable`_

Signature: ``Collection::transpose();``

.. code-block:: php

    $records = [
        [
            'id' => 2135,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ],
        [
            'id' => 3245,
            'first_name' => 'Sally',
            'last_name' => 'Smith',
        ],
        [
            'id' => 5342,
            'first_name' => 'Jane',
            'last_name' => 'Jones',
        ],
        [
            'id' => 5623,
            'first_name' => 'Peter',
            'last_name' => 'Doe',
        ],
    ];

    $result = Collection::with($records)
        ->transpose();

truthy
~~~~~~

Interface: `Truthyable`_

unpack
~~~~~~

Unpack items.

Interface: `Unpackable`_

Signature: ``Collection::unpack();``

.. code-block:: php

    $input = [['a', 'b'], ['c', 'd'], ['e', 'f']];

    $c = Collection::fromIterable($input)
        ->unpack();

    // [
    //     ['a' => 'b'],
    //     ['c' => 'd'],
    //     ['e' => 'f'],
    // ];

unpair
~~~~~~

Unpair a collection of pairs.

Interface: `Unpairable`_

Signature: ``Collection::unpair();``

.. code-block:: php

    $input = [
        'k1' => 'v1',
        'k2' => 'v2',
        'k3' => 'v3',
        'k4' => 'v4',
    ];

    $c = Collection::fromIterable($input)
        ->unpair();

    // [
    //     ['k1', 'v1'],
    //     ['k2', 'v2'],
    //     ['k3', 'v3'],
    //     ['k4', 'v4'],
    // ];

until
~~~~~

Limit a collection using a callback.

Interface: `Untilable`_

Signature: ``Collection::until(callable ...$callbacks);``

.. code-block:: php

    // The Collatz conjecture (https://en.wikipedia.org/wiki/Collatz_conjecture)
    $collatz = static function (int $value): int
    {
        return 0 === $value % 2 ?
            $value / 2:
            $value * 3 + 1;
    };

    $collection = Collection::iterate($collatz, 10)
        ->until(static function ($number): bool {
            return 1 === $number;
        });

unwrap
~~~~~~

Unwrap every collection element.

Interface: `Unwrapable`_

Signature: ``Collection::unwrap();``

.. code-block:: php

     $data = [['a' => 'A'], ['b' => 'B'], ['c' => 'C']];

     $collection = Collection::with($data)
        ->unwrap();

window
~~~~~~

Loop the collection by yielding a specific window of data of a given length.

Interface: `Windowable`_

Signature: ``Collection::window(int ...$length);``

.. code-block:: php

     $data = range('a', 'z');

     $collection = Collection::with($data)
        ->window(2, 3)
        ->all();

wrap
~~~~

Wrap every element into an array.

Interface: `Wrapable`_

Signature: ``Collection::wrap();``

.. code-block:: php

     $data = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

     $collection = Collection::with($data)
        ->wrap();

zip
~~~

Zip a collection together with one or more iterables.

Interface: `Zipable`_

Signature: ``Collection::zip(iterable ...$iterables);``

.. code-block:: php

    $even = Collection::range(0, INF, 2);
    $odd = Collection::range(1, INF, 2);

    $positiveIntegers = Collection::with($even)
        ->zip($odd)
        ->limit(100)
        ->flatten();

.. _Allable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Allable.php
.. _Appendable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Appendable.php
.. _Applyable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Applyable.php
.. _Associateable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Associateable.php
.. _Cacheable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Cacheable.php
.. _Chunkable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Chunkable.php
.. _Collapseable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Collapseable.php
.. _Columnable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Columnable.php
.. _Combinateable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Combinateable.php
.. _Combineable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Combineable.php
.. _Compactable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Compactable.php
.. _Containsable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Containsable.php
.. _Cycleable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Cycleable.php
.. _Diffable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Diffable.php
.. _Diffkeysable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Diffkeysable.php
.. _Distinctable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Distinctable.php
.. _Explodeable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Explodeable.php
.. _Falsyable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Falsyable.php
.. _Filterable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Filterable.php
.. _Firstable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Firstable.php
.. _Flattenable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Flattenable.php
.. _Flipable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Flipable.php
.. _array_flip(): https://php.net/array_flip
.. _FoldLeftable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/FoldLeftable.php
.. _FoldRightable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/FoldRightable.php
.. _Forgetable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Forgetable.php
.. _Frequencyable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Frequencyable.php
.. _Getable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Getable.php
.. _Groupable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Groupable.php
.. _Hasable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Hasable.php
.. _Headable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Headable.php
.. _IfThenElseable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/IfThenElseable.php
.. _Implodeable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Implodeable.php
.. _Initable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Initable.php
.. _Intersectable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Intersectable.php
.. _Intersectkeysable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Intersectkeysable.php
.. _Intersperseable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Intersperseable.php
.. _Keysable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Keysable.php
.. _Lastable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Lastable.php
.. _Limitable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Limitable.php
.. _Mapable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Mapable.php
.. _Mergeable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Mergeable.php
.. _Normalizeable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Normalizeable.php
.. _Nthable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Nthable.php
.. _Nullsyable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Nullsyable.php
.. _Onlyable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Onlyable.php
.. _Packable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Packable.php
.. _Padable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Padable.php
.. _Pairable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Pairable.php
.. _Permutateable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Permutateable.php
.. _Pluckable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Pluckable.php
.. _Prependable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Prependable.php
.. _Productable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Productable.php
.. _Randomable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Randomable.php
.. _Reduceable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Reduceable.php
.. _Reductionable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Reductionable.php
.. _Reverseable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Reverseable.php
.. _Scaleable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Scaleable.php
.. _Skipable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Skipable.php
.. _Sinceable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Sinceable.php
.. _Sliceable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Sliceable.php
.. _Sortable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Sortable.php
.. _Splitable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Splitable.php
.. _Tailable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Tailable.php
.. _Transposeable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Transposeable.php
.. _Truthyable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Truthyable.php
.. _Unpackable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Unpackagle.php
.. _Unpairable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Unpairable.php
.. _Untilable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Untilable.php
.. _Unwrapable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Unwrapable.php
.. _Windowable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Windowable.php
.. _Wrapable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Wrapable.php
.. _Zipable: https://github.com/loophp/collection/blob/master/src/Contract/Operation/Zipable.php
