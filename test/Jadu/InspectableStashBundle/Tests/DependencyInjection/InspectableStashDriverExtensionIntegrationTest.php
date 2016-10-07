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

use Jadu\InspectableStashBundle\DependencyInjection\InspectableStashDriverExtension;
use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class InspectableStashDriverExtensionIntegrationTest
 *
 * @author Jadu Ltd.
 */
class InspectableStashDriverExtensionIntegrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var InspectableStashDriverExtension
     */
    private $extension;

    public function setUp()
    {
        $this->containerBuilder = new ContainerBuilder();

        $this->extension = new InspectableStashDriverExtension();
    }

    public function testShouldDefineMemcachedInspectorService()
    {
        $this->extension->load([
            'inspectable_stash_driver' => [
                'memcached_service' => 'my.service_id'
            ]
        ], $this->containerBuilder);

        $this->assertTrue($this->containerBuilder->hasDefinition('inspectable_stash_driver.inspector.memcached'));
    }

    public function testShouldDefineStashCacheDumpCommandService()
    {
        $this->extension->load([
            'inspectable_stash_driver' => [
                'memcached_service' => 'my.service_id'
            ]
        ], $this->containerBuilder);

        $this->assertTrue($this->containerBuilder->hasDefinition('inspectable_stash_driver.command.dump_stash_cache'));
    }

    public function testShouldSetMemcachedServiceIdAsContainerParameter()
    {
        $this->extension->load([
            'inspectable_stash_driver' => [
                'memcached_service' => 'my.service_id'
            ]
        ], $this->containerBuilder);

        $this->assertSame(
            'my.service_id',
            $this->containerBuilder->getParameter('inspectable_stash_driver.memcached_service')
        );
    }
}
