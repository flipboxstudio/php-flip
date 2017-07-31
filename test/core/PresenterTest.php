<?php

namespace Test\Core;

use Test\TestCase;
use Test\Core\Models\TestModel;
use Core\Transformer\Transformer;
use Test\Core\Autobots\TestAutobot;
use Core\Util\Presenters\XMLPresenter;
use Core\Util\Presenters\JSONPresenter;

class PresenterTest extends TestCase
{
    public function testPresenter()
    {
        $transformer = $this->core->ioc(Transformer::class);

        $transformer->register(
            TestModel::class,
            TestAutobot::class
        );

        $model = new TestModel();

        $model->set('id', '123');
        $model->set('name', 'Anu Gemes');

        $transformed = $transformer->transform($model);

        $this->assertTrue(
            method_exists($transformed, 'using'),
            'Should have `using` method to set presenter class.'
        );

        $this->assertTrue(
            method_exists($transformed, 'present'),
            'Should have `present` method to present an object to something.'
        );

        $this->assertEquals(
            $transformed->using(JSONPresenter::class)->present(),
            json_encode(['id' => 123, 'name' => 'Anu Gemes']),
            'Presenter should be able to produce valid JSON.'
        );

        libxml_use_internal_errors(true);

        $this->assertTrue(
            simplexml_load_string(
                $transformed->using(XMLPresenter::class)->present()
            ) !== false,
            'Presenter should be able to produce valid XML.'
        );
    }
}
