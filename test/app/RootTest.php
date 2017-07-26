<?php

namespace Test\App;

use Test\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RootTest extends TestCase
{
    public function testRoot()
    {
        $this->json('GET', '/')
             ->seeJson([
                'message' => 'You have arrived.'
             ]);
    }
}
