<?php

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var User $currentUser */
        $currentUser = $subject;

        if ($user !== $currentUser) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($currentUser, $user);
            case self::EDIT:
                return $this->canEdit($currentUser, $user);
            case self::DELETE:
                return $this->canEdit($currentUser, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(User $currentUser, User $user): bool
    {
        if ($this->canEdit($currentUser, $user)) {
            return true;
        }
    }

    private function canEdit(User $currentUser, User $user): bool
    {
        if ($this->canDelete($currentUser, $user)) {
            return true;
        }
    }

    private function canDelete(User $currentUser, User $user): bool
    {
        return true;
    }
}