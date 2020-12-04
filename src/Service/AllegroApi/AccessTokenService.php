<?php

namespace Szkolenie\Service\AllegroApi;

class AccessTokenService
{
    public const RETURN_URI = 'http://localhost/lista-allegro';
    public const RETURN_URI_PKCE = 'http://localhost/lista-allegro-pkce';

    /**
     * @return string
     */
    public static function generateCodeVerifier()
    {
        return sha1(time().rand(1, 10000)) . md5(time().rand(1, 20000));
    }

    /**
     * @param string $codeVerifier
     * @return string
     */
    public static function getCodeChallenge(string $codeVerifier)
    {
        // BASE64URL-ENCODE(SHA256())
        $codeChallenge = hash('sha256', $codeVerifier, true); // surowa wersja 32 znaki

        $base64 = base64_encode($codeChallenge);

        $base64url = strtr($base64, '+/', '-_'); // zamieniamy + na - oraz / na _
        $base64url = rtrim($base64url, '='); // pozbywamy sie paddingu

        return $base64url;

        // ponizej zapis ten sam co wyzej tylko w jednej linii
        //return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function getWithClientCredentialsGrant(): string
    {
        return self::doRequest('grant_type=client_credentials');
    }

    /**
     * @param string $code
     * @return string
     * @throws \Exception
     */
    public static function getWithAuthorizationCodeGrant(string $code): string
    {
        $authUrl = 'grant_type=authorization_code&code=' . $code . '&redirect_uri=' . self::RETURN_URI;

        return self::doRequest($authUrl);
    }

    /**
     * @param string $code
     * @param string $codeVerifier
     * @return string
     * @throws \Exception
     */
    public static function getWithAuthorizationCodeWithPkceGrant(string $code, string $codeVerifier): string
    {
        $authUrl = 'grant_type=authorization_code&code=' . $code . '&redirect_uri=' . self::RETURN_URI_PKCE .
            '&code_verifier=' . $codeVerifier;

        return self::doRequest($authUrl, false);
    }

    /**
     * Zwraca URL dla przycisku "Zaloguj do Allegro"
     * @return string
     */
    public static function getAuthUrlForAuthorizationCodeGrant()
    {
        return $_ENV['ALLEGRO_AUTH_HOST'] .
            'auth/oauth/authorize?response_type=code&client_id=' . $_ENV['ALLEGRO_CLIENT_ID'] .
            '&redirect_uri=' . self::RETURN_URI;
    }

    /**
     * Zwraca URL dla przycisku "Zaloguj do Allegro" z wykorzystaniem PKCE
     * @return string
     */
    public static function getAuthUrlForAuthorizationCodePKCEGrant(string $codeChallenge)
    {
        return $_ENV['ALLEGRO_AUTH_HOST'] .
            'auth/oauth/authorize?response_type=code&client_id=' . $_ENV['ALLEGRO_CLIENT_ID'] .
            '&redirect_uri=' . self::RETURN_URI_PKCE .
            '&code_challenge_method=S256&code_challenge='.$codeChallenge .
            '&prompt=confirm';
    }

    /**
     * @param string $url
     * @param bool $auth
     * @return mixed
     * @throws \Exception
     */
    private static function doRequest(string $url, bool $auth = true)
    {
        $curl = curl_init($_ENV['ALLEGRO_AUTH_HOST'] . "auth/oauth/token?" . $url); // sesja curl

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($auth) {
            $base64 = base64_encode($_ENV['ALLEGRO_CLIENT_ID'].':'.$_ENV['ALLEGRO_CLIENT_SECRET']);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $base64]);
        }

        $tokenResult = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            $result = json_decode($tokenResult);

            if (isset($result->access_token)) {
                return $result->access_token;
            } else if (isset($result->error)) {
                throw new \Exception('Błąd: ' . $result->error .', '. $result->error_description);
            }
        } else {
            throw new \Exception('Błąd połączenia: ' . $err);
        }
    }
}
