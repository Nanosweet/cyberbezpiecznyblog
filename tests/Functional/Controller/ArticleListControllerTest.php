<?php


namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleListControllerTest extends WebTestCase
{
    public function testArticleList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/list');
        $link = $crawler
            ->filter('a') // przeszukanie wszystkich linkow na stronie
            ->eq(5) // klikniecie w 5 link z listy
            ->link()
        ;


        $crawler = $client->click($link);


        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}