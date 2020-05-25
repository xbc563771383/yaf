<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BannerModel extends Model {

    protected $table = 'banner';

    use SoftDeletes;

}
