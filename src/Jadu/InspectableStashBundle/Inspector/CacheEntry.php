<?php

namespace Jadu\InspectableStashBundle\Inspector;

/**
 * Class CacheEntry
 *
 * @author Jadu Ltd.
 */
class CacheEntry
{
    /**
     * @var string
     */
    private $hashedKey;

    /**
     * @var MemcachedInspector
     */
    private $inspector;

    /**
     * @var array
     */
    private $originalKey;

    /**
     * @param InspectorInterface $inspector
     * @param string $hashedKey
     * @param array $originalKey
     */
    public function __construct(InspectorInterface $inspector, $hashedKey, array $originalKey)
    {
        $this->hashedKey = $hashedKey;
        $this->originalKey = $originalKey;
        $this->inspector = $inspector;
    }

    /**
     * Returns the hashed key used to store this cache entry
     *
     * @return string
     */
    public function getHashedKey()
    {
        return $this->hashedKey;
    }

    /**
     * Returns the original key used to store this cache entry
     *
     * @return array
     */
    public function getOriginalKey()
    {
        return $this->originalKey;
    }

    /**
     * Returns the original key used to store this cache entry as a string
     * (assuming a delimiter of "/", by default)
     *
     * @return string
     */
    public function getOriginalKeyString($delimiter = '/')
    {
        return implode($delimiter, $this->originalKey);
    }

    /**
     * Returns the original value of this cache entry
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->inspector->getValue($this);
    }
}
