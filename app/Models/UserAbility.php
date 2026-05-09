<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Silber\Bouncer\Database\Ability;

class UserAbility extends Ability
{

    public function parent()
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

    public function child()
    {
        return $this->hasMany(__CLASS__, 'parent_id');
    }
}
