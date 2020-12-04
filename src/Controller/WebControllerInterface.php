<?php

namespace Szkolenie\Controller;

use Twig\Environment as TwigTemplateEngine;

interface WebControllerInterface
{
    /**
     * @param TwigTemplateEngine $engine
     */
    public function setTemplateEngine(TwigTemplateEngine $engine);

    /**
     * @param string $filename
     * @param array $params
     */
    public function template(string $filename, array $params = []);

    /**
     * @param \PDO $pdo
     */
    public function setDatabaseConnection(\PDO $pdo);

    /**
     * @return \PDO
     */
    public function getDatabase();
}
