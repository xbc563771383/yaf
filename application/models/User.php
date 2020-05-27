<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserModel extends Model {

    protected $table = 'user';

    use SoftDeletes;

}
