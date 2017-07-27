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

    protected $assigned = [];

    public function __construct(ContainerContract $container)
    {
        $this->container = $container;
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
            $Autobot = $this->determineAutobotFromIteratorAggregate($data);
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
        return $data instanceof IteratorAggregate;
    }

    protected function dataIsAnItem($data): bool
    {
        return $data instanceof ModelContract;
    }

    protected function determineAutobotFromIteratorAggregate(IteratorAggregate $collection): ?string
    {
        foreach ($collection->getIterator() as $model) {
            return $this->determineAutobotFromModel($model);
        }

        return null;
    }

    protected function determineAutobotFromModel(ModelContract $model): ?string
    {
        $modelFqn = get_class($model);

        if (array_key_exists($modelFqn, $this->assigned)) {
            return $this->assigned[$modelFqn];
        }

        $implements = array_intersect(
            array_keys((new ReflectionClass($modelFqn))->getInterfaces()),
            array_keys($this->autobots)
        );

        if (count($implements) > 0) {
            $firstMatchingContract = reset($implements);

            return $this->assigned[$modelFqn] = $this->autobots[$firstMatchingContract];
        }

        return null;
    }

    protected function transformItem(
        ModelContract $model,
        AutobotContract $autobot
    ): Fluent {
        return $autobot->transform($model);
    }

    protected function transformCollection(
        IteratorAggregate $collection,
        AutobotContract $autobot
    ): Collection {
        $result = new Collection();

        foreach ($collection as $model) {
            $result->push(
                $this->transformItem($model, $autobot)
            );
        }

        return $result;
    }
}
