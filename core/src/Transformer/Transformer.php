<?php

namespace Core\Transformer;

use ReflectionClass;
use IteratorAggregate;
use Core\Util\Data\Fluent;
use Illuminate\Support\Arr;
use InvalidArgumentException;
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

    public function once(string $modelName, $autobotName): Transformer
    {
        $this->validateAutobot($autobotName);

        $this->once[$modelName] = $autobotName;

        return $this;
    }

    public function register(string $modelName, $autobotName): Transformer
    {
        $this->validateAutobot($autobotName);

        $this->autobots[$modelName] = $autobotName;

        return $this;
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
                'Data cannot be transformed. There is no Autobot assigned for this data type.',
                500,
                $data
            );
        }

        if (is_callable($Autobot)) {
            // Autobot is a closure, or any public method
            return call_user_func_array(
                $Autobot,
                [$data]
            );
        } elseif (is_string($Autobot)) {
            // Autobot is a string, we can create the instance using container.
            return $this->{$method}(
                $data,
                $this->container->make($Autobot)
            );
        } else {
            throw new TransformerException(
                'Data cannot be transformed. Cannot gather attributes from assigned Autobot.',
                500,
                $data
            );
        }
    }

    protected function validateAutobot($autobotName)
    {
        if (!is_string($autobotName) && !is_callable($autobotName)) {
            throw new InvalidArgumentException(
                'Autobot must be a class name (string) or something callable',
                500
            );
        }
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

    protected function determineAutobotFromCollection($collection)
    {
        foreach ($collection as $model) {
            return $this->determineAutobotFromModel($model);
        }

        return null;
    }

    protected function determineAutobotFromModel(ModelContract $model)
    {
        return $this->determineAutobotFromItsClassNameMapping(get_class($model))
            ?? $this->determineAutobotFromItsContract($model);
    }

    protected function determineAutobotFromItsClassNameMapping(string $modelFqn)
    {
        // Once is a priority
        if (array_key_exists($modelFqn, $this->once)) {
            $autobotName = $this->once[$modelFqn];

            unset($this->once[$modelFqn]);

            return $autobotName;
        }

        // Using interface mapping, interface to an autobot
        if (array_key_exists($modelFqn, $this->assigned)) {
            return $this->assigned[$modelFqn];
        }

        // Using direct mapping, classname to an autobot
        if (array_key_exists($modelFqn, $this->autobots)) {
            return $this->autobots[$modelFqn];
        }

        return null;
    }

    protected function determineAutobotFromItsContract(ModelContract $model)
    {
        if ($Autobot = $this->determineAutobotFromItsInterfaceMapping($model, $this->once)) {
            return $Autobot;
        }

        if ($Autobot = $this->determineAutobotFromItsInterfaceMapping($model, $this->assigned)) {
            return $Autobot;
        }

        if ($Autobot = $this->determineAutobotFromItsInterfaceMapping($model, $this->autobots)) {
            return $Autobot;
        }

        return null;
    }

    protected function determineAutobotFromItsInterfaceMapping(ModelContract $model, array $mapping)
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
        return $autobot->bind($model)->transform();
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
