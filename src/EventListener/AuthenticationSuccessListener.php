<?php

namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener 
{
    private $currentUser;


    public function __construct(UserRepository $currentUser) {
        $this->currentUser = $currentUser;

    }

    /**
    * @param AuthenticationSuccessEvent $event
    */
    public function OnAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();
        $detailsUser = $this->currentUser->find($user);
        //dd($user);

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['data'] = array(
            'id' => $detailsUser->getId(),
            'email' => $detailsUser->getEmail(),
            'username' => $detailsUser->getUsername(),
        );

        $event->setData($data);
    }
}

