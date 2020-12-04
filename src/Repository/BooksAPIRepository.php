<?php

namespace Szkolenie\Repository;

class BooksAPIRepository implements BooksRepositoryInterface
{
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    /**
     * Pobieranie listy ksiazek
     * @return mixed
     * @throws \Exception
     */
    public function getList()
    {
        return $this->getCurlResponse(
            $this->createCurl('books')
        );
    }

    /**
     * Pobieranie pojedynczej ksiazki
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function getBookById(int $id)
    {
        return $this->getCurlResponse(
            $this->createCurl('books/'.$id)
        );
    }

    /**
     * Dodawanie nowej ksiazki
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function addBook(array $data)
    {
        return $this->getCurlResponse(
            $this->createCurl('book/new', $data, self::METHOD_POST)
        );
    }

    /**
     * Edycja wybranej ksiazki
     * @param int $id Identyfikator ksiazki
     * @param array $data Dane do edycji
     * @return mixed
     * @throws \Exception
     */
    public function editBook(int $id, array $data)
    {
        return $this->getCurlResponse(
            $this->createCurl('books/edit/'.$id, $data, self::METHOD_PUT)
        );
    }

    /**
     * Usuwa wybrana ksiazke
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function removeBook(int $id)
    {
        return $this->getCurlResponse(
            $this->createCurl('books/remove/'.$id, [], self::METHOD_DELETE)
        );
    }

    /**
     * @param $curl
     * @return mixed
     * @throws \Exception
     */
    private function getCurlResponse($curl)
    {
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($response) {
            return json_decode($response);
        } else {
            var_dump($err);
            throw new \Exception('Nie udało się pobrać danych ;(');
        }
    }

    /**
     * @return string
     */
    private function createBasiAuthHeader()
    {
        return base64_encode($_ENV['REST_API_USER'].':'.$_ENV['REST_API_PWD']);
    }

    /**
     * @param string $uri URI do zasobu w API
     * @param string $method Metoda HTTP dla żądania, domyslnie GET
     * @return resource
     */
    private function createCurl(string $uri, array $postData = [], string $method = 'GET')
    {
        $curl = curl_init();

        $params = [
            CURLOPT_URL => $_ENV['REST_API_HOST'] . $uri,
            CURLOPT_RETURNTRANSFER => true, // obowiazkowy
            CURLOPT_TIMEOUT => 5, // warto ustawic cos niskiego aby polaczenie nie wisialo ze dlugo
            CURLOPT_FOLLOWLOCATION => true, // false - nie idzie za przekierowaniami
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic ' . $this->createBasiAuthHeader() // basic authentication
            )
        ];

        if (in_array($method, ['POST', 'PUT']) && !empty($postData)) {
            $params[CURLOPT_POSTFIELDS] = json_encode($postData); // CURLOPT_POSTFIELDS jest obowiazkowe przy POST i PUT do przeslania danych
        }

        curl_setopt_array($curl, $params);

        return $curl;
    }
}
