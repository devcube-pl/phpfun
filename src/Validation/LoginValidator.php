<?php

namespace Szkolenie\Validation;

class LoginValidator
{
    /**
     * @var array
     */
    private $data;

    /**
     * @return bool
     */
    public function validate()
    {
        //https://github.com/davidecesarano/Embryo-Validation
        $errors = false;

        $email = $this->validateEmail(filter_input(INPUT_POST, 'email'));

        if ($email == false) {
            $errors['email'] = 'Podany adres e-mail jest nieprawidłowy';
        }

        $pwd = $this->validatePassword(filter_input(INPUT_POST, 'pwd'));

        if ($pwd == false) {
            $errors['pwd'] = 'Hasło musi mieć długość od 6 do 70 znaków i może zawierać litery, cyfry oraz znaki ! & . (kropka)';
        }

        $this->data = [
            'email' => $email,
            'pwd' => $pwd
        ];

        return $errors;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $value
     * @return bool|mixed
     */
    protected function validateEmail($value)
    {
        $value = trim($value);

        if (strlen($value) > 150) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param $value
     * @return bool|mixed
     */
    protected function validatePassword($value)
    {
        $value = trim($value);

        if (strlen($value) < 6 || strlen($value) > 70) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_REGEXP, ["options" => ['regexp' => '/^[A-Za-z0-9!&\.]+$/']]);
    }
}
