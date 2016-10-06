<?php

/*
 * This file is part of the InspectableStashBundle package.
 *
 * (c) Jadu Ltd. <https://jadu.net>
 *
 * For the full copyright and license information, please view the NCSA-LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Jadu\InspectableStashBundle\Tests\Driver;

use Jadu\InspectableStashBundle\Driver\InspectableDriver;
use Mockery;
use Mockery\Mock;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use Stash\Interfaces\DriverInterface;

/**
 * Class InspectableDriverTest
 *
 * @author Jadu Ltd.
 */
class InspectableDriverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var InspectableDriver
     */
    private $driver;

    /**
     * @var Mock|DriverInterface
     */
    private $wrappedDriver;

    public function setUp()
    {
        $this->wrappedDriver = Mockery::mock(DriverInterface::class, [
            'getData' => [
                'data' => [
                    'originalKey' => ['cache', 'stuff', 'a-name'],
                    'originalData' => 'My cached value'
                ],
                'expiration' => 1476042125
            ]
        ]);

        $this->driver = new InspectableDriver($this->wrappedDriver);
    }

    public function testSetOptionsSetsTheOptionsOnTheWrappedDriver()
    {
        $this->wrappedDriver->shouldReceive('setOptions')
            ->once()
            ->with(['first_option' => 'first value']);

        $this->driver->setOptions(['first_option' => 'first value']);
    }

    public function testGetDataReturnsTheCorrectStructureWhenEntryExistsInInspectableFormat()
    {
        $this->wrappedDriver->shouldReceive('getData')
            ->with(['my', 'cache', 'key'])
            ->andReturn([
                'data' => [
                    'originalKey' => ['cache', 'stuff', 'a-name'],
                    'originalData' => 'My cached value'
                ],
                'expiration' => 1476042125
            ]);

        $this->assertEquals(
            [
                'data' => 'My cached value',
                'expiration' => 1476042125
            ],
            $this->driver->getData(['my', 'cache', 'key'])
        );
    }

    public function testGetDataReturnsFalseForAMissingEntryWhenWrappedDriverDoes()
    {
        $this->wrappedDriver->shouldReceive('getData')
            ->with(['my', 'cache', 'key'])
            ->andReturn(false);

        $this->assertFalse($this->driver->getData(['my', 'cache', 'key']));
    }

    /**
     * @dataProvider incorrectFormatDataProvider
     * @param mixed $data
     */
    public function testGetDataThrowsWhenEntryExistsInWrongFormat($data)
    {
        $this->wrappedDriver->shouldReceive('getData')
            ->with(['my', 'cache', 'key'])
            ->andReturn([
                'data' => $data,
                'expiration' => 1476042125
            ]);

        $this->setExpectedException(
            RuntimeException::class,
            'Value for cache key "my/cache/key" is not in inspectable format - do you need to clear the cache?"'
        );

        $this->driver->getData(['my', 'cache', 'key']);
    }

    /**
     * @return array
     */
    public function incorrectFormatDataProvider()
    {
        return [
            [
                21
            ],
            [
                ['some other' => 'array structure']
            ]
        ];
    }

    public function testStoreDataStoresTheCorrectDataViaTheWrappedDriver()
    {
        $this->wrappedDriver->shouldReceive('storeData')
            ->once()
            ->with(
                ['my', 'cache', 'key'],
                [
                    'originalData' => 'my data',
                    'originalKey' => ['my', 'cache', 'key']
                ],
                Mockery::any()
            );

        $this->driver->storeData(
            ['my', 'cache', 'key'],
            'my data',
            123123
        );
    }

    public function testStoreDataStoresTheDataWithCorrectExpirationViaTheWrappedDriver()
    {
        $this->wrappedDriver->shouldReceive('storeData')
            ->once()
            ->with(
                Mockery::any(),
                Mockery::any(),
                321321
            );

        $this->driver->storeData(
            ['my', 'cache', 'key'],
            'my data',
            321321
        );
    }
    
    public function testClearClearsViaTheWrappedDriverWhenNoKeyIsProvided()
    {
        $this->wrappedDriver->shouldReceive('clear')
            ->once()
            ->with(null);

        $this->driver->clear();
    }

    public function testClearClearsTheCorrectKeyViaTheWrappedDriverWhenAKeyIsProvided()
    {
        $this->wrappedDriver->shouldReceive('clear')
            ->once()
            ->with(['my', 'cache', 'key']);

        $this->driver->clear(['my', 'cache', 'key']);
    }

    public function testPurgePurgesViaTheWrappedDriver()
    {
        $this->wrappedDriver->shouldReceive('purge')
            ->once();

        $this->driver->purge();
    }

    public function testIsAvailableReturnsTrueAsWeDoNotStaticallyKnowWhichDriverWillBeWrapped()
    {
        $this->assertTrue(InspectableDriver::isAvailable());
    }
}
