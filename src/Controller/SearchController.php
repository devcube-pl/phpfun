<?php

namespace Szkolenie\Controller;

class SearchController extends AbstractAllegroAPIController
{
    public function index()
    {
        // pobierz szukana fraze z URL'a i przefiltruj
        $phrase = $this->getPhrase();

        // pobierz access token
        $accessToken = $this->getAccessTokenWithClientCredentialsGrant();

        // szukaj frazy w API
        $items = $this->getAllegroAPIService($accessToken)->search($phrase);

        if (isset($items->items)) {
            $items = $items->items->regular;
        }

        return $this->template('Search/index.html', ['items' => $items, 'phrase' => $phrase]);
    }

    /**
     * @return mixed
     */
    private function getPhrase()
    {
        $phrase = trim($_GET['phrase']);
        return filter_var($phrase, FILTER_SANITIZE_STRING);
    }
}
