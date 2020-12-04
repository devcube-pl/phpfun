<?php

namespace Szkolenie\Controller;

use Szkolenie\Service\AllegroApi\AccessTokenService;
use Szkolenie\Service\AllegroAPIService;

class AbstractAllegroAPIController extends AbstractController
{
    private $api;

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getAccessTokenWithClientCredentialsGrant()
    {
        // jesli nie ma w sesji tokena client_credentials pobierz go
        $_SESSION['allegro_access_token_cc'] ??= AccessTokenService::getWithClientCredentialsGrant();

        // zwroc token z sesji
        return $_SESSION['allegro_access_token_cc'];
    }

    /**
     * @param string|null $code
     * @return bool|string
     * @throws \Exception
     */
    protected function getAccessTokenWithAuthorizationCodeGrant(string $code = null)
    {
        if ($code) {
            // przekazano code wiec pobierz access token i zapamietaj w sesji
            $_SESSION['allegro_access_token_ac'] = AccessTokenService::getWithAuthorizationCodeGrant($code);
        } else if (!isset($_SESSION['allegro_access_token_ac'])) {
            // jesli nie ma sesji i nie ma code wiec trzeba wyswietlic przycisk - tutaj nie rob nic
            return false;
        }

        // zwroc token z sesji
        return $_SESSION['allegro_access_token_ac'];
    }

    /**
     * @param string $codeVerifier
     * @param string|null $code
     * @return bool|string
     * @throws \Exception
     */
    protected function getAccessTokenWithAuthorizationCodePkceGrant(string $codeVerifier, string $code = null)
    {
        if ($code) {
            // przekazano code wiec pobierz access token i zapamietaj w sesji
            $_SESSION['allegro_access_token_ac_pkce'] = AccessTokenService::getWithAuthorizationCodeWithPkceGrant(
                $code,
                $codeVerifier
            );
        } else if (!isset($_SESSION['allegro_access_token_ac_pkce'])) {
            // jesli nie ma sesji i nie ma code wiec trzeba wyswietlic przycisk - tutaj nie rob nic
            return false;
        }

        // zwroc token z sesji
        return $_SESSION['allegro_access_token_ac_pkce'];
    }

    /**
     * @param string $accessToken
     * @return AllegroAPIService
     */
    protected function getAllegroAPIService(string $accessToken): AllegroAPIService
    {
        if (!$this->api instanceof AllegroAPIService) {
            $this->api = new AllegroAPIService($accessToken);
        }

        return $this->api;
    }
}
