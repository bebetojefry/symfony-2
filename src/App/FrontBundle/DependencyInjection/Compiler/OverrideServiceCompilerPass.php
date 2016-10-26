<?php

namespace App\FrontBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('funddy.jstranslations.service.configuredtranslationsextractor');
        $definition->replaceArgument(0, $container->getDefinition('translator.default'));
    }
}
