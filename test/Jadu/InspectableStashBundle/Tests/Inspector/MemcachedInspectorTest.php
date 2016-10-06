<?php

/*
 * This file is part of the InspectableStashBundle package.
 *
 * (c) Jadu Ltd. <https://jadu.net>
 *
 * For the full copyright and license information, please view the NCSA-LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Jadu\InspectableStashBundle\Tests\Inspector;

use Jadu\InspectableStashBundle\Inspector\CacheEntry;
use Jadu\InspectableStashBundle\Inspector\MemcachedInspector;
use Memcached;
use Mockery;
use Mockery\Mock;
use PHPUnit_Framework_TestCase;
use RuntimeException;

/**
 * Class MemcachedInspectorTest
 *
 * @author Jadu Ltd.
 */
class MemcachedInspectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MemcachedInspector
     */
    private $inspector;

    /**
     * @var Mock|Memcached
     */
    private $memcached;

    public function setUp()
    {
        $this->memcached = Mockery::mock([
            'getAllKeys' => ['abc123', '456def', 'a-non-inspectable-stash-key', 'a-non-stash-key']
        ]);
        $this->memcached->shouldReceive('get')->andReturnUsing(function ($hashedKey) {
            if ($hashedKey === 'abc123') {
                return [
                    'data' => [
                        'originalKey' => ['my', 'first', 'key'],
                        'originalData' => 'my first value'
                    ],
                    'expiration' => '123123123'
                ];
            }

            if ($hashedKey === '456def') {
                return [
                    'data' => [
                        'originalKey' => ['my', 'second', 'key'],
                        'originalData' => 'my second value'
                    ],
                    'expiration' => '234234234'
                ];
            }

            if ($hashedKey === 'a-non-inspectable-stash-key') {
                return [
                    'data' => 'a non-inspectable stash value',
                    'expiration' => '456456456'
                ];
            }

            if ($hashedKey === 'a-non-stash-key') {
                return 'a non-stash value';
            }

            return false;
        })->byDefault();

        $this->inspector = new MemcachedInspector($this->memcached);
    }

    public function testGetCacheEntriesReturnsACacheEntryForAllKeys()
    {
        /** @var CacheEntry[] $cacheEntries */
        $cacheEntries = $this->inspector->getCacheEntries();

        $this->assertCount(2, $cacheEntries);
        $this->assertSame('abc123', $cacheEntries[0]->getHashedKey());
        $this->assertEquals(['my', 'first', 'key'], $cacheEntries[0]->getOriginalKey());
        $this->assertSame('my first value', $cacheEntries[0]->getValue());
        $this->assertSame('456def', $cacheEntries[1]->getHashedKey());
        $this->assertEquals(['my', 'second', 'key'], $cacheEntries[1]->getOriginalKey());
        $this->assertSame('my second value', $cacheEntries[1]->getValue());
    }

    public function testGetCacheEntriesThrowsExceptionWhenMemcachedReturnsFailure()
    {
        $this->memcached->shouldReceive('getAllKeys')->andReturn(false);

        $this->setExpectedException(
            RuntimeException::class,
            'Could not talk to Memcached - is Memcached running on the machine this command was run from?'
        );

        $this->inspector->getCacheEntries();
    }

    public function testGetValueReturnsValueForTheSpecifiedEntry()
    {
        /** @var Mock|CacheEntry $cacheEntry */
        $cacheEntry = Mockery::mock(CacheEntry::class, [
            'getHashedKey' => '456def',
            'getOriginalKey' => ['my', 'second', 'key'],
            'getOriginalKeyString' => 'my/second/key'
        ]);

        $this->assertSame('my second value', $this->inspector->getValue($cacheEntry));
    }

    public function testGetValueThrowsExceptionWhenValueReturnedFromMemcachedIsNotInInspectableFormat()
    {
        /** @var Mock|CacheEntry $cacheEntry */
        $cacheEntry = Mockery::mock(CacheEntry::class, [
            'getHashedKey' => 'a-non-inspectable-stash-key',
            'getOriginalKey' => ['my', 'non-inspectable', 'key'],
            'getOriginalKeyString' => 'my/non-inspectable/key'
        ]);

        $this->setExpectedException(
            RuntimeException::class,
            'Value for cache key "my/non-inspectable/key" is not in inspectable format - do you need to clear the cache?"'
        );

        $this->inspector->getValue($cacheEntry);
    }

    public function testGetValueThrowsExceptionWhenValueReturnedFromMemcachedIsNotAnEntryFromStash()
    {
        /** @var Mock|CacheEntry $cacheEntry */
        $cacheEntry = Mockery::mock(CacheEntry::class, [
            'getHashedKey' => 'a-non-stash-key',
            'getOriginalKey' => ['my', 'non-stash', 'key'],
            'getOriginalKeyString' => 'my/non-stash/key'
        ]);

        $this->setExpectedException(
            RuntimeException::class,
            'Value for cache key "my/non-stash/key" is not in inspectable format - do you need to clear the cache?"'
        );

        $this->inspector->getValue($cacheEntry);
    }
}
