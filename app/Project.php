<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
	public static $projects_of_each_page = 10;

    protected $fillable = [
        'name', 'en_name', 'username', 'user_id'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User')->withPivot('is_pro_admin')->withTimestamps();
    }
}
