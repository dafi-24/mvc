<?php

namespace App\Controller;

use App\Entity\Library;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\LibraryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

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
            return $this->redirectToRoute('library_list');
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
            $book->setTitel((string) ($data->get('titel') ?? ''));
            $book->setIsbn((string) ($data->get('isbn') ?? ''));
            $book->setAuthor((string) ($data->get('author') ?? ''));

            $book->setImageUrl('default.jpg');

            /** @var UploadedFile $imageFile */
            $imageFile = $request->files->get('imageFile');
            // @phpstan-ignore-next-line
            if ($imageFile && $imageFile->isValid()) {
                // @phpstan-ignore-next-line
                $uploadsDir = (string) $this->getParameter('kernel.project_dir') . '/public/uploads';
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($uploadsDir, $newFilename);
                    $book->setImageUrl($newFilename);
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }
            }

            $entitymanager = $doctrine->getManager();
            $entitymanager->persist($book);
            $entitymanager->flush();

            return $this->redirectToRoute('library_list');
        }

        return $this->render('library/form.html.twig', [
            'action'      => 'Lägg till bok',
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
            $book->setTitel((string) ($data->get('titel') ?? ''));
            $book->setIsbn((string) ($data->get('isbn') ?? ''));
            $book->setAuthor((string) ($data->get('author') ?? ''));

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
        $entitymanager = $doctrine->getManager();
        $book = $repo->find($id);
        if ($book) {
            $entitymanager->remove($book);
            $entitymanager->flush();
        }
        return $this->redirectToRoute('library_list');
    }
}
