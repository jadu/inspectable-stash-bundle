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

use Hamcrest\Core\IsIdentical;
use Jadu\InspectableStashBundle\Inspector\CacheEntry;
use Jadu\InspectableStashBundle\Inspector\InspectorInterface;
use Jadu\InspectableStashBundle\Inspector\MemcachedInspector;
use Mockery;
use Mockery\Mock;
use PHPUnit_Framework_TestCase;

/**
 * Class CacheEntryTest
 *
 * @author Jadu Ltd.
 */
class CacheEntryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CacheEntry
     */
    private $entry;

    /**
     * @var Mock|InspectorInterface
     */
    private $inspector;

    public function setUp()
    {
        $this->inspector = Mockery::mock(MemcachedInspector::class, [
            'getValue' => 'my value'
        ]);

        $this->entry = new CacheEntry($this->inspector, 'abc123123', ['my', 'cache', 'key']);
    }

    public function testGetHashedKey()
    {
        $this->assertSame('abc123123', $this->entry->getHashedKey());
    }

    public function testGetOriginalKey()
    {
        $this->assertEquals(['my', 'cache', 'key'], $this->entry->getOriginalKey());
    }

    public function testGetOriginalKeyStringWithDefaultDelimiter()
    {
        $this->assertEquals('my/cache/key', $this->entry->getOriginalKeyString());
    }

    public function testGetOriginalKeyStringWithCustomDelimiter()
    {
        $this->assertEquals('my#cache#key', $this->entry->getOriginalKeyString('#'));
    }

    public function testGetValueFetchesTheValueViaTheInspector()
    {
        $this->inspector->shouldReceive('getValue')
            ->with(IsIdentical::identicalTo($this->entry))
            ->andReturn('this is my value');

        $this->assertSame('this is my value', $this->entry->getValue());
    }
}
