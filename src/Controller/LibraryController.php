<?php

namespace App\Controller;

use App\Entity\Library;
use App\Repository\LibraryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
        ]);
    }

    #[Route('/library/list', name: 'library_list')]
    public function list(LibraryRepository $repo): Response
    {
        $books = $repo->findAll();
        return $this->render('library/list.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/library/show/{id}', name: 'library_show', requirements: ['id' => '\d+'])]
    public function show(LibraryRepository $repo, int $id): Response
    {
        $book = $repo->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Boken hittades ej');
        }
        return $this->render('library/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/library/create', name: 'library_create', methods: ['GET', 'POST'])]
    public function create(ManagerRegistry $doctrine, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request;
            $book = new Library();
            $book->setTitle($data->get('title'));
            $book->setIsbn($data->get('isbn'));
            $book->setAuthor($data->get('author'));
            $book->setImageUrl($data->get('imageUrl'));

            $em = $doctrine->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('library_list');
        }

        return $this->render('library/form.html.twig', [
            'action'      => 'Skapa bok',
            'book'        => null,
            'form_action' => $this->generateUrl('library_create'),
        ]);
    }

    #[Route('/library/edit/{id}', name: 'library_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(ManagerRegistry $doctrine, Request $request, LibraryRepository $repo, int $id): Response
    {
        $book = $repo->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Boken hittades ej');
        }

        if ($request->isMethod('POST')) {
            $data = $request->request;
            $book->setTitle($data->get('title'));
            $book->setIsbn($data->get('isbn'));
            $book->setAuthor($data->get('author'));
            $book->setImageUrl($data->get('imageUrl'));

            $doctrine->getManager()->flush();
            return $this->redirectToRoute('library_show', ['id' => $id]);
        }

        return $this->render('library/form.html.twig', [
            'action'      => 'Uppdatera bok',
            'book'        => $book,
            'form_action' => $this->generateUrl('library_edit', ['id' => $id]),
        ]);
    }

    #[Route('/library/delete/{id}', name: 'library_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(ManagerRegistry $doctrine, LibraryRepository $repo, int $id): Response
    {
        $em = $doctrine->getManager();
        $book = $repo->find($id);
        if ($book) {
            $em->remove($book);
            $em->flush();
        }
        return $this->redirectToRoute('library_list');
    }
}
