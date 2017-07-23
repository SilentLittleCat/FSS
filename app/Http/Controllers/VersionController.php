<?php

namespace App\Http\Controllers;

use App\Version;
use App\User;
use App\Project;
use App\File;
use App\History;
use App\Checklist;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Storage;
use Illuminate\Support\Str;

class VersionController extends Controller
{
    public function show(Request $request, $id, $version_id)
    {
        $project = Project::find($id);
        $version = Version::find($version_id);

        if($project == null || $version == null) {
            return redirect()->route('home');
        }

        $files = File::where('version_id', $version_id)->orderBy('name')->get();

        return view('versions.show', compact('project', 'version', 'files'));
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'version-name' => 'required|string|max:255',
            'version-en-name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\-\.0-9_ ]*$/']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $version = Version::where([
            'project_id' => $id,
            'name' => $request->input('version-name')
        ])->orWhere([
            'project_id' => $id,
            'en_name' => $request->input('en-name')
        ])->get();
        
        if($version->count() > 0) {
            $validator->errors()->add('version-name', '版本已存在！');
            return back()->withErrors($validator)->withInput();
        }

        Version::create([
            'project_id' => $id,
            'username' => Auth::user()->username,
            'user_id' => Auth::user()->id,
            'name' => $request->input('version-name'),
            'en_name' => $request->input('version-en-name')
        ]);

        if($request->has('from') && $request->input('from') == 'manage') {
            return redirect()->route('versions.manage', ['project_id' => $id]);
        }

        return redirect()->route('projects.show', ['id' => $id, 'status' => 'version']);
    }

    public function destroy(Request $request, $id, $version_id)
    {
        $version = Version::find($version_id);
        $files = File::where('version_id', $version_id)->get();

        foreach ($files as $file) {
            if($file->type == 'checklist') {
                Checklist::find($file->checklist_id)->delete();
                DB::table('histories')->where('checklist_id', $file->checklist_id)->delete();
            }
            Storage::delete($file->path);
            DB::table('histories')->where('file_id', $file->id)->delete();
        }

        DB::table('files')->where('version_id', $version_id)->delete();
        $version->delete();

        if($request->has('from') && $request->input('from') == 'manage') {
            return redirect()->route('versions.manage', ['project_id' => $id]);
        }

        return redirect()->route('projects.show', ['id' => $id]);
    }

    public function uploadfiles(Request $request, $id, $version_id)
    {
        $project = Project::find($id);
        $version = Version::find($version_id);

        if($project != null && $version != null && $request->hasFile('qqfile')) {
            $path = env('PROJECTS_ROOT');
            $file = $request->qqfile;
            $name = explode('.', $file->hashName())[0];
            $extension = $file->getClientOriginalExtension();
            $filename = $name . '.' . $extension;
            $store_path = $file->storeAs($path, $filename);

            $store_file = File::create([
                'user_id' => Auth::user()->id,
                'update_user_id' => Auth::user()->id,
                'project_id' => $project->id,
                'version_id' => $version->id,
                'username' => Auth::user()->username,
                'update_user' => Auth::user()->username,
                'version_name' => $version->name . '|' . $version->en_name,
                'project_name' => $project->name . '|' . $project->en_name,
                'name' => explode('.', $file->getClientOriginalName())[0],
                'type' => $file->getClientOriginalExtension(),
                'path' => $store_path,
            ]);

            History::create([
                'user_id' => Auth::user()->id,
                'username' => Auth::user()->username,
                'file_id' => $store_file->id,
                'filename' => $store_file->name,
                'type' => $store_file->type,
                'info' => 'upload',
            ]);

            return response()->json(array('success' => true), 200);
        }

        $info = 'info';
        return response()->json(array('success' => false, 'info' => $info), 200);
    }

    public function urlUpload(Request $request, $id, $version_id)
    {
        if(! $request->has('url')) {
            return back();
        }
        $url = $request->input('url');
        $array = explode('/', $url);
        $fullname = end($array);
        $project = Project::find($id);
        $version = Version::find($version_id);

        $store_file = File::create([
            'user_id' => Auth::user()->id,
            'update_user_id' => Auth::user()->id,
            'project_id' => $project->id,
            'version_id' => $version->id,
            'username' => Auth::user()->username,
            'update_user' => Auth::user()->username,
            'version_name' => $version->name . '|' . $version->en_name,
            'project_name' => $project->name . '|' . $project->en_name,
            'name' => $fullname,
            'type' => 'nas_url',
            'path' => $url,
        ]);

        return back();
    }

    public function manage(Request $request)
    {
        $projects = null;
        $manage_project = null;
        $versions = null;
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

        $versions = Version::where('project_id', $manage_project->id)->orderBy('name')->get();
        return view('versions.manage', compact('projects', 'versions', 'manage_project'));
    }

    public function update(Request $request, $id, $version_id)
    {
        $validator = Validator::make($request->all(), [
            'version-name' => 'required|string|max:255',
            'version-en-name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\-\.0-9_ ]*$/']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $version = Version::where([
            ['project_id', '=', $id],
            ['id', '<>', $version_id],
            ['name', '=', $request->input('version-name')]
        ])->get();

        if($version->count() != 0) {
             $validator->errors()->add('version-name', '版本已存在！');
             return back()->withErrors($validator)->withInput();
        }

        $version = Version::where([
            ['project_id', '=', $id],
            ['id', '<>', $version_id],
            ['en_name', '=', $request->input('version-en-name')] 
        ])->get();

        if($version->count() != 0) {
             $validator->errors()->add('version-en-name', '版本已存在！');
             return back()->withErrors($validator)->withInput();
        }

        Version::where('id', $version_id)->update([
            'name' => $request->input('version-name'),
            'en_name' => $request->input('version-en-name')
        ]);

        if($request->has('from') && $request->input('from') == 'manage') {
            return redirect()->route('versions.manage', ['project_id', $id]);
        }

        return redirect()->route('projects.show', ['id' => $id]);
    }

    public function create()
    {
        $projects = null;
        if(Auth::user()->is_admin) {
            $projects = Project::orderBy('name')->get();
        } else {
            $projects = Auth::user()->projects()->orderBy('name')->get();
        }

        if($projects == null || $projects->count() == 0) {
            return back();
        }

        return view('versions.create', compact('projects'));
    }

    public function edit(Request $request)
    {
        $projects = null;
        $versions = null;
        $edit_version = null;
        if(Auth::user()->is_admin) {
            $projects = Project::orderBy('name')->get();
        } else {
            $projects = Auth::user()->projects()->orderBy('name')->get();
        }

        $projects = $projects->filter(function($item, $key) {
            return Version::where('project_id', $item->id)->get()->count() != 0;
        });

        if($projects == null || $projects->count() == 0) {
            return back();
        }

        $tmp = null;
        if($request->has('version_id') && ($tmp = Version::find($request->input('version_id'))) != null) {
            if(! Auth::user()->isProjectAdmin($tmp->project_id)) {
                return back();
            }
        }

        if($request->has('project_id') && ($tmp = Project::find($request->input('project_id'))) != null) {
            if(! Auth::user()->isProjectAdmin($tmp->id)) {
                return back();
            }
        }

        if($request->has('version_id') && Version::find($request->input('version_id')) != null) {
            $edit_version = Version::find($request->input('version_id'));
            $versions = Version::where('project_id', $edit_version->project_id)->orderBy('name')->get();
        } elseif($request->has('project_id') && $projects->where('id', $request->input('project_id'))->count() != 0) {
            $versions = Version::where('project_id', $request->input('project_id'))->orderBy('name')->get();
            $edit_version = $versions->first();
        } else {
            $versions = Version::where('project_id', $projects->first()->id)->orderBy('name')->get();
            $edit_version = $versions->first();
        }

        return view('versions.edit', compact('projects', 'edit_version', 'versions'));
    }
}
