<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class LogApi extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'log_api';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */


    public $sortable = [ 'updated_at', 'id', 'request_id', 'org_code', 'isSuccess', 'created_uid', 'updated_uid'];

    public function User()
    {
        return $this->belongsTo(User::class, 'created_uid', 'id');
    }
}
