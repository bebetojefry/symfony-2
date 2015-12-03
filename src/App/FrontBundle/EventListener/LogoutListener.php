<?php

namespace App\FrontBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutListener implements  LogoutSuccessHandlerInterface
{
    public function onLogoutSuccess(Request $request)
    {
        return new Response('', 401);
    }
}

?>
