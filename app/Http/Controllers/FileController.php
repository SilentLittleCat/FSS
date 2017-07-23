<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Version;
use App\User;
use App\Project;
use App\File;
use App\History;
use App\Checklist;
use Auth;
use Storage;
use Carbon\Carbon;
use Zipper;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function destroy($id)
    {
    	$file = File::find($id);

        if(strtoupper($file->type) == 'CHECKLIST') {
            Checklist::find($file->checklist_id)->delete();
        } elseif(strtoupper($file->type) == 'NAS_URL') {

        } else {
            Storage::delete($file->path);
        }

        History::create([
            'user_id' => Auth::user()->id,
            'username' => Auth::user()->username,
            'file_id' => $file->id,
            'checklist_id' => $file->checklist_id,
            'filename' => $file->name,
            'type' => $file->type,
            'info' => 'delete',
        ]);

    	$file->delete();

    	return back();
    }

    public function download($id)
    {
        $file = File::find($id);
        if($file != null && Storage::exists($file->path)) {
            $url = storage_path('app/' . $file->path);
            return response()->download($url, $file->name . '.' . $file->type);
        }
        return back();
    }

    public function update(Request $request, $id)
    {
        if($request->has('file-name')) {
            File::find($id)->update([
                'name' => $request->input('file-name'),
                'updated_at' => Carbon::now()
            ]);
        }

        return back();
    }

    public function multiDestroy(Request $request)
    {
        $ids = $request->input('ids');
        foreach($ids as $id) {
            $file = File::find($id);    

            if($file->type == 'checklist') {
                Checklist::find($file->checklist_id)->delete();
            } elseif(strtoupper($file->type) == 'NAS_URL') {
            
            } else {
                Storage::delete($file->path);
            }
            $file->delete();
        }
        return response()->json($request->input('ids'), 200);
    }

    public function multiDownload(Request $request)
    {
        if($request->has('ids')) {
            $tmp = $request->input('ids');
            $ids = explode(',', $tmp);
            $hashName = null;
            $path = null;
            $root = env('ZIP_ROOT');
            do {
                $hashName = Str::random(40);
                $path = $root . '/' . $hashName . '.zip';
            } while(Storage::exists($path));
            $zipper = Zipper::make('storage/' . substr($path, 7));
            foreach($ids as $id) {
                $id = (int)$id;
                $file = File::find($id);
                if($file != null && strtolower($file->type) != 'checklist' && strtolower($file->type) != 'nas_url') {
                    $zipper->add('storage/' . substr($file->path, 7), $file->name . '.' . $file->type);
                }
                
            }
            $zipper->close();
            $url = storage_path('app/' . $path);
            return response()->download($url, 'files.zip')->deleteFileAfterSend(true);
        }
        return back();
    }
}
