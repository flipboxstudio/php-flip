<?php

namespace Core\Transformer;

use ReflectionClass;
use IteratorAggregate;
use Core\Util\Data\Fluent;
use Core\Util\Data\Collection;
use Core\Exceptions\TransformerException;
use Illuminate\Contracts\Support\Arrayable;
use Core\Contracts\Models\Model as ModelContract;
use Core\Contracts\Container as ContainerContract;
use Core\Transformer\Autobots\User as UserAutobot;
use Core\Transformer\Autobots\Token as TokenAutobot;
use Core\Contracts\Models\User as UserModelContract;
use Core\Contracts\Models\Token as TokenModelContract;
use Core\Contracts\Transformer\Autobot as AutobotContract;

class Transformer
{
    protected $container;

    protected $autobots = [
        // ModelContract::class => ModelAutobot::class
        UserModelContract::class => UserAutobot::class,
        TokenModelContract::class => TokenAutobot::class,
    ];

    public function __construct(ContainerContract $container)
    {
        $this->container = $container;
    }

    public function register(string $modelName, $autobotName)
    {
        $this->autobots[$modelName] = $autobotName;
    }

    public function transform($data): Arrayable
    {
        $Autobot = null;
        $method = null;

        if ($this->dataIsCollection($data)) {
            $Autobot = $this->determineAutobotFromIteratorAggregate($data);
            $method = 'transformCollection';
        } elseif ($this->dataIsItem($data)) {
            $Autobot = $this->determineAutobotFromModel($data);
            $method = 'transformItem';
        }

        if (!$Autobot || !$method) {
            throw new TransformerException(
                'Cannot transform type. There is no Autobot assigned for that type.',
                500
            );
        }

        return call_user_func_array(
            [$this, $method],
            [$data, $this->container->make($Autobot)]
        );
    }

    protected function dataIsCollection($data): bool
    {
        return $data instanceof IteratorAggregate;
    }

    protected function dataIsItem($data): bool
    {
        return $data instanceof ModelContract;
    }

    protected function determineAutobotFromIteratorAggregate(IteratorAggregate $models): ?string
    {
        foreach ($models->getIterator() as $model) {
            return $this->determineAutobotFromModel($model);
        }

        return null;
    }

    protected function determineAutobotFromModel(ModelContract $model): ?string
    {
        $implements = array_intersect(
            array_keys((new ReflectionClass($model))->getInterfaces()),
            array_keys($this->autobots)
        );

        if (count($implements) > 0) {
            return $this->autobots[
                reset($implements)
            ];
        }

        return null;
    }

    protected function transformItem(
        ModelContract $model,
        AutobotContract $autobot
    ): Fluent {
        if (!$autobot->canTransform($model)) {
            $autobotClass = get_class($autobot);

            throw new TransformerException("Autobot [{$autobotClass}] cannot transform that type.", 500);
        }

        return $autobot->transform($model);
    }

    protected function transformCollection(
        IteratorAggregate $models,
        AutobotContract $autobot
    ): Collection {
        $collection = new Collection();

        foreach ($models->getIterator() as $model) {
            $collection->push(
                $this->transformItem($model, $autobot)
            );
        }

        return $collection;
    }
}
