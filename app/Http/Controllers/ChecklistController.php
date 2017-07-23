<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Checklist;
use App\Project;
use App\Version;
use App\File;
use App\History;
use Auth;
use Excel;
use Carbon\Carbon;

class ChecklistController extends Controller
{
    public function index()
    {
        $checklists = Checklist::where('is_template', true)->orderBy('updated_at', 'desc')->get();

        return view('checklists.index', compact('checklists'));
    }

    public function createTemplate()
    {
    	$templates = Checklist::where('is_template', true)->orderBy('name')->get();
    	return view('checklists.create-template', compact('templates'));
    }

    public function create(Request $request)
    {
        $projects = null;
        $versions = null;
        $edit_version = null;
        if(Auth::user()->is_admin) {
            $projects = Project::orderBy('name')->get();
        } else {
            $projects = Auth::user()->projects()->orderBy('name')->get();
        }

        if($projects == null || $projects->count() == 0) {
            return back();
        }

        $projects = $projects->filter(function($item, $key) {
            return Version::where('project_id', $item->id)->orderBy('name')->get()->count() != 0;
        });

        if($projects == null || $projects->count() == 0) {
            return back();
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
        $templates = Checklist::where('is_template', true)->get();
        return view('checklists.create', compact('templates', 'projects', 'versions', 'edit_version'));
    }

    public function store(Request $request)
    {
    	$is_template = $request->has('is-template');
    	$content = $request->input('template') == 'empty' ? null : Checklist::find($request->input('template'))->content;
        if($content == null) {
            $content = $this->initContent();
        }
        $checklist = Checklist::create([
            'name' => $request->input('template-name'),
            'is_template' => $is_template,
            'content' => $content,
            'user_id' => Auth::user()->id,
            'username' => Auth::user()->username,
        ]);

        if($request->has('version')) {

            $version = Version::find($request->input('version'));
            $project = Project::find($version->project_id);

            $checklist->project_id = $project->id;
            $checklist->version_id = $version->id;

            $checklist->save();

            $file = File::create([
                'user_id' => Auth::user()->id,
                'update_user_id' => Auth::user()->id,
                'project_id' => $project->id,
                'version_id' => $version->id,
                'username' => Auth::user()->username,
                'update_user' => Auth::user()->username,
                'version_name' => $version->name . '|' . $version->en_name,
                'project_name' => $project->name . '|' . $project->en_name,
                'name' => $request->input('template-name'),
                'type' => 'checklist',
                'path' => $checklist->id,
                'checklist_id' => $checklist->id,
            ]);

            $checklist->file_id = $file->id;
            $checklist->save();

            History::create([
                'user_id' => Auth::user()->id,
                'username' => Auth::user()->username,
                'file_id' => $file->id,
                'checklist_id' => $checklist->id,
                'filename' => $file->name,
                'type' => 'checklist',
                'info' => 'create',
            ]);
        }

    	return redirect()->route('checklists.edit', ['id' => $checklist->id]);
    }

    public function initContent()
    {
        $data = array();
        for($i = 0; $i < 20; ++$i) {
            $array = array();
            for($j = 0; $j < 20; ++$j) {
                array_push($array, '');
            }
            array_push($data, $array);
        }
        return json_encode(['data' => $data]);
    }

    public function edit($id)
    {
        $checklist = Checklist::find($id);
        if($checklist == null) {
            return back();
        }
        if(!$checklist->is_template) {

            if(! Auth::user()->isMember($checklist->project_id)) {
                return back();
            }
        }

        if($checklist->content == null) {
            $checklist->content = $this->initContent();
        }
    	return view('checklists.edit', ['checklist' => $checklist]);
    }

    public function filter($id)
    {
        $checklist = Checklist::find($id);
        if($checklist == null) {
            return back();
        }
        if(!$checklist->is_template) {

            if(! Auth::user()->isMember($checklist->project_id)) {
                return back();
            }
        }

        $data = [[]];
        if($checklist->content == null) {
            $checklist->content = $this->initContent();
        }
        $data = json_decode($checklist->content)->data;
        return view('checklists.filter', ['checklist' => $checklist, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        if($request->has('template_name')) {
            Checklist::create([
                'name' => $request->input('template_name'),
                'is_template' => true,
                'content' => json_encode($request->all()),
                'user_id' => Auth::user()->id,
                'username' => Auth::user()->username,
            ]);
        }

        $checklist = Checklist::find($id);
        if(! $checklist->is_template) {

            $file = File::find($checklist->file_id);
            $file->update_user = Auth::user()->username;
            $file->update_user_id = Auth::user()->id;
            $file->updated_at = Carbon::now();
            $file->name = $request->input('fileName');
            $file->save();
        }

        Checklist::find($id)->update([
            'content' => json_encode($request->all()),
            'name' => $request->input('fileName'),
        ]);

        History::create([
            'user_id' => Auth::user()->id,
            'username' => Auth::user()->username,
            'file_id' => $checklist->id,
            'checklist_id' => $checklist->id,
            'filename' => $checklist->name,
            'type' => 'checklist',
            'info' => 'update',
        ]);

        return response()->json(array('status' => 'succuss'), 200);
    }

    public function export(Request $request, $id)
    {
        $checkList = CheckList::find($id);
        $content = json_decode($checkList->content);

        if($content == null) {
            Excel::create($checkList->name, function($excel) {
                $excel->sheet('Sheetname', function($sheet) {
                    
                });
            })->export('xls');
        } else {
            Excel::create($checkList->name, function($excel) use($content) {
                $excel->sheet('Sheetname', function($sheet) use($content) {

                    $merge_array = isset($content->mergeInfo) ? $content->mergeInfo : [];
                    $data = isset($content->data) ? $content->data : [''];
                    $row_info = isset($content->rowInfo) ? $content->rowInfo : [];
                    $col_info = isset($content->colInfo) ? $content->colInfo : [];
                    $color_info = isset($content->colorInfo) ? $content->colorInfo : [];
                    $align_info = isset($content->alignInfo) ? $content->alignInfo : [];
                    $sheet->fromArray($data, null, 'A1', true, false);

                    for($i = 0; $i < count($merge_array); ++$i) {
                        $col = (int) $merge_array[$i]->col;
                        $row = (int) $merge_array[$i]->row + 1;
                        $colspan = (int) $merge_array[$i]->colspan;
                        $rowspan = (int) $merge_array[$i]->rowspan;
                        $left_top = $sheet->getCellByColumnAndRow($col, $row)->getCoordinate();
                        $right_bottom = $sheet->getCellByColumnAndRow($col + $colspan - 1, $row + $rowspan - 1)->getCoordinate();
                        $range = $left_top . ":" . $right_bottom;
                        $val = $sheet->getCell($left_top)->getValue();
                        $sheet->mergeCells($range);
                        $sheet->getCell($left_top)->setValue($val);
                    }

                    for($i = 0; $i < count($color_info); ++$i) {
                        $col = (int) $color_info[$i]->col;
                        $row = (int) $color_info[$i]->row + 1;
                        $color = $color_info[$i]->color;
                        $left_top = $sheet->getCellByColumnAndRow($col, $row)->getCoordinate();
                        $sheet->cell($left_top, function($cell) use($color) {
                            $cell->setBackground($color);
                        });
                    }

                    $row = count($data);
                    $col = count($data[0]);

                    for($i = 0; $i < $col; ++$i) {
                        for($j = 0; $j < $row; ++$j) {
                            $left_top = $sheet->getCellByColumnAndRow($i, $j + 1)->getCoordinate();
                            $sheet->getStyle($left_top)->getAlignment()->setWrapText(true);
                        }
                    }
                    
                    $left_top = $sheet->getCellByColumnAndRow(0, 1)->getCoordinate();
                    $right_bottom = $sheet->getCellByColumnAndRow($col - 1, $row)->getCoordinate();
                    $range = $left_top . ":" . $right_bottom;
                    $sheet->cells($range, function($cells) {
                        $cells->setAlignment('left')->setValignment('top');
                    });

                    for($i = 0; $i < count($align_info); ++$i) {
                        $col = (int) $align_info[$i]->col;
                        $row = (int) $align_info[$i]->row + 1;
                        $className = $align_info[$i]->className;
                        $left_top = $sheet->getCellByColumnAndRow($col, $row)->getCoordinate();
                        $sheet->cell($left_top, function($cell) use($className) {
                            if(strstr($className, 'htLeft')) {
                                $cell->setAlignment('left');
                            } else if(strstr($className, 'htCenter')) {
                                $cell->setAlignment('center');
                            } else if(strstr($className, 'htRight')) {
                                $cell->setAlignment('right');
                            } else if(strstr($className, 'htJustify')) {
                                $cell->setAlignment('left');
                            } else {
                                $cell->setAlignment('left');
                            }

                            if(strstr($className, 'htTop')) {
                                $cell->setValignment('top');
                            } else if(strstr($className, 'htBottom')) {
                                $cell->setValignment('bottom');
                            } else if(strstr($className, 'htMiddle')) {
                                $cell->setValignment('center');
                            } else {
                                $cell->setValignment('top');
                            }
                        });
                        $sheet->getStyle($left_top)->getAlignment()->setWrapText(true);
                    }

                    $widthScale = 6.5;
                    $heightScale = 1.3;
                    for($i = 0; $i < count($row_info); ++$i) {
                        $sheet->setHeight($i + 1, (int)($row_info[$i] / $heightScale));
                    }
                    for($j = 0; $j < count($col_info); ++$j) {
                        $sheet->setWidth($sheet->getColumnDimensionByColumn($j)->getColumnIndex(), (int)($col_info[$j] / $widthScale));
                    }
                    // $sheet->setAutoSize(true);
                }); 
            })->export('xls');
        }

        return 'ok';
    }

    public function deleteTemplate(Request $request, $id)
    {
        Checklist::find($id)->delete();

        return back();
    }
}
