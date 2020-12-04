<?php

namespace Szkolenie\Repository;

use Szkolenie\Entity\User;

interface GetUserEmailInterface
{
    /**
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User;
}
