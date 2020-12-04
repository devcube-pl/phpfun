<?php

namespace Szkolenie\Repository;

use Szkolenie\Entity\User;

class UserRepository
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $email
     * @return User
     */
    public function getUserByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email=:email");
        $stmt->execute(['email' => $email]);

        $raw = $stmt->fetch();

        if ($raw === false) {
            return null;
        }

        return $this->createEntity($raw);
    }

    /**
     * @param User $user
     * @return string
     */
    public function createUser(User $user)
    {
        $sql = 'INSERT INTO users(`name`, email, password, created_at) VALUES(?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getCreatedAt()
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * @param array $raw
     * @return User
     */
    private function createEntity(array $raw)
    {
        $user = new User($raw['id']);
        $user->setName($raw['name']);
        $user->setEmail($raw['email']);
        $user->setRawPassword($raw['password']);
        $user->setCreatedAt($raw['created_at']);
        return $user;
    }
}
