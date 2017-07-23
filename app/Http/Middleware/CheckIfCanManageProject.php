<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Project;
use App\Version;

class CheckIfCanManageProject
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! Auth::user()->canManageProject()) {
            return back();
        }
        if($request->has('project_id') && !Auth::user()->isProjectAdmin($request->input('project_id'))) {
            return back();
        }

        $version = null;
        if($request->has('version_id') && ($version = Version::find($request->input('version_id'))) != null && !Auth::user()->isProjectAdmin($version->project_id)) {
            return back();
        }
        return $next($request);
    }
}
