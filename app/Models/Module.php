<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    // public function parent()
    // {
    //     return $this->belongsTo(__CLASS__, 'parent_id');
    // }

    // public function child()
    // {
    //     return $this->hasMany(__CLASS__, 'parent_id');
    // }
}
