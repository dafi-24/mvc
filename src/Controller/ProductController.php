<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ProductController hanterar CRUD-operationer för Product-entity.
 * Inkluderar skapande, visning, uppdatering och borttagning av produkter.
 */
final class ProductController extends AbstractController
{
    /**
     * Visar startsidan för Product-avsnittet.
     *
     * @return Response Renderad Twig-mall för produktstartsidan
     */
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    /**
     * Skapar en ny produkt med slumpmässigt namn och värde och sparar den i databasen.
     *
     * @param ManagerRegistry $doctrine Doctrine service för entitetsmanager
     * @return Response Textsvar med det nya produkt-ID:t
     */
    #[Route('/product/create', name: 'product_create')]
    public function createProduct(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $product = new Product();
        $product->setName('Keyboard_num_' . random_int(1, 9));
        $product->setValue(random_int(100, 999));

        $entityManager->persist($product);
        $entityManager->flush();

        return new Response('Saved new product with id ' . $product->getId());
    }

    /**
     * Returnerar alla produkter som JSON.
     *
     * @param ProductRepository $productRepository Repository för Product-entity
     * @return Response JSON-svar med alla produkter
     */
    #[Route('/product/show', name: 'product_show_all')]
    public function showAllProduct(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        $response = $this->json($products);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Returnerar en specifik produkt som JSON baserat på ID.
     *
     * @param ProductRepository $productRepository Repository för Product-entity
     * @param int $id ID för produkten
     * @return Response JSON-svar med produkten eller null om inte hittad
     */
    #[Route('/product/show/{id}', name: 'product_by_id')]
    public function showProductById(ProductRepository $productRepository, int $id): Response
    {
        $product = $productRepository->find($id);

        return $this->json($product);
    }

    /**
     * Tar bort en produkt baserat på ID och omdirigerar till visning av alla.
     *
     * @param ManagerRegistry $doctrine Doctrine service för entitetsmanager
     * @param int $id ID för produkten som ska tas bort
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException Om produkten inte hittas
     * @return Response Omdirigering till routen 'product_show_all'
     */
    #[Route('/product/delete/{id}', name: 'product_delete_by_id')]
    public function deleteProductById(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('product_show_all');
    }

    /**
     * Uppdaterar en produkts värde baserat på ID och nytt värde, sedan omdirigering.
     *
     * @param ManagerRegistry $doctrine Doctrine service för entitetsmanager
     * @param int $id ID för produkten som ska uppdateras
     * @param int $value Nytt värde för produkten
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException Om produkten inte hittas
     * @return Response Omdirigering till routen 'product_show_all'
     */
    #[Route('/product/update/{id}/{value}', name: 'product_update')]
    public function updateProduct(ManagerRegistry $doctrine, int $id, int $value): Response
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

        $product->setValue($value);
        $entityManager->flush();

        return $this->redirectToRoute('product_show_all');
    }

    /**
     * Visar alla produkter i en Twig-mall.
     *
     * @param ProductRepository $productRepository Repository för Product-entity
     * @return Response Renderad Twig-mall med alla produkter
     */
    #[Route('/product/view', name: 'product_view_all')]
    public function viewAllProduct(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/view.html.twig', [
            'products' => $products,
        ]);
    }
}
