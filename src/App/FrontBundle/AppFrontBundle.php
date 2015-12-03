<?php

namespace App\FrontBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use App\FrontBundle\Entity\Product;

class AppFrontBundle extends Bundle
{
    public function boot()
    {
        Product::setUploadDir($this->container->getParameter('products_upload_dir'));
    }
}
