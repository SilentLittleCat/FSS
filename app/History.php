<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'user_id', 'username', 'file_id', 'filename', 'type', 'checklist_id', 'info'
    ];
}
