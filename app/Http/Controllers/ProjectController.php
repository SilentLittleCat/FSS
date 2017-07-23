<?php

namespace App\Http\Controllers;

use App\User;
use App\Project;
use App\Version;
use App\File;
use App\Checklist;
use App\History;
use Illuminate\Http\Request;
use Auth;
use Validator;
use DB;
use Storage;

class ProjectController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $projects = null;
        $all_projects = null;
        if(Auth::user()->is_admin) {
            $projects = Project::orderBy('updated_at', 'desc')->get();
            $all_projects = Project::orderBy('updated_at', 'desc')->get();
        } else {
            $projects = Auth::user()->projects()->orderBy('updated_at', 'desc')->get();
            $all_projects = Auth::user()->projects()->orderBy('updated_at', 'desc')->get();
        }

        return view('projects.index', ['projects' => $projects, 'all_projects' => $all_projects]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project-name' => 'required|string|max:255|unique:projects,name',
            'project-en-name' => ['required', 'string', 'max:255', 'unique:projects,en_name', 'regex:/^[a-zA-Z\-\.0-9_ ]*$/']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $project = Project::create([
            'uid_number' => Auth::user()->uid_number,
            'name' => $request->input('project-name'),
            'en_name' => $request->input('project-en-name'),
            'username' => Auth::user()->username,
            'user_id' => Auth::user()->id
        ]);

        DB::table('project_user')->insert([
            'user_id' => Auth::user()->id,
            'project_id' => $project->id
        ]);

        return redirect()->route('projects.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $project = Project::find($id);
        $exsit_users = null;
        $can_add_users = null;

        if(!Auth::user()->isMember($project->id)) {
            return back();
        }

        if($project == null || $project->count() == 0) {
            return back();
        }
        $members = $project->users()->orderBy('username')->get();
        $exsit_users = $members->map(function($user) {
            return $user->id;
        })->toArray();
        $can_add_users = User::whereNotIn('id', $exsit_users)->orderBy('username')->get();

        $versions = Version::where('project_id', $id)->orderBy('name')->get();
        // $status = $request->has('status') ?: null;
        return view('projects.show', compact('project', 'members', 'versions', 'can_add_users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $projects = null;
        $edit_project = null;
        if(Auth::user()->is_admin) {
            $projects = Project::orderBy('updated_at', 'desc')->get();
        } else {
            $projects = Auth::user()->projects()->orderBy('updated_at', 'desc')->get();
        }

        if($projects == null || $projects->count() == 0) {
            return back();
        }

        if($request->has('project_id') && Project::find($request->input('project_id')) != null) {
            $edit_project = Project::find($request->input('project_id'));
        } else {
            $edit_project = $projects->first();
        }

        return view('projects.edit', compact('projects', 'edit_project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'project-name' => 'required|string|max:255',
            'project-en-name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\-\.0-9_ ]*$/']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $project = Project::where([
            ['id', '<>', $id],
            ['name', '=', $request->input('project-name')]
        ])->get();

        if($project->count() != 0) {
             $validator->errors()->add('project-name', '项目已存在！');
             return back()->withErrors($validator)->withInput();
        }

        $project = Project::where([
            ['id', '<>', $id],
            ['en_name', '=', $request->input('project-en-name')] 
        ])->get();

        if($project->count() != 0) {
             $validator->errors()->add('project-en-name', '项目已存在！');
             return back()->withErrors($validator)->withInput();
        }

        Project::where('id', $id)->update([
            'name' => $request->input('project-name'),
            'en_name' => $request->input('project-en-name')
        ]);

        return redirect()->route('projects.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project = Project::find($id);
        $versions = Version::where('project_id', $id)->get();
        foreach ($versions as $version) {
            $files = File::where('version_id', $version->id)->get();

            foreach ($files as $file) {
                if($file->type == 'checklist') {
                    Checklist::find($file->checklist_id)->delete();
                    DB::table('histories')->where('checklist_id', $file->checklist_id)->delete();
                }
                Storage::delete($file->path);
                DB::table('histories')->where('file_id', $file->id)->delete();
            }

            DB::table('files')->where('version_id', $version->id)->delete();
        }
        DB::table('versions')->where('project_id', $id)->delete();
        $project->delete();
        DB::table('project_user')->where('project_id', $id)->delete();

        return redirect()->route('projects.index');
    }
}
