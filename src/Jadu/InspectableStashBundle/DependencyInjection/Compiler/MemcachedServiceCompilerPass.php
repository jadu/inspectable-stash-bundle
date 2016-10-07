<?php

/*
 * This file is part of the InspectableStashBundle package.
 *
 * (c) Jadu Ltd. <https://jadu.net>
 *
 * For the full copyright and license information, please view the NCSA-LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Jadu\InspectableStashBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class MemcachedServiceCompilerPass
 *
 * @author Jadu Ltd.
 */
class MemcachedServiceCompilerPass implements CompilerPassInterface
{
    /**
     * P@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('inspectable_stash_driver.inspector.memcached');

        $definition->setArguments([
            new Reference($container->getParameter('inspectable_stash_driver.memcached_service'))
        ]);
    }
}
