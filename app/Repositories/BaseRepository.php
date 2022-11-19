<?php

namespace App\Repositories;

class BaseRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function create($attributes)
    {
        return $this->model->create($attributes);
    }

    public function updateOrCreate($attributes)
    {
        return $this->model->updateOrCreate($attributes);
    }
}
