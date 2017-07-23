<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Project;
use Validator;
use DB;
use Auth;
use Adldap;

class MemberController extends Controller
{

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'member-id' => 'required|exists:users,id'
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $project = Project::find($id);
        if($project == null || $project->count() == 0) {
            $validator->errors()->add('project', '项目不存在！');
            return back()->withErrors($validator)->withInput();
        }

        DB::table('project_user')->insert([
        	'user_id' => $request->input('member-id'),
        	'project_id' => $id,
        ]);

        if($request->has('from') && $request->input('from') == 'manage') {
            return redirect()->route('members.manage', ['project_id' => $id]);
        }

        return redirect()->route('projects.show', ['id' => $id, 'status' => 'member']);
    }

    public function destroy(Request $request, $id, $member_id)
    {
        DB::table('project_user')->where([
        	'project_id' => $id,
        	'user_id' => $member_id,
        ])->delete();

        if($request->has('from') && $request->input('from') == 'manage') {
            return redirect()->route('members.manage', ['project_id' => $id]);
        }

        return redirect()->route('projects.show', ['id' => $id]);
    }

    public function manage(Request $request)
    {
        $projects = null;
        $manage_project = null;
        $members = null;
        $exsit_users = null;
        $can_add_users = null;
        if(Auth::user()->is_admin) {
            $projects = Project::orderBy('name')->get();
        } else {
            $projects = Auth::user()->projects()->orderBy('name')->get();
        }
        
        if($projects == null || $projects->count() == 0) {
            return back();
        }

        if($request->has('project_id') && $projects->where('id', $request->input('project_id'))->count() == 0) {
            return back();
        }

        if($request->has('project_id')) {
            $manage_project = $projects->where('id', $request->input('project_id'))->first();
        } else {
            $manage_project = $projects->first();
        }

        $members = $manage_project->users()->orderBy('username')->get();
        $exsit_users = $members->map(function($user) {
            return $user->id;
        })->toArray();

        $can_add_users = User::whereNotIn('id', $exsit_users)->orderBy('username')->get();

        return view('members.manage', compact('projects', 'members', 'manage_project', 'can_add_users'));
    }

    public function permission(Request $request, $id, $user_id)
    {
        if($request->has('status')) {
            if($request->status == 'set') {
                DB::table('project_user')->where(['project_id' => $id, 'user_id' => $user_id])->update(['is_pro_admin' => true]);
            } else if($request->status == 'cancel') {
                DB::table('project_user')->where(['project_id' => $id, 'user_id' => $user_id])->update(['is_pro_admin' => false]);
            }
        }
        if($request->has('from') && $request->input('from') == 'manage') {
            return redirect()->route('members.manage', ['project_id' => $id]);
        }
        return redirect()->route('projects.show', ['id' => $id]);
    }

    public function synchronize()
    {
        $root = env('ADLDAP_USRES');
        $ldap_users = Adldap::search()->users()->sortBy('uid')->setDn($root)->get();
        $ldap_usernames = $ldap_users->map(function($item) {
            return $item->uid;
        })->flatten();

        $users = User::all();
        $usernames = $users->map(function($item) {
            return $item->username;
        })->flatten();

        foreach($ldap_users as $ldap_user) {
            if(! $usernames->contains($ldap_user->uid[0])) {
                User::create([
                    'username' => $ldap_user->uid[0],
                    'uid_number' => $ldap_user->uidnumber[0],
                    'password' => '123456'
                ]);
            }
        }

        foreach($users as $user) {
            if(! $ldap_usernames->contains($user->username)) {
                $user->delete();
            }
        }

        return back();
    }
}
