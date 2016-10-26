<?php

namespace App\FrontBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use App\FrontBundle\Entity\Product;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use App\FrontBundle\DependencyInjection\Compiler\OverrideServiceCompilerPass;

class AppFrontBundle extends Bundle
{
    public function boot()
    {
        Product::setUploadDir($this->container->getParameter('products_upload_dir'));
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new OverrideServiceCompilerPass());
    }
}
