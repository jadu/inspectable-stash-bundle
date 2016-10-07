<?php

/*
 * This file is part of the InspectableStashBundle package.
 *
 * (c) Jadu Ltd. <https://jadu.net>
 *
 * For the full copyright and license information, please view the NCSA-LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Jadu\InspectableStashBundle;

use Jadu\InspectableStashBundle\DependencyInjection\Compiler\InspectableDriverCompilerPass;
use Jadu\InspectableStashBundle\DependencyInjection\Compiler\MemcachedServiceCompilerPass;
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

        $container->addCompilerPass(new MemcachedServiceCompilerPass());
        $container->addCompilerPass(new InspectableDriverCompilerPass());
    }
}
