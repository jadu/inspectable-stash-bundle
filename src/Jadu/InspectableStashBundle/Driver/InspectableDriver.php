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

/**
 * Class InspectableDriver
 *
 * @author Jadu Ltd.
 */
class InspectableDriver implements DriverInterface
{
    /**
     * @var DriverInterface
     */
    private $wrappedDriver;

    /**
     * @param DriverInterface $wrappedDriver
     */
    public function __construct(DriverInterface $wrappedDriver)
    {
        $this->wrappedDriver = $wrappedDriver;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options = array())
    {
        $this->wrappedDriver->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key)
    {
        $value = $this->wrappedDriver->getData($key);
        
        if ($value === false) {
            return false;
        }

        if (!is_array($value['data']) || !array_key_exists('originalData', $value['data'])) {
            throw new RuntimeException(sprintf(
                'Value for cache key "%s" is not in inspectable format - do you need to clear the cache?"',
                implode('/', $key)
            ));
        }

        return [
            'data' => $value['data']['originalData'],
            'expiration' => $value['expiration']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function storeData($key, $data, $expiration)
    {
        return $this->wrappedDriver->storeData(
            $key,
            [
                'originalData' => $data,
                'originalKey' => $key
            ],
            $expiration
        );
    }

    /**
     * {@inheritdoc}
     */
    public function clear($key = null)
    {
        return $this->wrappedDriver->clear($key);
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        return $this->wrappedDriver->purge();
    }

    /**
     * This function checks to see if this driver is available. This always returns true because
     * the result depends on the wrapped driver used, which is not known statically
     *
     * {@inheritdoc}
     *
     * @return bool true
     */
    public static function isAvailable()
    {
        return true;
    }
}
