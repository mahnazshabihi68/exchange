<?php

namespace App\Repositories\impls;

use App\Models\BaseModel;
use App\Repositories\interfaces\IBaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Jenssegers\Mongodb\Eloquent\Model as MongoDBModel;

class BaseRepository implements IBaseRepository
{
    /**
     * @param  Model|MongoDBModel  $model
     */
    public function __construct(protected Model|MongoDBModel $model)
    {
    }

    /**
     * @inheritDoc
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * @inheritDoc
     */
    public function paginate(int $perPage = BaseModel::PAGINATION_CHUNK): LengthAwarePaginator
    {
        return $this->model->paginate($perPage)->appends(request()?->all());
    }

    /**
     * @inheritDoc
     */
    public function create(Model|MongoDBModel $model): Model
    {
        $model->save();
        return $model->fresh();
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @inheritDoc
     */
    public function update(Model|MongoDBModel $model): Model
    {
        $model->save();
        return $model->fresh();
    }

    /**
     * @inheritDoc
     */
    public function delete(Model|MongoDBModel $model): bool
    {
        return $model->delete();
    }
}
