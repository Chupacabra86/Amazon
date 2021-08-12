<?php

namespace App\Controller;

use App\Entity\Books;
use App\Form\BooksType;
use App\Service\BookPricerService;
use App\Repository\BooksRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BooksController extends AbstractController
{
    #[Route('/admin/books/', name: 'books_index', methods: ['GET'])]
    public function index(BooksRepository $booksRepository): Response
    {
        return $this->render('admin/books/index.html.twig', [
            'books' => $booksRepository->findAll(),
        ]);
    }

    #[Route('/admin/books/new', name: 'books_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BookPricerService $bookPricerService): Response
    {
        $book = new Books();
        $form = $this->createForm(BooksType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookPricerService->computePrice($book);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/books/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/admin/books/{id}', name: 'books_show', methods: ['GET'])]
    public function show(Books $book): Response
    {
        return $this->render('admin/books/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/admin/books/{id}/edit', name: 'books_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Books $book, BookPricerService $bookPricerService): Response
    {
        $form = $this->createForm(BooksType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookPricerService->computePrice($book);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/books/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/admin/books/{id}', name: 'books_delete', methods: ['POST'])]
    public function delete(Request $request, Books $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
    }

}
