<?php

namespace Szkolenie\Validation;

class SignUpValidator extends LoginValidator
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
        $errors = parent::validate();

        $name = $this->validateName(filter_input(INPUT_POST, 'name'));

        if ($name == false) {
            $errors['name'] = 'Wpisz swoje imię';
        }

        $pwd = $this->validatePassword(filter_input(INPUT_POST, 'pwd'));
        $pwd2 = $this->validatePassword(filter_input(INPUT_POST, 'pwd2'));

        if ($pwd !== $pwd2) {
            $errors['pwd2'] = 'Podane hasła różnią się';
        }

        $this->data = parent::getData();
        $this->data['name'] = $name;

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
    protected function validateName($value)
    {
        $value = trim($value);

        if (strlen($value) < 2 || strlen($value) > 35) {
            return false;
        }

        return filter_var($value, FILTER_SANITIZE_STRING);
    }
}
