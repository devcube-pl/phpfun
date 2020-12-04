<?php

namespace Szkolenie\Service;

class AllegroAPIService
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * AllegroAPIService constructor.
     * @param string|null $accessToken
     */
    public function __construct(?string $accessToken = null)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getBookCategories()
    {
        return $this->getBody(
            $this->getRequest('sale/categories?parent.id=7')
        );
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getOffersListing()
    {
        return $this->getBody(
            $this->getRequest('offers/listing?category.id=7')
        );
    }

    /**
     * @param string $phrase
     * @return mixed
     * @throws \Exception
     */
    public function search(string $phrase)
    {
        return $this->getBody(
            $this->getRequest('offers/listing?phrase='.$phrase)
        );
    }

    /**
     * @param $curl
     * @return mixed
     * @throws \Exception
     */
    private function getBody($curl)
    {
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($response && !$err) {
            return json_decode($response);
        } else {
            throw new \Exception('Nie udało się pobrać danych ;(');
        }
    }

    /**
     * @param string $uri URI do zasobu w API
     * @param string $method Metoda HTTP dla żądania, domyslnie GET
     * @return resource
     */
    private function getRequest(string $uri, array $postData = [], string $method = 'GET')
    {
        $curl = curl_init($_ENV['ALLEGRO_API_HOST'] . $uri);

        $params = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5, // warto ustawic cos niskiego aby polaczenie nie wisialo ze dlugo
            CURLOPT_FOLLOWLOCATION => true, // false - nie idzie za przekierowaniami
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/vnd.allegro.public.v1+json'
            ]
        ];

        if (in_array($method, ['POST', 'PUT']) && !empty($postData)) {
            $params[CURLOPT_POSTFIELDS] = json_encode($postData); // CURLOPT_POSTFIELDS jest obowiazkowe przy POST i PUT do przeslania danych
        }

        curl_setopt_array($curl, $params);

        return $curl;
    }
}
