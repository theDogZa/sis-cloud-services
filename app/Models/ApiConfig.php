<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ApiConfig extends Model
{
    use Sortable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'api_config';

    public $sortable = ['id', 'code', 'type', 'isRequest', 'name', 'des', 'val', 'updated_uid'];
}
