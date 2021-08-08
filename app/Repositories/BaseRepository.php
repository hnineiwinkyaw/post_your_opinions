<?php

namespace App\Repositories;

class BaseRepository{

    public $model;
    
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function index()
    {
        return $this->model->all();
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }

    public function update($model , array $data)
    {
        return $model->update($data);
    }

    public function destroy($model)
    {
        return $model->delete();
    }

    public function count()
    {
        return $this->model->count();
    }

    public function findBySlug($slug)
    {
        return $this->model::whereSlug($slug)->firstOrFail();
    }

    public function tableName()
    {
        return $this->model->getTable();
    }
}