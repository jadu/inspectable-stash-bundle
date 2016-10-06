<?php

namespace Jadu\InspectableStashBundle\DependencyInjection\Compiler;

use Jadu\InspectableStashBundle\Driver\InspectableDriverFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class InspectableDriverCompilerPass
 *
 * @author Jadu Ltd.
 */
class InspectableDriverCompilerPass implements CompilerPassInterface
{
    /**
     * P@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /**
         * `stash.driver` is an abstract service definition from StashBundle.
         * It references a DriverFactory that is used to create the actual DriverInterface object,
         * which we change here to use our custom DriverFactory that produces proxy InspectableDrivers.
         */
        $definition = $container->findDefinition('stash.driver');

        $definition->setFactory([InspectableDriverFactory::class, 'createDriver']);

        // "Undo" the old factory syntax that Stash will have assigned to the service definition
        $definition->setFactoryClass(null);
        $definition->setFactoryMethod(null);
    }
}
