<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Adldap;
use App\Project;
use App\Version;
use App\File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $projects = null;
        $versions = null;
        $project = null;
        $version = null;
        $files = null;
        $item = null;

        if(Auth::user()->isAdmin()) {
            $projects = Project::orderBy('updated_at', 'desc')->get();
        } else {
            $projects = Auth::user()->projects()->orderBy('updated_at', 'desc')->get();
        }

        if($request->has('version_id') && ($item = Version::find($request->input('version_id'))) != null && Auth::user()->isMember($item->project_id)) {
            $version = $item;
            $project = Project::find($item->project_id);
            $versions = Version::where('project_id', $item->project_id)->orderBy('updated_at', 'desc')->get();
        } else if($request->has('project_id') && ($item = Project::find($request->input('project_id'))) != null && Auth::user()->isMember($item->id)) {
            $project = $item;
            $versions = Version::where('project_id', $item->id)->orderBy('updated_at', 'desc')->get();
            if($versions != null && $versions->count() != 0) {
                $version = $versions->first();
            }
        } else {
            if($projects != null && $projects->count() != 0) {
                $project = $projects->first();
                $versions = Version::where('project_id', $project->id)->orderBy('updated_at', 'desc')->get();
                if($versions != null && $versions->count() != 0) {
                    $version = $versions->first();
                }
            }
        }

        if($version != null) {
            $files = File::where('version_id', $version->id)->orderBy('updated_at', 'desc')->get();
        }

        $is_admin = Auth::user()->isProjectAdmin($project->id) ? 1 : 0;
        $user_id = Auth::user()->id;

        return view('home.index', compact('projects', 'versions', 'project', 'version', 'files', 'is_admin', 'user_id'));
    }
}
