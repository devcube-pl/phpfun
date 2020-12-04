<?php

namespace Szkolenie\Repository;

interface BooksRepositoryInterface
{
    /**
     * Pobieranie listy ksiazek
     */
    public function getList();

    /**
     * Pobieranie pojedynczej ksiazki
     */
    public function getBookById(int $id);

    /**
     * Dodawanie nowej ksiazki
     */
    public function addBook(array $data);

    /**
     * Edycja wybranej ksiazki
     * @param int $id Identyfikator ksiazki
     * @param array $data Dane do edycji
     */
    public function editBook(int $id, array $data);

    /**
     * Usuwa wybrana ksiazke
     * @param int $id
     */
    public function removeBook(int $id);
}
