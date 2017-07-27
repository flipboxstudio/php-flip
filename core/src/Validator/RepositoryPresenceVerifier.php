<?php

namespace Core\Validator;

use Core\Contracts\Container as ContainerContract;
use Illuminate\Validation\PresenceVerifierInterface;

class RepositoryPresenceVerifier implements PresenceVerifierInterface
{
    protected $container;

    protected $connection;

    public function __construct(ContainerContract $container)
    {
        $this->container = $container;
    }

    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param string $collection
     * @param string $column
     * @param string $value
     * @param int    $excludeId
     * @param string $idColumn
     * @param array  $extra
     *
     * @return int
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $repository = $this->container->make($collection);
        $criteria = [[$column, '=', $value]];

        if (!is_null($excludeId) && $excludeId != 'NULL') {
            $criteria[] = [$idColumn ?: $repository->primaryKey(), '<>', $excludeId];
        }

        return $repository->count($criteria);
    }

    /**
     * Count the number of objects in a collection with the given values.
     *
     * @param string $collection
     * @param string $column
     * @param array  $values
     * @param array  $extra
     *
     * @return int
     */
    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {
        $repository = $this->container->make($collection);

        $criteria = [[$column, '=', $values]];

        return $repository->count($criteria);
    }

    /**
     * Set the connection to be used.
     *
     * @param string $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }
}
