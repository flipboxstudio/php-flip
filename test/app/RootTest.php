<?php

namespace Test\App;

use Test\TestCase;

class RootTest extends TestCase
{
    public function testRoot()
    {
        $this->json('GET', '/')
             ->seeJson([
                'message' => 'You have arrived.',
             ]);
    }
}
