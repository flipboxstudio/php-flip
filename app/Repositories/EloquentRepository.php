<?php

namespace App\Repositories;

use IteratorAggregate;
use Illuminate\Database\Eloquent\Builder;
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
        $modelFqn = $this->modelClassName();

        return $this->where(
            new $this->modelClassName(),
            $criteria
        )->get();
    }

    public function count(array $criteria = []): int
    {
        $modelFqn = $this->modelClassName();

        return $this->where(
            new $modelFqn(),
            $criteria
        )->count();
    }

    protected function where(CoreModelContract $model, array $criteria = []): Builder
    {
        foreach ($criteria as $expression) {
            [$field, $operator, $value] = array_merge($expression, ['', '', '']);

            if (strtoupper($operator) === 'IN') { // IN
                $model = $model->whereIn($field, $value);
            } elseif (strtoupper($operator) === 'NIN') { // NOT IN
                $model = $model->whereNotIn($field, $value);
            } elseif (strtoupper($operator) === 'ORIN') { // OR IN
                $model = $model->orWhereIn($field, $value);
            } elseif (strtoupper($operator) === 'ORNIN') { // OR NOT IN
                $model = $model->orWhereNotIn($field, $value);
            } else {
                $model = $model->where($field, $operator, $value);
            }
        }

        return $model;
    }

    abstract protected function modelClassName(): string;
}
