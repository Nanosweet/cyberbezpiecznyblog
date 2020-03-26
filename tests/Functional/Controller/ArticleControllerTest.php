<?php


namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    public function testShowPost()
    {
        $client = static::createClient();

        $client->request('GET', '/article/apple-rezygnuje-z-pomyslu-szyfrowania-kopii-zapasowych-zawartosci-iphoneow');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}