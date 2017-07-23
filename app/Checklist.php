<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $fillable = [
        'name', 'content', 'is_template', 'user_id', 'username', 'project_id', 'version_id'
    ];
}
