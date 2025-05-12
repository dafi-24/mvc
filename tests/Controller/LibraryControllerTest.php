<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Library;
use Doctrine\ORM\EntityManagerInterface;

class LibraryControllerTest extends WebTestCase
{
    private EntityManagerInterface $entitymanager;
    private $client;
    private string $testTitle;
    private string $updatedTitle;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entitymanager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->testTitle = 'Testbok_' . uniqid();
        $this->updatedTitle = 'Updated_' . uniqid();
    }

    public function testIndexPageLoads(): void
    {
        $this->client->request('GET', '/library');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Biblioteket');
    }

    public function testListPageLoads(): void
    {
        $this->client->request('GET', '/library/list');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table.library-table');
    }

    public function testShowRedirectsIfBookNotFound(): void
    {
        $this->client->request('GET', '/library/show/99999');
        $this->assertResponseRedirects('/library/list');
    }

    public function testCreateFormLoads(): void
    {
        $this->client->request('GET', '/library/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form.book-form');
    }

    public function testCreateBook(): void
    {
        $crawler = $this->client->request('GET', '/library/create');
        $form = $crawler->selectButton('Lägg till bok')->form([
            'titel' => $this->testTitle,
            'isbn'  => '1234567890',
            'author'=> 'Testförfattare',
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/library/list');

        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', $this->testTitle);
    }

    public function testShowBook(): void
    {
        $book = $this->createTestBook();
        $id = $book->getId();

        $this->client->request('GET', "/library/show/{$id}");
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', $this->testTitle);
        $this->assertSelectorTextContains('body', 'Testförfattare');
    }

    public function testEditBook(): void
    {
        $book = $this->createTestBook();
        $id = $book->getId();

        $crawler = $this->client->request('GET', "/library/edit/{$id}");
        $form = $crawler->selectButton('Uppdatera bok')->form([
            'titel' => $this->updatedTitle,
            'isbn'  => '1234567890',
            'author'=> 'Ny Författare',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects("/library/show/{$id}");

        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', $this->updatedTitle);
    }

    public function testDeleteBook(): void
    {
        $book = $this->createTestBook();
        $id = $book->getId();

        $this->client->request('POST', "/library/delete/{$id}", ['_method' => 'DELETE']);
        $this->assertResponseRedirects('/library/list');
        $this->client->followRedirect();
        $this->assertStringNotContainsString($this->testTitle, $this->client->getResponse()->getContent());

        $this->entitymanager->clear();
        $deleted = $this->entitymanager->getRepository(Library::class)->find($id);
        $this->assertNull($deleted);
    }

    private function createTestBook(): Library
    {
        $crawler = $this->client->request('GET', '/library/create');
        $form = $crawler->selectButton('Lägg till bok')->form([
            'titel' => $this->testTitle,
            'isbn'  => '1234567890',
            'author'=> 'Testförfattare',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/library/list');
        $this->client->followRedirect();

        $this->entitymanager->clear();
        return $this->entitymanager->getRepository(Library::class)
            ->findOneBy(['titel' => $this->testTitle]);
    }
}