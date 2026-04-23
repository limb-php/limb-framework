<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\active_record\src;

use limb\core\src\lmbCollection;
use limb\core\src\lmbCollectionDecorator;
use limb\core\src\lmbCollectionInterface;

/**
 *  Smart, lazy and re-queryable collection returned by {@see lmbARQuery::fetch()}.
 *
 *  Behaviour:
 *    - count() and at($pos) ALWAYS issue a fresh SQL query against the
 *      underlying lmbARQuery, so external mutations are immediately visible
 *      and no AR objects are hydrated unless explicitly requested.
 *    - Iteration / getArray() / jsonSerialize() materialise the full result
 *      set once and cache it. The cache is invalidated by sort()/paginate()
 *      and can be cleared explicitly via reload().
 *    - paginate() stores offset/limit locally and applies them at
 *      materialisation time only. count() still returns the unpaginated total
 *      (matching the historical raw-record-set semantics); countPaginated()
 *      respects the local offset/limit.
 *    - sort() loose forms like ['id'] or 'id' are normalised to the
 *      ['id' => 'ASC'] shape expected by lmbArrayHelper::sortArray().
 *
 * @package active_record
 */
class lmbARLazyCollection extends lmbCollectionDecorator
{
    private lmbARQuery $query;
    private bool $loaded = false;
    private int $offset = 0;
    private int $limit = 0;
    private array $sort_params = [];

    function __construct(lmbARQuery $query)
    {
        $this->query = $query;
        parent::__construct(null);
    }

    /**
     *  True once the iteration snapshot has been materialised.
     */
    function isLoaded(): bool
    {
        return $this->loaded;
    }

    /**
     *  Force materialisation of the iteration snapshot. Useful in tests or
     *  when the caller wants SQL errors to surface eagerly.
     */
    function load(): lmbCollectionInterface
    {
        $this->_materialize();
        return $this->iterator;
    }

    /**
     *  Drop the cached iteration snapshot so the next foreach/getArray()
     *  re-runs SQL and re-hydrates the records.
     */
    function reload(): void
    {
        $this->_invalidate();
    }

    private function _materialize(): void
    {
        if ($this->loaded)
            return;
        $this->loaded = true;

        $collection = $this->query->fetchAll();

        if ($this->sort_params)
            $collection->sort($this->sort_params);

        if ($this->limit)
            $collection->paginate($this->offset, $this->limit);

        $this->iterator = $collection;
    }

    private function _invalidate(): void
    {
        $this->loaded = false;
        $this->iterator = null;
    }

    /**
     *  Always reflects the current row count of the underlying query — never
     *  triggers materialisation, never hydrates AR objects.
     */
    function count(): int
    {
        return $this->query->countRows();
    }

    /**
     *  Always fetches a single row at $pos via LIMIT 1 OFFSET — never
     *  triggers full materialisation. Pagination set via paginate() is
     *  intentionally ignored here, mirroring the historical raw record set
     *  behaviour.
     */
    function at($pos)
    {
        return $this->query->fetchAt((int) $pos);
    }

    function paginate($offset, $limit)
    {
        $this->offset = (int) $offset;
        $this->limit = (int) $limit;
        $this->_invalidate();
        return $this;
    }

    function getOffset()
    {
        return $this->offset;
    }

    function getLimit()
    {
        return $this->limit;
    }

    function countPaginated()
    {
        if (!$this->limit)
            return $this->count();

        $remaining = max(0, $this->count() - $this->offset);
        return min($this->limit, $remaining);
    }

    /**
     *  Records the requested sort. Applied in-memory at materialisation time
     *  (or immediately, if the snapshot is already loaded), matching what
     *  lmbARQuery::sort_params used to do via the raw record set.
     */
    function sort($params)
    {
        $this->sort_params = self::_normalizeSortParams($params);

        if ($this->loaded && $this->sort_params)
            $this->iterator->sort($this->sort_params);

        return $this;
    }

    private static function _normalizeSortParams($params): array
    {
        if (is_string($params))
            return [$params => 'ASC'];

        if (!is_array($params))
            return [];

        $normalized = [];
        foreach ($params as $key => $value) {
            if (is_int($key))
                $normalized[(string) $value] = 'ASC';
            else
                $normalized[$key] = $value;
        }
        return $normalized;
    }

    function getArray()
    {
        $this->_materialize();
        return parent::getArray();
    }

    function rewind(): void
    {
        $this->_materialize();
        parent::rewind();
    }

    function valid(): bool
    {
        $this->_materialize();
        return parent::valid();
    }

    #[\ReturnTypeWillChange]
    function current()
    {
        $this->_materialize();
        return parent::current();
    }

    function next(): void
    {
        $this->_materialize();
        parent::next();
    }

    #[\ReturnTypeWillChange]
    function key()
    {
        $this->_materialize();
        return parent::key();
    }

    function offsetExists($offset): bool
    {
        return $this->at($offset) !== null;
    }

    #[\ReturnTypeWillChange]
    function offsetGet($offset)
    {
        return $this->at($offset);
    }

    function offsetSet($offset, $value): void
    {
        $this->_materialize();
        parent::offsetSet($offset, $value);
    }

    function offsetUnset($offset): void
    {
        $this->_materialize();
        parent::offsetUnset($offset);
    }

    function jsonSerialize(): array
    {
        $this->_materialize();
        return parent::jsonSerialize();
    }
}
