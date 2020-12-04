<?php

namespace Szkolenie\Controller;

use Szkolenie\Entity\User;
use Szkolenie\Repository\UserRepository;
use Szkolenie\Validation\SignUpValidator;

class SignUpController extends AbstractController
{
    /**
     * @return mixed
     * @throws \Exception
     */
    public function index()
    {
        if (isset($_POST['submit'])) {
            return $this->submit();
        }

        return $this->template('SignUp/index.html', []);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function submit()
    {
        $validator = new SignUpValidator();
        $errors = $validator->validate();

        if ($errors !== false) {
            return $this->template('SignUp/index.html', [
                'errors' => $errors
            ]);
        }

        // utworz encje danych
        $user = new User();
        $user->setEmail($validator->getData()['email']);
        $user->setName($validator->getData()['name']);
        $user->setPassword($validator->getData()['pwd']);

        // utworz rekord w bazie
        $repo = new UserRepository($this->getDatabase());
        $repo->createUser($user);

        $this->addFlashMessage('success', 'Konto zostało utworzone! Teraz możesz się zalogować.');

        $this->redirect('/logowanie');
    }
}
