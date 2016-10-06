<?php

namespace Jadu\InspectableStashBundle;

use Jadu\InspectableStashBundle\DependencyInjection\Compiler\InspectableDriverCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class InspectableStashBundle
 *
 * @author Jadu Ltd.
 */
class InspectableStashDriverBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new InspectableDriverCompilerPass());
    }
}
