<?php

namespace App\Controller;

use App\Entity\Library;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\LibraryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * LibraryController hanterar CRUD-operationer för bibliotekets böcker.
 * Inkluderar visning, listning, skapande, uppdatering och borttagning.
 */
class LibraryController extends AbstractController
{
    /**
     * Visar huvudvy för biblioteket.
     *
     * @return Response Renderad Twig-mall för bibliotekets startsida
     */
    #[Route('/library', name: 'app_library')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
        ]);
    }

    /**
     * Visar en lista över alla böcker i biblioteket.
     *
     * @param LibraryRepository $repo Repository för Library-entity
     * @return Response Renderad Twig-mall med alla böcker
     */
    #[Route('/library/list', name: 'library_list')]
    public function list(LibraryRepository $repo): Response
    {
        $books = $repo->findAll();
        return $this->render('library/list.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * Visar detaljer för en specifik bok.
     * Om boken saknas omdirigeras användaren till listan.
     *
     * @param LibraryRepository $repo Repository för Library-entity
     * @param int $id ID för boken
     * @return Response Renderad Twig-mall med bokdetaljer eller omdirigering
     */
    #[Route('/library/show/{id}', name: 'library_show', requirements: ['id' => '\\d+'])]
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

    /**
     * Skapar en ny bok via formulär (GET visar formuläret, POST hanterar inskickad data).
     * Hanterar även filuppladdning för bokomslag.
     *
     * @param ManagerRegistry $doctrine Doctrine service för entitetsmanager
     * @param Request $request HTTP-request med formulärdata och fil
     * @return Response Renderad Twig-mall för formulär eller omdirigering till lista
     */
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

            /** @var UploadedFile|null $imageFile */
            // @phpstan-ignore-next-line
            $imageFile = $request->files->get('imageFile');
            // @phpstan-ignore-next-line
            if ($imageFile instanceof UploadedFile && $imageFile->isValid()) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($uploadsDir, $newFilename);
                    $book->setImageUrl($newFilename);
                } catch (FileException $e) {
                    // Logga eller hantera fel vid filuppladdning
                }
            }

            $em = $doctrine->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('library_list');
        }

        return $this->render('library/form.html.twig', [
            'action'      => 'Lägg till bok',
            'book'        => null,
            'form_action' => $this->generateUrl('library_create'),
        ]);
    }

    /**
     * Uppdaterar en befintlig bok via formulär (GET visar formuläret, POST hanterar uppdatering).
     *
     * @param ManagerRegistry $doctrine Doctrine service för entitetsmanager
     * @param Request $request HTTP-request med formulärdata
     * @param LibraryRepository $repo Repository för Library-entity
     * @param int $id ID för boken som ska uppdateras
     * @return Response Renderad Twig-mall för formulär eller omdirigering till visning
     */
    #[Route('/library/edit/{id}', name: 'library_edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
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

    /**
     * Tar bort en bok från biblioteket.
     *
     * @param ManagerRegistry $doctrine Doctrine service för entitetsmanager
     * @param LibraryRepository $repo Repository för Library-entity
     * @param int $id ID för boken som ska tas bort
     * @return Response Omdirigering till boklistan
     */
    #[Route('/library/delete/{id}', name: 'library_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
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
