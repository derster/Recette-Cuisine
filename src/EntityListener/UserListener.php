<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
    private UserPasswordHasherInterface $userHash;

    public function __construct(UserPasswordHasherInterface $userHash)
    {
        $this->userHash = $userHash;
    }

    public function prePersist(User $user)
    {
        $this->encodePassword($user);
    }

    public function preUpdate(User $user)
    {
        $this->encodePassword($user);
    }

    /**
     *
     * Encode passsWord base on plainPassword
     *
     * @param User $user
     */

    public function encodePassword(User $user)
    {
        if ($user->getPlainPassword() === null){
            return;
        }
        $hashPass = $this->userHash->hashPassword($user,$user->getPlainPassword());
        $user->setPassword($hashPass);

        $user->setPlainPassword(null);
    }
}