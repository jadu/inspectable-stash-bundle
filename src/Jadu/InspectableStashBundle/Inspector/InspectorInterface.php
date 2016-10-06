<?php

namespace Jadu\InspectableStashBundle\Inspector;

use RuntimeException;

/**
 * Interface InspectorInterface
 *
 * @author Jadu Ltd.
 */
interface InspectorInterface
{
    /**
     * Fetches all original keys (in array format) currently stored in Memcached,
     * where the `InspectableDriver` was used to store them.
     * False will be returned on failure
     *
     * @return CacheEntry[]
     * @throws RuntimeException
     */
    public function getCacheEntries();

    /**
     * Fetches the value for the specified cache entry
     *
     * @param CacheEntry $cacheEntry
     * @return mixed
     */
    public function getValue(CacheEntry $cacheEntry);
}
