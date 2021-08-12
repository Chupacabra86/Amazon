<?php

namespace App\Service;

use App\Entity\Books;

class BookPricerService
{
    public function computePrice(Books $book):void
    {
        $book->setPrice(strlen($book->getDescription()));
    }

}