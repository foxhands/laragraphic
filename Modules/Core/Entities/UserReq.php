<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class UserReq extends Model
{

    protected $fillable = [
        'male_name',
        'male_birthday',
        'male_birthplace',
        'female_name',
        'female_birthday',
        'female_birthplace'
    ];
}
