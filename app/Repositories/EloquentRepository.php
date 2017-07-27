<?php

namespace App\Repositories;

use IteratorAggregate;
use Core\Contracts\Models\Model as CoreModelContract;
use Core\Contracts\Repositories\Repository as CoreRepositoryContract;

abstract class EloquentRepository implements CoreRepositoryContract
{
    public function primaryKey(): string
    {
        return $this->modelClassName()::primaryKey();
    }

    public function save(CoreModelContract $model)
    {
        $model->save();
    }

    public function delete(CoreModelContract $model)
    {
        $model->delete();
    }

    public function all(): IteratorAggregate
    {
        return $this->modelClassName()::all();
    }

    public function find($id): ?CoreModelContract
    {
        return $this->modelClassName()::find($id);
    }

    public function findBy(string $field, $value): ?CoreModelContract
    {
        return $this->modelClassName()::where($field, $value)->first();
    }

    public function make(): ?CoreModelContract
    {
        return $this->modelClassName()::make([]);
    }

    public function search(array $criteria): IteratorAggregate
    {
        return $this->modelClassName()::where($criteria)->get();
    }

    public function count(array $criteria = []): int
    {
        return $this->modelClassName()::where($criteria)->count();
    }

    abstract protected function modelClassName(): string;
}
