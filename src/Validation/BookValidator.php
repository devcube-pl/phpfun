<?php

namespace Szkolenie\Validation;

class BookValidator
{
    /**
     * @var array
     */
    private $data;

    public function validate()
    {
        $errors = [];

        $title = $this->validateTitle($errors);
        $author = $this->validateAuthor($errors);
        $price = $this->validatePrice($errors);

        if (empty($errors)) {
            $this->data = [
                'title' => $title,
                'author' => $author,
                'price' => $price
            ];
            return true;
        }

        return $errors;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    protected function validatePrice(&$errors)
    {
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT, ['min_range' => 0, 'max_range' => 1000000]);

        if ($price === false) {
            $errors['price'] = 'Cena książki musi mieścić się w zakresie od zera do miliona';
        } elseif (empty($price)) {
            $errors['price'] = 'Podaj cenę książki';
        }

        return $price;
    }

    protected function validateTitle(&$errors)
    {
        $title = $this->getPOSTValue('title');
        if (strlen($title) > 100) {
            $errors['title'] = 'Podany tytuł jest zbyt długi';
        } elseif (empty($title)) {
            $errors['title'] = 'Podaj tytuł książki';
        }

        return $title;
    }

    /**
     * @param array $errors
     * @param string $fieldName
     */
    protected function validateAuthor(&$errors)
    {
        $author = $this->getPOSTValue('author');
        if (strlen($author) > 50) {
            $errors['author'] = 'Podana nazwa autora książki jest zbyt długa';
        } elseif (empty($author)) {
            $errors['author'] = 'Podaj autora książki';
        }

        return $author;
    }

    protected function getPOSTValue(string $fieldName)
    {
        return trim(filter_input(INPUT_POST, $fieldName));
    }
}
