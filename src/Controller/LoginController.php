<?php

namespace Szkolenie\Controller;

use Szkolenie\Entity\User;
use Szkolenie\Repository\UserRepository;
use Szkolenie\Validation\LoginValidator;

class LoginController extends AbstractController
{
    public function index()
    {
        if (isset($_POST['submit'])) {
            return $this->submit();
        }

        return $this->template('Login/index.html', []);
    }

    public function logout()
    {
        $_SESSION['user'] = null;
        session_destroy(); // usuwa dane z sesji po wylogowaniu

        $this->addFlashMessage('info', 'Zostałeś wylogowany');
        $this->redirect('/');
    }

    private function submit()
    {
        $validator = new LoginValidator();
        $errors = $validator->validate();

        if ($errors === false) {
            $repo = new UserRepository($this->getDatabase());
            $user = $repo->getUserByEmail($validator->getData()['email']);

            if ($user instanceof User && $this->authenticateUser($validator->getData()['pwd'], $user->getPassword(), $user)) {
                $this->addFlashMessage('info', 'Zalogowano pomyślnie!');
                $this->redirect('/');
            } else {
                $this->addFlashMessage('warning', 'Podany email i/lub hasło jest nieprawidłowy');
            }
        }

        return $this->template('Login/index.html', [
            'errors' => $errors
        ]);
    }
}
