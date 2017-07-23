<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'version_id', 'file_id', 'name', 'type', 'path', 'username', 'checklist_id', 'update_user', 'update_user_id'
    ];
}
