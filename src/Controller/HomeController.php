<?php

namespace Szkolenie\Controller;

class HomeController extends AbstractController
{
    public function index()
    {
        return $this->template(
            'Home/index.html'
        );
    }
}
