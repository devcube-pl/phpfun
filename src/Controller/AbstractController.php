<?php

namespace Szkolenie\Controller;

use Szkolenie\Core\Router\Router;
use Szkolenie\Entity\User;
use Twig\Environment as TwigTemplateEngine; // to jest alias TwigTemplateEngine dla przestrzeni nazw Twig\Environment

abstract class AbstractController implements WebControllerInterface
{
    /**
     * @var TwigTemplateEngine
     */
    private $template;

    /**
     * @var \PDO
     */
    private $database;

    /**
     * @param \PDO $pdo
     */
    public function setDatabaseConnection(\PDO $pdo)
    {
        $this->database = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param TwigTemplateEngine $engine
     */
    public function setTemplateEngine(TwigTemplateEngine $engine)
    {
        $this->template = $engine;
    }

    /**
     * @param string $filename
     * @param array $params
     */
    public function template(string $filename, array $params = []) : string
    {
        if (!isset($this->template)) {
            throw new \Exception('There is no template engine.');
        }

        return $this->template->render($filename, $params + $this->getTemplateVars());
    }

    /**
     * @param string $url
     */
    protected function redirect(string $url)
    {
        Router::getInstance()->redirect($url);
    }

    /**
     * @return User|bool
     */
    protected function getUser()
    {
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            if ($user instanceof User) {
                $user->clearPassword();
            }
            return $user;
        }

        return false;
    }

    /**
     * @param $formPassword
     * @param $dbPassword
     * @param User $user
     * @return bool
     */
    protected function authenticateUser($formPassword, $dbPassword, User $user) : bool
    {
        if (User::verifyPassword($formPassword, $dbPassword)) {
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    protected function isAuthenticated() : bool
    {
        return $this->getUser() !== false;
    }

    /**
     * @return mixed
     */
    protected function getFlashMessages()
    {
        $data = [];

        if (isset($_SESSION['flash_messages'])) {
            $data = $_SESSION['flash_messages'];
            $_SESSION['flash_messages'] = [];
        }
        return $data;
    }

    /**
     * @param $type
     * @param $message
     */
    protected function addFlashMessage($type, $message)
    {
        $_SESSION['flash_messages'] ??= [];
        $_SESSION['flash_messages'][$type] ??= [];
        $_SESSION['flash_messages'][$type][] = $message;
    }

    /**
     * @param string $message
     */
    protected function addSuccessFlashMessage(string $message)
    {
        $this->addFlashMessage('success', $message);
    }

    /**
     * @param string $message
     */
    protected function addErrorFlashMessage(string $message)
    {
        $this->addFlashMessage('danger', $message);
    }

    /**
     * @return array
     */
    private function getTemplateVars()
    {
        $user = $this->getUser();

        return [
            'app' => (object)[
                'flashes' => $this->getFlashMessages(),
                'currentRouteName' => Router::getInstance()->getCurrentRouteName(),
                'isAuthenticated' => ($user !== false),
                'user' => $user
            ]
        ];
    }
}
