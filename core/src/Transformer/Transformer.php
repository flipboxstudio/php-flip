<?php

namespace Core\Transformer;

use ReflectionClass;
use IteratorAggregate;
use Core\Util\Data\Fluent;
use Illuminate\Support\Arr;
use Core\Util\Data\Collection;
use Core\Exceptions\TransformerException;
use Core\Transformer\Autobots\UserAutobot;
use Core\Transformer\Autobots\TokenAutobot;
use Illuminate\Contracts\Support\Arrayable;
use Core\Contracts\Models\Model as ModelContract;
use Core\Contracts\Container as ContainerContract;
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

    protected $assigned = [];

    protected $once = [];

    public function __construct(ContainerContract $container)
    {
        $this->container = $container;
    }

    public function once(string $modelName, string $autobotName): Transformer
    {
        $this->once[$modelName] = $autobotName;

        return $this;
    }

    public function register(string $modelName, string $autobotName)
    {
        $this->autobots[$modelName] = $autobotName;
    }

    public function transform($data): Arrayable
    {
        $Autobot = null;
        $method = null;

        if ($this->dataIsACollection($data)) {
            $Autobot = $this->determineAutobotFromCollection($data);
            $method = 'transformCollection';
        } elseif ($this->dataIsAnItem($data)) {
            $Autobot = $this->determineAutobotFromModel($data);
            $method = 'transformItem';
        }

        if (!$Autobot || !$method) {
            throw new TransformerException(
                'Data is not transformable. There is no Autobot assigned for this data type.',
                500,
                $data
            );
        }

        return call_user_func_array(
            [$this, $method],
            [$data, $this->container->make($Autobot)]
        );
    }

    protected function dataIsACollection($data): bool
    {
        return $data instanceof IteratorAggregate
            || is_array($data);
    }

    protected function dataIsAnItem($data): bool
    {
        return $data instanceof ModelContract;
    }

    protected function determineAutobotFromCollection($collection): ?string
    {
        foreach ($collection as $model) {
            return $this->determineAutobotFromModel($model);
        }

        return null;
    }

    protected function determineAutobotFromModel(ModelContract $model): ?string
    {
        return $this->determineAutobotFromItsClassNameMapping(get_class($model))
            ?? $this->determineAutobotFromItsContract($model);
    }

    protected function determineAutobotFromItsClassNameMapping(string $modelFqn): ?string
    {
        if (array_key_exists($modelFqn, $this->once)) {
            $autobotName = $this->once[$modelFqn];

            unset($this->once[$modelFqn]);

            return $autobotName;
        }

        if (array_key_exists($modelFqn, $this->autobots)) {
            return $this->autobots[$modelFqn];
        }

        if (array_key_exists($modelFqn, $this->assigned)) {
            return $this->assigned[$modelFqn];
        }

        return null;
    }

    protected function determineAutobotFromItsContract(ModelContract $model): ?string
    {
        if ($Autobot = $this->determineAutobotFromItsContractMapping($model, $this->once)) {
            return $Autobot;
        }

        if ($Autobot = $this->determineAutobotFromItsContractMapping($model, $this->autobots)) {
            return $Autobot;
        }

        return null;
    }

    protected function determineAutobotFromItsContractMapping(ModelContract $model, array $mapping): ?string
    {
        $implementedInterfaces = array_intersect(
            array_keys((new ReflectionClass($modelFqn = get_class($model)))->getInterfaces()),
            array_keys($mapping)
        );

        if (count($implementedInterfaces) > 0) {
            $firstMatchingContract = Arr::first($implementedInterfaces);

            return $this->assigned[$modelFqn] = $mapping[$firstMatchingContract];
        }

        return null;
    }

    protected function transformItem(ModelContract $model, AutobotContract $autobot): Fluent
    {
        return $autobot->transform($model);
    }

    protected function transformCollection(IteratorAggregate $collection, AutobotContract $autobot): Collection
    {
        $result = new Collection();

        foreach ($collection as $model) {
            $result->push($this->transformItem($model, $autobot));
        }

        return $result;
    }
}
