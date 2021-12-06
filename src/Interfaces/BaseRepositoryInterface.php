<?php

namespace Atikercan\LaravelRepositorier\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface {

    /**
     * Fin an item by id
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model;

    /**
     * Return all items
     * @return Collection|null
     */
    public function all(): ?Collection;

    /**
     * Return query builder instance to perform more manouvers
     * @return Builder|null
     */
    public function query(): ?Builder;

    /**
     * Return query builder instance to perform more manouvers
     * @return Builder|null
     */
    public function resetQuery(): ?Builder;

    /**
     * Create an item
     * @param array|mixed $data
     * @return Model|null
     */
    public function create($data): ?Model;

    /**
     * Update a model
     * @param int|mixed $id
     * @param array|mixed $data
     * @return bool|mixed
     */
    public function update($id, array $data);

    /**
     * Delete a model
     * @param int|Model $id
     */
    public function delete($id);
    /**
     * Returns a pagination
     * @param int $limit
     * @return LengthAwarePaginator
     */
    public function paginate(int $limit) : LengthAwarePaginator;
}
