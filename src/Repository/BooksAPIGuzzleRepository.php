<?php

namespace Szkolenie\Repository;

use GuzzleHttp\Client;

class BooksAPIGuzzleRepository implements BooksRepositoryInterface
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client(
            [
                'base_uri' => $_ENV['REST_API_HOST'],
                'auth' => [ // basic authentication
                    $_ENV['REST_API_USER'],
                    $_ENV['REST_API_PWD']
                ]
            ]
        );
    }

    /**
     * Pobieranie listy ksiazek
     */
    public function getList()
    {
        $res = $this->client->request(self::METHOD_GET, 'books');
        return $this->getResponse($res);
    }

    /**
     * Pobieranie pojedynczej ksiazki
     */
    public function getBookById(int $id)
    {
        $res = $this->client->request(self::METHOD_GET, 'books/'.$id);
        return $this->getResponse($res);
    }

    /**
     * Dodawanie nowej ksiazki
     */
    public function addBook(array $data)
    {
        $res = $this->createDataRequest('book/new', $data);
        return $this->getResponse($res);
    }

    /**
     * Edycja wybranej ksiazki
     * @param int $id Identyfikator ksiazki
     * @param array $data Dane do edycji
     */
    public function editBook(int $id, array $data)
    {
        $res = $this->createDataRequest('books/edit/'.$id, $data, self::METHOD_PUT);
        return $this->getResponse($res);
    }

    /**
     * Usuwa wybrana ksiazke
     * @param int $id
     */
    public function removeBook(int $id)
    {
        $res = $this->createDataRequest('books/remove/'.$id, [], self::METHOD_DELETE);
        return $this->getResponse($res);
    }

    /**
     * @return object
     */
    private function getResponse($res)
    {
        /**
         * @var \GuzzleHttp\Psr7\Stream
         */
        $response = $res->getBody();
        return json_decode($response);
    }

    /**
     * @param string $uri
     * @param array $data
     */
    private function createDataRequest(string $uri, array $data, string $method = self::METHOD_POST)
    {
        return $this->client->request(
            $method,
            $uri,
            ['body' => json_encode($data)]
        );
    }
}
