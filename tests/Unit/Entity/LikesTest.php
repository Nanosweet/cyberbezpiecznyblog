<?php

namespace App\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Article;

class LikesTest extends TestCase
{
    public function setIncrementProvider()
    {
        return [
            'case 0' => [
                'likes' => 0,
                'expectedValue' => 1,
            ],
            'case 1' => [
                'likes' => 10,
                'expectedValue' => 11,
            ],
            'case 2' => [
                'likes' => 100,
                'expectedValue' => 101,
            ]
        ];
    }

    /**
     * @dataProvider setIncrementProvider
     */
    public function testIncrementLikes(int $likes, int $expectedValue)
    {
        $article = new Article();

        $article->setLikes($likes);
        $article->incrementLikes();

        $this->assertEquals($expectedValue, $article->getLikes());
    }
}