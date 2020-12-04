<?php

namespace Szkolenie\Controller;

use Szkolenie\Repository\BooksRepositoryInterface;

interface BooksAPIControllerInterface
{
    /**
     * @param BooksRepositoryInterface $booksRepository
     */
    public function setBooksRepository(BooksRepositoryInterface $booksRepository);
}
