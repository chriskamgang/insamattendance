<?php

namespace App\Http\Repositories;
use App\Exceptions\SMException;
use App\Models\FaceIds;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
        $this->create = true;
    }

    /**
     * @param array $filter
     * @return mixed
     */
    public function findALl(array $filter = []): mixed
    {
        return $this->user->with('getShift','getDepartment')->when(array_keys($filter, true), function ($query) use ($filter) {
            if (isset($filter['search'])) {
                $query->where(function ($q) use ($filter) {
                    $q->orWhere('name', 'like', '%' . $filter['search'] . '%');
                    $q->orWhere('email', 'like', '%' . $filter['search'] . '%');
                    $q->orWhere('mobile', 'like', '%' . $filter['search'] . '%');
                });
            }
            if (isset($filter['department_id'])) {
                $query->where('department_id', $filter['department_id']);
            }
            if (isset($filter['shift_id'])) {
                $query->where('shift_id', $filter['shift_id']);
            }
        })->orderBy('id', 'desc')->where('user_type','employee')->paginate(10);
    }
    public function findALlAdmin(): mixed
    {
        return $this->user->orderBy('id', 'desc')->where('user_type','admin')->paginate(10);
    }
    public function getAllEmployee(): mixed
    {
        return $this->user->with('getShift','getDepartment')->where('user_type','employee')->orderBy('id', 'desc')->get();
    }

    /**
     * @param $data
     * @return mixed
     * @throws SMException
     */
    public function save($data): mixed
    {
        if ($this->create){
            return DB::transaction(function () use ($data) {
                return $this->user->create($data)->fresh();
            });
        }
        throw new SMException("10 Employee can be only created. System is in demo mode");

    }

    /**
     * @param $user
     * @param $data
     * @return mixed
     */
    public function update($user, $data): mixed
    {
        return DB::transaction(static function () use ($user, $data) {
            return $user->update($data);
        });
    }


    /**
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        return $this->user->find($id);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function delete(User $user): mixed
    {
        return DB::transaction(static function () use ($user) {
            return $user->delete();
        });
    }

    /**
     * @param $username
     * @return mixed
     */
    /**
     * @param $email
     * @return mixed
     */
    public function getUserByEmail($email): mixed
    {
        return $this->user->where('email', $email)->first();
    }
    public function getSelectList(): mixed
    {
        return $this->user->where('user_type','employee')->pluck('name', 'id');
    }

    public function saveFaceIds($data): mixed
    {
        return DB::transaction(function () use ($data) {
            return FaceIds::create($data)->fresh();
        });
    }

    public function massDeleteFaceIds($user_id): mixed
    {
        return DB::transaction(function () use ($user_id) {
            return FaceIds::where('user_id', $user_id)->delete();
        });
    }
    public function getAllFaceIdS(): mixed
    {
        return FaceIds::all();
    }

    public function checkUniqueCode($unique_code): mixed
    {
        return $this->user->where('unique_code', $unique_code)->first();
    }

    public function checkTwoFactorCode($two_factor_code)
    {
        return $this->user->where('two_factor_code', $two_factor_code)
            ->where('two_factor_expires_at', '>', Carbon::now())
            ->first();
    }
}
