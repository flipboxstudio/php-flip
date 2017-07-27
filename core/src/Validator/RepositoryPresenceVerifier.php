<?php

namespace Core\Validator;

use Core\Contracts\Container as ContainerContract;
use Illuminate\Validation\PresenceVerifierInterface;

class RepositoryPresenceVerifier implements PresenceVerifierInterface
{
    /**
     * Container instance.
     *
     * @var ContainerContract
     */
    protected $container;

    /**
     * Connection.
     *
     * @var string
     */
    protected $connection;

    /**
     * Initialize class.
     *
     * @param ContainerContract $container
     */
    public function __construct(ContainerContract $container)
    {
        $this->container = $container;
    }

    /**
     * Count the number of objects in a Repository having the given value.
     *
     * @param string $Repository
     * @param string $column
     * @param string $value
     * @param int    $excludeId
     * @param string $idColumn
     * @param array  $extra
     *
     * @return int
     */
    public function getCount($Repository, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $repository = $this->container->make($Repository);
        $criteria = [[$column, '=', $value]];

        if (!is_null($excludeId) && $excludeId != 'NULL') {
            $criteria[] = [$idColumn ?: $repository->primaryKey(), '<>', $excludeId];
        }

        return $repository->count($criteria);
    }

    /**
     * Count the number of objects in a Repository with the given values.
     *
     * @param string $Repository
     * @param string $column
     * @param array  $values
     * @param array  $extra
     *
     * @return int
     */
    public function getMultiCount($Repository, $column, array $values, array $extra = [])
    {
        $repository = $this->container->make($Repository);

        $criteria = [[$column, 'IN', $values]];

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
