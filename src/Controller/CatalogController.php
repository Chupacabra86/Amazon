<?php

namespace App\Controller;

use App\Repository\BooksRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CatalogController extends AbstractController
{
    #[Route('/catalog', name: 'catalog')]
    public function index(BooksRepository $booksRepository): Response
    {
        return $this->render('catalog/index.html.twig', [
            'books' => $booksRepository->findAll(),
        ]);
    }
}
