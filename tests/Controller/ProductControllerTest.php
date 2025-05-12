<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductControllerTest extends WebTestCase
{
    private $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testIndexPageLoads(): void
    {
        $this->client->request('GET', '/product');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'ProductController');
    }

    public function testCreateProduct(): void
    {
        $this->client->request('GET', '/product/create');
        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertMatchesRegularExpression('/Saved new product with id \d+/', $content);
    }

    public function testShowAllProduct(): void
    {
        $product = new Product();
        $product->setName('TestProd');
        $product->setValue(123);
        $this->em->persist($product);
        $this->em->flush();

        $this->client->request('GET', '/product/show');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
        $names = array_column($data, 'name');
        $this->assertContains('TestProd', $names);
    }

    public function testShowProductById(): void
    {
        $product = new Product();
        $product->setName('SingleProd');
        $product->setValue(456);
        $this->em->persist($product);
        $this->em->flush();
        $id = $product->getId();

        $this->client->request('GET', "/product/show/{$id}");
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('SingleProd', $data['name']);
        $this->assertEquals(456, $data['value']);
    }

    public function testUpdateProduct(): void
    {
        $product = new Product();
        $product->setName('UpdateProd');
        $product->setValue(10);
        $this->em->persist($product);
        $this->em->flush();
        $id = $product->getId();

        $newValue = 999;
        $this->client->request('GET', "/product/update/{$id}/{$newValue}");
        $this->assertResponseRedirects('/product/show');

        $this->client->followRedirect();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $found = array_filter($data, fn($p) => $p['id'] === $id);
        $this->assertEquals($newValue, array_shift($found)['value']);
    }

    public function testDeleteProductById(): void
    {
        $product = new Product();
        $product->setName('DeleteProd');
        $product->setValue(20);
        $this->em->persist($product);
        $this->em->flush();
        $id = $product->getId();

        $this->client->request('GET', "/product/delete/{$id}");
        $this->assertResponseRedirects('/product/show');

        $this->em->clear();
        $deleted = $this->em->getRepository(Product::class)->find($id);
        $this->assertNull($deleted);
    }

    public function testViewAllProduct(): void
    {
        $product = new Product();
        $product->setName('ViewProd');
        $product->setValue(33);
        $this->em->persist($product);
        $this->em->flush();

        $this->client->request('GET', '/product/view');
        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('ViewProd', $content);
        $this->assertStringContainsString('33', $content);
    }
}
