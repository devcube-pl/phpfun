<?php

namespace Szkolenie\Controller;

use Szkolenie\Repository\BooksAPIGuzzleRepository;
use Szkolenie\Repository\BooksRepositoryInterface;
use Szkolenie\Validation\BookValidator;

class ListApiController extends AbstractController implements BooksAPIControllerInterface
{
    /**
     * @var BooksRepositoryInterface
     */
    private $apiRepo;

    /**
     * @var string
     */
    private $libName;

    /**
     * @param BooksRepositoryInterface $booksRepository
     */
    public function setBooksRepository(BooksRepositoryInterface $booksRepository)
    {
        $this->apiRepo = $booksRepository;
        // ta nazwa ponizej jest tylko po to zeby wyswietlic ją w szablonie
        $this->libName = ($booksRepository instanceof BooksAPIGuzzleRepository)? 'Guzzle' : 'cURL';
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        $items = $this->apiRepo->getList();

        return $this->template('ListApi/index.html', ['items' => $items, 'libname' => $this->libName]);
    }

    /**
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function item(int $id)
    {
        $item = $this->apiRepo->getBookById($id);

        return $this->template('ListApi/item.html', ['item' => $item]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function add()
    {
        if (isset($_POST['submit'])) {
            return $this->submitAdd();
        }

        // jest to GET wiec wyswietl formularz
        return $this->template('ListApi/add.html');
    }

    /**
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function edit(int $id)
    {
        $item = $this->apiRepo->getBookById($id);

        if (isset($_POST['submit'])) {
            return $this->submitEdit($id);
        }

        // jest to GET wiec wyswietl formularz
        return $this->template('ListApi/edit.html', ['item' => $item]);
    }

    /**
     * Usuwa ksiazke
     * @param int $id
     */
    public function remove(int $id)
    {
        $this->apiRepo->removeBook($id);
        $this->addSuccessFlashMessage('Książka została usunięta');
        $this->redirect('/lista-api');
    }

    /**
     * Obsluguje wyslany formularz dodawania
     * @return string
     * @throws \Exception
     */
    private function submitAdd()
    {
        $errors = [];
        $validator = new BookValidator();
        $isValid = $validator->validate();

        if ($isValid === true) {
            $this->apiRepo->addBook($validator->getData());

            $this->addSuccessFlashMessage('Książka została dodana!');
            $this->redirect('/lista-api');

        } else {
            $errors = $isValid;
        }

        return $this->template('ListApi/add.html', ['errors' => $errors]);
    }

    /**
     * Obsluguje wyslane formularz edycji
     * @param int $id
     * @return string
     * @throws \Exception
     */
    private function submitEdit(int $id)
    {
        $errors = [];
        $validator = new BookValidator();
        $isValid = $validator->validate();

        if ($isValid === true) {
            $this->apiRepo->editBook($id, $validator->getData());

            $this->addSuccessFlashMessage('Dane książki zostały zmienione!');
            $this->redirect('/lista-api/'.$id);

        } else {
            $errors = $isValid;
        }

        return $this->template('ListApi/edit.html', ['errors' => $errors]);
    }
}
