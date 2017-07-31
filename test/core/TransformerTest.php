<?php

namespace Test\Core;

use Test\TestCase;
use Core\Util\Data\Collection;
use Test\Core\Models\TestModel;
use Core\Transformer\Transformer;
use Test\Core\Autobots\TestAutobot;
use Test\Core\Models\AdvanceTestModel;
use Test\Core\Autobots\SortedTestAutobot;
use Test\Core\Autobots\AdvanceTestAutobot;

class TransformerTest extends TestCase
{
    public function testBasicTransformation()
    {
        $transformer = $this->core->ioc(Transformer::class);

        $this->assertInstanceOf(
            Transformer::class,
            $transformer,
            'Implementation of '.Transformer::class.' is not an instance of '.Transformer::class.'.'
        );

        $transformer->register(
            TestModel::class,
            TestAutobot::class
        );

        $model = new TestModel();

        $model->set('id', '123');
        $model->set('name', 'Anu Gemes');
        $model->set('hidden', 'SecretPassword');

        $transformed = $transformer->transform($model);

        $this->assertTrue(
            method_exists($transformed, 'toArray'),
            'Transformed should be convertable to array.'
        );

        $array = $transformed->toArray();

        $this->assertTrue(
            is_array($array),
            'Final transformation should be an array.'
        );

        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes'],
            $array,
            'Is valid data structure.'
        );
    }

    public function testAutoSortTransformation()
    {
        $transformer = $this->core->ioc(Transformer::class);

        $transformer->register(
            TestModel::class,
            SortedTestAutobot::class
        );

        $model = new TestModel();

        $model->set('z', '3');
        $model->set('y', '2');
        $model->set('x', '1');

        $transformed = $transformer->transform($model);

        $array = $transformed->toArray();

        $this->assertEquals(
            ['x' => 1, 'y' => 2, 'z' => 3],
            $array,
            'Is valid data structure.'
        );
    }

    public function testCollectionTransformation()
    {
        $transformer = $this->core->ioc(Transformer::class);

        $transformer->register(
            TestModel::class,
            TestAutobot::class
        );

        $collection = new Collection();

        foreach (range(1, 3) as $id) {
            $model = new TestModel();

            $model->set('id', $id);
            $model->set('name', 'Anu Gemes');

            $collection->push($model);
        }

        $transformed = $transformer->transform($collection);

        $array = $transformed->toArray();

        $this->assertEquals(
            [
                ['id' => 1, 'name' => 'Anu Gemes'],
                ['id' => 2, 'name' => 'Anu Gemes'],
                ['id' => 3, 'name' => 'Anu Gemes'],
            ],
            $array,
            'Is valid data structure.'
        );
    }

    public function testAdvanceTransformation()
    {
        $transformer = $this->core->ioc(Transformer::class);

        $transformer->register(
            AdvanceTestModel::class,
            AdvanceTestAutobot::class
        );

        $model = new AdvanceTestModel();

        $model->set('id', '123');
        $model->set('name', 'Anu Gemes');
        $model->set('phone_number', '6289123456789');

        $transformed = $transformer->transform($model);
        $array = $transformed->toArray();

        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes', 'phone_number' => '6289123456789'],
            $array,
            'Is valid data structure.'
        );

        // Change naming strategy (just to make sure)
        TestAutobot::$namingStrategy = TestAutobot::NAMING_SNAKE;

        $transformed = $transformer->transform($model);
        $array = $transformed->toArray();

        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes', 'phone_number' => '6289123456789'],
            $array,
            'Is valid data structure.'
        );

        // Change naming strategy
        TestAutobot::$namingStrategy = TestAutobot::NAMING_CAMEL;

        $transformed = $transformer->transform($model);
        $array = $transformed->toArray();

        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes', 'phoneNumber' => '6289123456789'],
            $array,
            'Is valid data structure.'
        );

        // Change naming strategy
        TestAutobot::$namingStrategy = TestAutobot::NAMING_STUDLY;

        $transformed = $transformer->transform($model);
        $array = $transformed->toArray();

        $this->assertEquals(
            ['Id' => 123, 'Name' => 'Anu Gemes', 'PhoneNumber' => '6289123456789'],
            $array,
            'Is valid data structure.'
        );
    }
}
