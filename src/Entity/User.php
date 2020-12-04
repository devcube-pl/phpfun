<?php

namespace Szkolenie\Entity;

class User
{
    private $id;

    private $name;

    private $email;

    private $password;

    private $createdAt;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
        $this->createdAt = date('Y-m-d H:i:s');
    }

    /**
     * @param string $password
     * @return bool|string
     */
    public static function hashPassword(string $password)
    {
        $options = ['cost' => 12];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = self::hashPassword($password);

        return $this;
    }

    /**
     * @param mixed $password
     * @return User
     */
    public function setRawPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param false|string $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     *
     */
    public function clearPassword()
    {
        $this->password = null;
    }
}
