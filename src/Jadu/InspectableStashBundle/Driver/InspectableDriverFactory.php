<?php

/*
 * This file is part of the InspectableStashBundle package.
 *
 * (c) Jadu Ltd. <https://jadu.net>
 *
 * For the full copyright and license information, please view the NCSA-LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Jadu\InspectableStashBundle\Driver;

use RuntimeException;
use Stash\Interfaces\DriverInterface;
use Tedivm\StashBundle\Factory\DriverFactory as StashDriverFactory;

/**
 * Class InspectableDriverFactory
 *
 * @author Jadu Ltd.
 */
class InspectableDriverFactory
{
    /**
     * Creates the "real" driver from Stash's DriverFactory, then returns
     * an InspectableDriver wrapping the real driver
     *
     * @param array $types
     * @param array $options
     * @throws RuntimeException
     * @return DriverInterface
     */
    public static function createDriver(array $types, array $options)
    {
        $wrappedDriver = StashDriverFactory::createDriver($types, $options);

        return new InspectableDriver($wrappedDriver);
    }
}
