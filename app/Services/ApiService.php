<?php

namespace App\Services;

use App\Models\Api;
use Illuminate\Database\Eloquent\Collection;

class ApiService extends BaseService
{
    public function getAllApis(int $perPage = 15)
    {
        return Api::with('user')->paginate($perPage);
    }

    public function getApiById(int $id): ?Api
    {
        return Api::with('user')->find($id);
    }

    public function createApi(array $data): Api
    {
        return Api::create($data);
    }

    public function updateApi(Api $api, array $data): Api
    {
        $api->update($data);
        return $api->fresh();
    }

    public function deleteApi(Api $api): bool
    {
        return $api->delete();
    }

    public function getApisByStatus(bool $status): Collection
    {
        return Api::where('status', $status)->with('user')->get();
    }
}
