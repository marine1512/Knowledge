<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;

class ThemeControllerTest extends WebTestCase
{
    public function testDetail(): void
    {
        $client = static::createClient();
        $client->request('GET', '/product/1');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Test Theme', $response->getContent());
    }

    public function testLessons(): void
    {
        $client = static::createClient();

        $client->request('GET', '/cursus/1/lecon'); // Adjust this if necessary

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Lesson 1', $response->getContent());
        $this->assertStringContainsString('Lesson 2', $response->getContent());
    }

    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/theme');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('themes', $response->getContent());
    }

    public function testNew(): void
    {
        $client = static::createClient();

        $uploadedFile = new UploadedFile(
            '/path/to/image.jpg', // Use a real, valid image path
            'image.jpg'
        );

        $client->request('POST', '/new', [
            'nom' => 'New Theme'
        ], [
            'image' => $uploadedFile
        ]);

        $response = $client->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEdit(): void
    {
        $client = static::createClient();
        $client->request('POST', '/1/edit', ['nom' => 'Updated Theme']);

        $response = $client->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $client->request('POST', '/1/delete');

        $response = $client->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
    }
}