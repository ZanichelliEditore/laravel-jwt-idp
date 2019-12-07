<?php
namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Creates and returns an instance of model.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Finds $model by $id.
     *
     * @param integer $id
     * @return mixed
     */
    public function find(int $id);

    /**
     * Deletes $model from DB.
     *
     * @param $model
     * @return bool
     */
    public function delete($model);

    /**
     * Returns all instances of model.
     *
     * @return mixed
     */
    public function all();

    /**
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function update(int $id, array $data);
}
