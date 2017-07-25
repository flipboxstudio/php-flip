<?php

namespace Core\Contracts\Repositories;

use IteratorAggregate;
use Core\Contracts\Models\Model;

interface Repository
{
    public function all(): IteratorAggregate;

    public function find($id): ?Model;

    public function make(): ?Model;

    public function findBy(string $field, $value): ?Model;

    public function save(Model $model);

    public function delete(Model $model);

    // TODO: Repository should provide:
    // - Pagination
    //   public function paginate(int $page, int $perPage = 10, array $criteria = []) : PaginationResult
    // - Fluent search
    //   public function search(array $criteria) : IteratorAggregate
    // - Bulk delete
    // public function deleteAll(array $criteria) : void
}
