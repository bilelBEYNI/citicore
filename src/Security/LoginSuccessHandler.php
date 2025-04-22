<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        $roles = $user->getRoles();  

        
        
        $role = reset($roles); 
        switch ($role) {
            case 'Admin':
                return new RedirectResponse($this->router->generate('admin_dashboard'));
                
            case 'Participant':
                return new RedirectResponse($this->router->generate('participant_dashboard'));
           
            default:
                return new RedirectResponse($this->router->generate('app_default'));
        }
    }
}



