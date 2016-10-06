<?php

namespace Jadu\InspectableStashBundle\Inspector;

use Memcached;
use RuntimeException;

/**
 * Class MemcachedInspector
 *
 * @author Jadu Ltd.
 */
class MemcachedInspector implements InspectorInterface
{
    /**
     * @var Memcached
     */
    private $memcached;

    /**
     * Unable to typehint with `Memcached` here as Mockery is unable to create
     * a mock of the Memcached class because it passes incorrect info to the Reflection API.
     *
     * @param Memcached $memcached
     */
    public function __construct(/* Memcached */$memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheEntries()
    {
        $keys = $this->memcached->getAllKeys();

        if ($keys === false) {
            throw new RuntimeException(
                'Could not talk to Memcached - is Memcached running on the machine this command was run from?'
            );
        }

        $cacheEntries = [];
        
        foreach ($keys as $hashedKey) {
            $data = $this->memcached->get($hashedKey);

            if ($data === false ||
                !is_array($data) ||
                !array_key_exists('data', $data) ||
                !is_array($data['data']) ||
                !array_key_exists('originalKey', $data['data'])
            ) {
                continue;
            }

            $cacheEntries[] = new CacheEntry($this, $hashedKey, $data['data']['originalKey']);
        }

        return $cacheEntries;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(CacheEntry $cacheEntry)
    {
        $data = $this->memcached->get($cacheEntry->getHashedKey());

        if ($data === false ||
            !is_array($data) ||
            !array_key_exists('data', $data) ||
            !is_array($data['data']) ||
            !array_key_exists('originalData', $data['data'])
        ) {
            throw new RuntimeException(sprintf(
                'Value for cache key "%s" is not in inspectable format - do you need to clear the cache?"',
                $cacheEntry->getOriginalKeyString()
            ));
        }

        return $data['data']['originalData'];
    }
}
