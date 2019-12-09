<?php
/**
 * Created by PhpStorm.
 * User: andreadecastri
 * Date: 01/07/19
 * Time: 17.03
 */

namespace App\Repositories;


abstract class BaseRepository implements RepositoryInterface
{
    /**
     * Deletes $model from DB.
     *
     * @param $model
     * @return bool
     */
    public function delete($model)
    {
        return $model->delete();
    }
}