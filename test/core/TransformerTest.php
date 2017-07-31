<?php

namespace Test\Core;

use Test\TestCase;
use Core\Util\Data\Fluent;
use Core\Util\Data\Collection;
use Test\Core\Models\TestModel;
use Core\Transformer\Transformer;
use Test\Core\Autobots\TestAutobot;
use Test\Core\Models\AdvanceTestModel;
use Test\Core\Autobots\SortedTestAutobot;
use Test\Core\Autobots\StaticTestAutobot;
use Test\Core\Autobots\AdvanceTestAutobot;

class TransformerTest extends TestCase
{
    protected function createTransformer()
    {
        return $this->core->ioc(Transformer::class);
    }

    protected function createTransformedObject(Transformer $transformer, string $modelFqn, array $attributes)
    {
        $transformer = $transformer ?? $this->createTransformer();

        $model = $this->createModel($modelFqn, $attributes);

        return $transformer->transform($model);
    }

    protected function createModel(string $modelFqn, array $attributes)
    {
        $model = new $modelFqn();

        foreach ($attributes as $property => $value) {
            $model->set($property, $value);
        }

        return $model;
    }

    public function testTransformerApi()
    {
        $transformer = $this->createTransformer();

        $this->assertInstanceOf(
            Transformer::class,
            $transformer,
            'Implementation of '.Transformer::class.' is not an instance of '.Transformer::class.'.'
        );

        $this->assertTrue(
            method_exists($transformer, 'register'),
            'Transformer should be able to add it\'s registrar on the fly.'
        );

        $this->assertTrue(
            method_exists($transformer, 'transform'),
            'Transformer should be able to transform an object into something.'
        );
    }

    public function testBasicTransformation()
    {
        $transformer = $this->createTransformer();

        $transformer->register(
            TestModel::class,
            TestAutobot::class
        );

        $transformed = $this->createTransformedObject($transformer, TestModel::class, [
            'id' => '123',
            'name' => 'Anu Gemes',
            'hidden' => 'SecretPassword',
        ]);

        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes'],
            $transformed->toArray(),
            'Is valid data structure.'
        );
    }

    public function testOverrideAutobot()
    {
        $transformer = $this->createTransformer();

        // It's registered
        $transformer->register(
            TestModel::class,
            TestAutobot::class
        );

        // Override using closure, should work on other method
        $transformer->once(
            TestModel::class,
            function (TestModel $model) {
                return new Fluent([
                    'id' => (int) $model->get('id'),
                    'name' => (string) $model->get('name'),
                    'override' => 'Ok.',
                ]);
            }
        );

        $transformed = $this->createTransformedObject($transformer, TestModel::class, [
            'id' => '123',
            'name' => 'Anu Gemes',
            'hidden' => 'SecretPassword',
        ]);

        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes', 'override' => 'Ok.'],
            $transformed->toArray(),
            'Is valid data structure.'
        );
    }

    public function testBasicTransformationFromClosure()
    {
        $transformer = $this->createTransformer();

        $transformer->register(
            TestModel::class,
            function (TestModel $model) {
                return new Fluent([
                    'id' => (int) $model->get('id'),
                    'name' => (string) $model->get('name'),
                ]);
            }
        );

        $transformed = $this->createTransformedObject($transformer, TestModel::class, [
            'id' => '123',
            'name' => 'Anu Gemes',
            'hidden' => 'SecretPassword',
        ]);

        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes'],
            $transformed->toArray(),
            'Is valid data structure.'
        );
    }

    public function testBasicTransformationFromStaticMethod()
    {
        $transformer = $this->createTransformer();

        $transformer->register(
            TestModel::class,
            [StaticTestAutobot::class, 'transform']
        );

        $transformed = $this->createTransformedObject($transformer, TestModel::class, [
            'id' => '123',
            'name' => 'Anu Gemes',
            'hidden' => 'SecretPassword',
        ]);

        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes'],
            $transformed->toArray(),
            'Is valid data structure.'
        );
    }

    public function testAutoSortTransformation()
    {
        $transformer = $this->createTransformer();

        $transformer->register(
            TestModel::class,
            SortedTestAutobot::class
        );

        $transformed = $this->createTransformedObject($transformer, TestModel::class, [
            'z' => '3',
            'y' => '2',
            'x' => '1',
        ]);

        $this->assertEquals(
            ['x' => 1, 'y' => 2, 'z' => 3],
            $transformed->toArray(),
            'Is valid data structure.'
        );
    }

    public function testCollectionTransformation()
    {
        $transformer = $this->createTransformer();

        $transformer->register(
            TestModel::class,
            TestAutobot::class
        );

        $collection = new Collection();

        foreach (range(1, 3) as $id) {
            $model = $this->createModel(TestModel::class, [
                'id' => $id,
                'name' => 'Anu Gemes',
            ]);

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

    public function testTransformationPropertyNaming()
    {
        $transformer = $this->createTransformer();

        $transformer->register(
            AdvanceTestModel::class,
            AdvanceTestAutobot::class
        );

        $model = $this->createModel(AdvanceTestModel::class, [
            'id' => '123',
            'name' => 'Anu Gemes',
            'phone_number' => '6289123456789',
        ]);

        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes', 'phone_number' => '6289123456789'],
            $transformer->transform($model)->toArray(),
            'Is valid data structure.'
        );

        // Change naming strategy (just to make sure)
        TestAutobot::$namingStrategy = TestAutobot::NAMING_SNAKE;
        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes', 'phone_number' => '6289123456789'],
            $transformer->transform($model)->toArray(),
            'Is valid data structure.'
        );

        // Change naming strategy
        TestAutobot::$namingStrategy = TestAutobot::NAMING_CAMEL;
        $this->assertEquals(
            ['id' => 123, 'name' => 'Anu Gemes', 'phoneNumber' => '6289123456789'],
            $transformer->transform($model)->toArray(),
            'Is valid data structure.'
        );

        // Change naming strategy
        TestAutobot::$namingStrategy = TestAutobot::NAMING_STUDLY;
        $this->assertEquals(
            ['Id' => 123, 'Name' => 'Anu Gemes', 'PhoneNumber' => '6289123456789'],
            $transformer->transform($model)->toArray(),
            'Is valid data structure.'
        );
    }
}
