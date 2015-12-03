<?php

namespace App\FrontBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('translator.data_collector');
        $definition->setClass('Funddy\Bundle\JsTranslationsBundle\ReadableTranslator\SymfonyReadableTranslator');
    }
}
