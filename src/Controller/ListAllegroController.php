<?php

namespace Szkolenie\Controller;

use Szkolenie\Service\AllegroApi\AccessTokenService;

class ListAllegroController extends AbstractAllegroAPIController
{
    public function index()
    {
        $items = [];

        /*
         * Jesli przekazano w URL parametr code (czyli przyszla zwrotka z API Allegro) zamien code na access token.
         * Jesli nie ma parametru code w URL (czyli zwykle wyswietlenie strony w aplikacji) popros o acess token zapamietany w sesji
         */
        $accessToken = $this->getAccessTokenWithAuthorizationCodeGrant(isset($_GET['code'])? $_GET['code'] : null);

        if ($accessToken) {
            // istnieje access token wiec pobierz liste produktow
            $authUrl = false;
            $response = $this->getAllegroAPIService($accessToken)->getOffersListing();

            if (isset($response->items)) {
                $items = $response->items->promoted;
            }

        } else {
            // nie ma access tokena w sesji wiec trzeba wyswietlic przycisk. Najpierw trzeba zbudowac URL do API
            $authUrl = AccessTokenService::getAuthUrlForAuthorizationCodeGrant();

        }

        return $this->template('ListAllegro/index.html', ['items' => $items, 'grantType' => 'Authorization Code', 'authUrl' => $authUrl]);
    }

    public function pkce()
    {
        // wygeneruj code verifier
        $_SESSION['pkce_code_verifier'] ??= AccessTokenService::generateCodeVerifier();

        /*
         * Jesli przekazano w URL parametr code (czyli przyszla zwrotka z API Allegro) zamien code na access token.
         * Jesli nie ma parametru code w URL (czyli zwykle wyswietlenie strony w aplikacji) popros o acess token zapamietany w sesji
         */
        $accessToken = $this->getAccessTokenWithAuthorizationCodePkceGrant($_SESSION['pkce_code_verifier'], isset($_GET['code'])? $_GET['code'] : null);

        if ($accessToken) {
            // istnieje access token wiec pobierz liste produktow
            $authUrl = false;
            $items = $this->getAllegroAPIService($accessToken)->getOffersListing()->items->promoted;

        } else {
            // nie ma access tokena w sesji wiec trzeba wyswietlic przycisk. Najpierw trzeba zbudowac URL do API razem z code challenge
            $authUrl = AccessTokenService::getAuthUrlForAuthorizationCodePKCEGrant(AccessTokenService::getCodeChallenge($_SESSION['pkce_code_verifier']));
            $items = [];
        }

        return $this->template('ListAllegro/index.html', ['items' => $items, 'grantType' => 'Authorization Code (PKCE)', 'authUrl' => $authUrl]);
    }
}
