<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersMap extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_map';

    public $sortable = ['id', 'user_id', 'api_user_id', 'active', 'created_uid', 'updated_uid'];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ApiUser()
    {
        return $this->belongsTo(ApiUser::class, 'api_user_id', 'id');
    }
}
