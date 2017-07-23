<?php

namespace App;

use Adldap\Laravel\Traits\HasLdapUser;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Adldap;
use Auth;
use DB;

class User extends Authenticatable
{
    use Notifiable, HasLdapUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'uid_number', 'password', 'image', 'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isAdmin()
    {
        $adminMembers = Adldap::search()->findByDn(env('ADLDAP_ADMIN_GROUP'))->getAttribute('uniquemember');

        $userDN = $this->ldap->getDn();

        if(in_array($userDN, $adminMembers)) {
            return true;
        } else {
            return false;
        }
    }

    public function isMember($project_id)
    {
        if($this->is_admin) return true;
        if(DB::table('project_user')->where(['user_id' => $this->id, 'project_id' => $project_id])->get()->count() != 0) {
            return true;
        } 
        return false;
    }

    public function projects()
    {
        return $this->belongsToMany('App\Project')->withPivot('is_pro_admin')->withTimestamps();
    }

    public function isProjectAdmin($project_id)
    {
        if($this->is_admin) return true;
        $item = DB::table('project_user')->where(['user_id' => $this->id, 'project_id' => $project_id])->first();
        if($item != null && $item->is_pro_admin == true) return true;
        return false;
    }

    public function canManageProject()
    {
        if($this->is_admin) return true;
        $item = DB::table('project_user')->where([
            'user_id' => $this->id,
            'is_pro_admin' => 1
        ])->get();
        if($item != null && $item->count() != 0) return true;
        return false;
    }
}
