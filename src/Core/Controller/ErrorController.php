<?php

namespace Szkolenie\Core\Controller;

class ErrorController
{
    public function error404()
    {
        http_response_code(404);
        return '404 - podana strona nie istnieje';
    }
}
