@extends('layouts.master')

@section('style')
<style type="text/css">
#files-table table.dataTable thead .sorting:after,
#files-table table.dataTable thead .sorting_asc:after,
#files-table table.dataTable thead .sorting_desc:after {
  top: 18px !important;
}
</style>
@endsection
@section('content')
<section class="content-header">
	<h1>{{ __('messages.home') }}</h1>
	<ol class="breadcrumb">
		<li><a href="{{ route('home') }}"><i class="fa fa-home"></i>{{ __('messages.home') }}</a></li>
	</ol>
</section>
<div class="main-content-padding main-content-table">
	<div class="box box-primary" style="min-width: 1000px;">
		<div class="box-header with-border" style="padding-bottom: 0px;">
            <h3 class="box-title"></h3>
            <form class="form-horizontal pull-left" style="min-width: 800px;">
                <div class="form-group">
                    <label class="control-label col-md-2">{{ __('messages.project.select') }}</label>
                    <div class="col-md-4">
                        <select class="form-control select2" name="version" style="width: 100%" id="project-select">
                            @if($projects != null && $projects->count() != 0)
                                @foreach($projects as $item)
                                    @if($item->id == $project->id)
                                        <option value="{{ $item->id }}" selected>
                                            {{ $item->name . '|' . $item->en_name }}
                                        </option>
                                    @else
                                        <option value="{{ $item->id }}">
                                            {{ $item->name . '|' . $item->en_name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <label class="col-md-2 control-label">{{ __('messages.version.select') }}</label>
                    <div class="col-md-4">
                        <select class="form-control select2" name="version" style="width: 100%" id="version-select">
                            @if($versions != null && $versions->count() != 0)
                                @foreach($versions as $item)
                                    @if($item->id == $version->id)
                                        <option value="{{ $item->id }}" selected>
                                            {{ $item->name . '|' . $item->en_name }}
                                        </option>
                                    @else
                                        <option value="{{ $item->id }}">
                                            {{ $item->name . '|' . $item->en_name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </form>
			<div class="box-tools">
				<div class="btn-group pull-right">
					@if($version != null)
                       <button type="button" class="btn btn-success button-link btn-sm">
                          <a href="{{ route('checklists.create') }}">
                              {{ __('messages.checklist.create') }}
                          </a>
                       </button>
					   <button id="upload-file" type="button" class="btn btn-primary btn-sm">
						  {{ __('messages.file.upload') }}
					   </button>
                       <button id="url-upload" type="button" class="btn btn-info btn-sm">
                          {{ __('messages.file.url-upload') }}
                       </button>
					@endif
				</div>
			</div>
		</div>
		<div class="box-body" id="files-table">
            <div class="btn-group pull-right">
                @if($files != null && $files->count() != 0)
                    <button type="button" class="btn btn-success btn-sm button-link" id="multi-download">
                        {{ __('messages.file.multi-download') }}
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="multi-destroy">
                        {{ __('messages.file.multi-destroy') }}
                    </button>
                @endif
            </div>
			<table class="table table-hover table-striped display" cellspacing="0" width="100%" id="buttonClick">
				<thead>
					<tr>
						<th style="padding-left: 15px; padding-right: 15px;">
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle">
                                <i class="fa fa-square-o"></i>
                            </button>
                        </th>
						<th>{{ __('messages.file.name') }}</th>
						<th>{{ __('messages.file.type') }}</th>
						<th>{{ __('messages.file.username') }}</th>
						<th>{{ __('messages.file.created_at') }}</th>
                        <th>{{ __('messages.file.update_user') }}</th>
                        <th>{{ __('messages.file.updated_at') }}</th>
						<th>{{ __('messages.operation') }}</th>
					</tr>
				</thead>
				<tbody id="table-body">
					@if($files == null || $files->count() == 0)
						<tr>
							<td colspan="8">{{ __('messages.file.no') }}</td>
						</tr>
					@else
						@foreach($files as $file)
						<tr>
							<td><input type="checkbox" data-id="{{ $file->id }}" data-name="{{ $file->name }}" data-type="{{ strtoupper($file->type) }}"></td>
                            @if($file->type == 'checklist')
                                <td>
                                    <a href="{{ route('checklists.edit', ['id' => $file->checklist_id]) }}">
                                        {{ $file->name }}
                                    </a> 
                                </td>
                            @else
                                <td>{{ $file->name }}</td>
                            @endif
							<td>{{ strtoupper($file->type) }}</td>
							<td>{{ $file->username }}</td>
                            <td>{{ $file->created_at }}</td>
							<td>{{ $file->update_user }}</td>
                            <td>{{ $file->updated_at }}</td>
							<td>
							<div class="btn-group">
                                @if(strtoupper($file->type) == 'CHECKLIST')
                                    <button type="button" class="btn btn-primary btn-sm button-link" style="background-color: #1caf9a; color: white; border-color: #1caf9a;">
                                        <a href="{{ route('checklists.edit', ['id' => $file->checklist_id]) }}">{{ __('messages.checklist.enter') }}</a>
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm button-link">
                                        <a href="{{ route('checklists.export', ['id' => $file->checklist_id]) }}">{{ __('messages.checklist.export') }}</a>
                                    </button>
                                    @if(Auth::user()->isProjectAdmin($file->project_id) || Auth::user()->id == $file->user_id)
                                        <button type="button" class="btn btn-danger btn-sm destroy-file" data-id="{{ $file->id }}" data-name="{{ $file->name . '.' . $file->type }}">{{ __('messages.file.destroy') }}</button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-sm destroy-file disabled" data-id="{{ $file->id }}" data-name="{{ $file->name . '.' . $file->type }}">{{ __('messages.file.destroy') }}</button>
                                    @endif
                                @elseif(strtoupper($file->type) == 'NAS_URL')
                                    @if(Auth::user()->isProjectAdmin($file->project_id) || Auth::user()->id == $file->user_id)
                                        <button type="button" class="btn btn-primary btn-sm edit-file" data-id="{{ $file->id }}" data-name="{{ $file->name }}">
                                            {{ __('messages.checklist.edit-simple') }}
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-primary btn-sm edit-file disabled" data-id="{{ $file->id }}" data-name="{{ $file->name }}">
                                            {{ __('messages.checklist.edit-simple') }}
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-success btn-sm button-link">
                                       <a href="{{ $file->path }}">{{ __('messages.file.download') }}</a>
                                    </button>
                                    @if(Auth::user()->isProjectAdmin($file->project_id) || Auth::user()->id == $file->user_id)
                                        <button type="button" class="btn btn-danger btn-sm destroy-file" data-id="{{ $file->id }}" data-name="{{ $file->name }}">
                                            {{ __('messages.file.destroy') }}                        
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-sm destroy-file disabled" data-id="{{ $file->id }}" data-name="{{ $file->name . '.' . $file->type }}">
                                            {{ __('messages.file.destroy') }}                        
                                        </button>
                                    @endif
                                @else
                                    @if(Auth::user()->isProjectAdmin($file->project_id) || Auth::user()->id == $file->user_id)
                                        <button type="button" class="btn btn-primary btn-sm edit-file" data-id="{{ $file->id }}" data-name="{{ $file->name }}">
                                            {{ __('messages.checklist.edit-simple') }}
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-primary btn-sm edit-file disabled" data-id="{{ $file->id }}" data-name="{{ $file->name }}">
                                            {{ __('messages.checklist.edit-simple') }}
                                        </button>
                                    @endif
								    <button type="button" class="btn btn-success btn-sm button-link">
									   <a href="{{ route('files.download', ['id' => $file->id]) }}">{{ __('messages.file.download') }}</a>
								    </button>
                                    @if(Auth::user()->isProjectAdmin($file->project_id) || Auth::user()->id == $file->user_id)
								        <button type="button" class="btn btn-danger btn-sm destroy-file" data-id="{{ $file->id }}" data-name="{{ $file->name . '.' . $file->type }}">
                                            {{ __('messages.file.destroy') }}                        
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-sm destroy-file disabled" data-id="{{ $file->id }}" data-name="{{ $file->name . '.' . $file->type }}">
                                            {{ __('messages.file.destroy') }}                        
                                        </button>
                                    @endif
                                @endif
							</div>
							</td>
						</tr>
						@endforeach
					@endif
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="modal fade modal-margin-top" id="file-upload-modal" tabindex="-1" role="dialog" aria-labelledby="file-upload-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="fine-uploader-manual-trigger"></div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade modal-margin-top modal-warning" id="destroy-modal" tabindex="-1" role="dialog" aria-labelledby="destroy-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="destroy-modal-label"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-outline" id="destroy-confirm-btn">
                    {{ __('messages.destroy') }}
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-margin-top modal-warning" id="destroy-multi-modal" tabindex="-1" role="dialog" aria-labelledby="destroy-multi-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="destroy-multi-modal-label"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-outline" id="destroy-multi-confirm-btn">
                    {{ __('messages.destroy') }}
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-margin-top" id="edit-file-modal" tabindex="-1" role="dialog" aria-labelledby="edit-file-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="edit-file-modal-label">
                    {{ __('messages.file.edit-name') }}
                </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}

                    <div class="form-group">
                        <label for="file-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.file.name') }}</label>
                        <div class="col-md-6">
                            <input id="edit-file-name" type="text" class="form-control" name="file-name" required autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary" id="edit-file-confirm">
                                {{ __('messages.store') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@if($project != null && $version != null)
<div class="modal fade modal-margin-top" id="url-upload-modal" tabindex="-1" role="dialog" aria-labelledby="url-upload-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="url-upload-modal-label">
                    {{ __('messages.file.url-upload') }}
                </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ route('projects.versions.url-upload', ['id' => $project->id, 'version_id' => $version->id]) }}" id="url-upload-form">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="url" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.file.url') }}</label>
                        <div class="col-md-6">
                            <input id="url" type="text" class="form-control" name="url" required autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary" id="url-upload-confirm">
                                {{ __('messages.upload') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<form id="destroy-form" method="POST" style="display: none;">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
</form>
@endif
@endsection

@section('script')
    <script type="text/template" id="qq-template-manual-trigger">
        <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
            </div>
            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                <span class="qq-upload-drop-area-text-selector"></span>
            </div>
            <div class="buttons">
                <div class="qq-upload-button-selector qq-upload-button">
                    <div>{{ __('messages.file.select-files') }}</div>
                </div>
                <button type="button" id="trigger-upload" class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>{{ __('messages.file.upload') }} 
                </button>
            </div>
            <span class="qq-drop-processing-selector qq-drop-processing">
                <span>{{ __('messages.file.processing-dropped-files') }}</span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>
            <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                <li>
                    <div class="qq-progress-bar-container-selector">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                    </div>
                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                    <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                    <span class="qq-upload-file-selector qq-upload-file"></span>
                    <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                    <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                    <span class="qq-upload-size-selector qq-upload-size"></span>
                    <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">{{ __('messages.cancel') }}</button>
                    <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">{{ __('messages.retry') }}</button>
                    <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">{{ __('messages.delete') }}</button>
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">{{ __('messages.close') }}</button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">{{ __('messages.no') }}</button>
                    <button type="button" class="qq-ok-button-selector">{{ __('messages.yes') }}</button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">{{ __('messages.cancel') }}</button>
                    <button type="button" class="qq-ok-button-selector">{{ __('messages.ok') }}</button>
                </div>
            </dialog>
        </div>
    </script>
<script type="text/javascript">
$(function() {
	@if($project != null && $version != null)
    var uploader = new qq.FineUploader({
    	element: document.getElementById("fine-uploader-manual-trigger"),
        request: {
            endpoint: "{{ route('projects.versions.uploadfiles', ['id' => $project->id, 'version_id' => $version->id]) }}",
            customHeaders: {
            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        template: 'qq-template-manual-trigger',
        thumbnails: {
            placeholders: {
                waitingPath: "{{ url('vendor/fine-uploader/placeholders/waiting-generic.png') }}",
                notAvailablePath: "{{ url('vendor/fine-uploader/placeholders/not_available-generic.png') }}"
            }
        },
        autoUpload: false,
        callbacks: {
        	onComplete: function(id, name, response) {
        		
        	},
        	onAllComplete: function(succeeded, failed)
        	{
                if(failed.length == 0) {
                    window.location = "{{ route('home', ['version_id' => $version->id]) }}";
                }
        	}
        },
        validation: {
        	allowedExtensions: ['doc', 'docx', 'ppt', 'pptx', 'pdf', 'xls', 'xlsx', 'csv', 'txt'],
        	allowEmpty: true,
        },
        messages: {
        	noFilesError: '没有需要上传的文件',
        	sizeError: '{file}过大',
        	typeError: '{file}上传错误！支持的文件类型：{extensions}',
        }
    });
    @endif

    $('#trigger-upload').click(function() {
        uploader.uploadStoredFiles();
    });

    $('#upload-file').on('click', function() {
        $('#file-upload-modal').modal('show');
    });

    $('#buttonClick').on('click', '.destroy-file:not(.disabled)', function() {
        $('#destroy-modal').attr('data-id', $(this).attr('data-id'));
        header = "确定要删除《" + $(this).attr('data-name') + "》吗？";
        warn = "注意！你将删除文件《" + $(this).attr('data-name') + '》';
        $('#destroy-modal-label').text(header);
        $('#destroy-modal .modal-body').text(warn);
        $('#destroy-modal').modal('show');
    }).on('click', '.edit-file:not(.disabled)', function() {
        $('#edit-file-modal').attr('data-id', $(this).attr('data-id'));
        $('#edit-file-name').val($(this).attr('data-name'));
        $('#edit-file-modal').modal('show');
    });

    $('#edit-file-confirm').on('click', function() {
        url = "{{ route('index') }}" + '/files/' + $('#edit-file-modal').attr('data-id');
        $('form', '#edit-file-modal').attr('action', url).submit();
    });

    $('#destroy-confirm-btn').on('click', function() {
        url = "{{ route('index') }}" + "/files/" + $('#destroy-modal').attr('data-id');
        $('#destroy-form').attr('action', url).submit();
    });

    $(".select2").select2();

    $('#project-select').change(function() {
    	window.location = "{{ route('home') }}" + '?project_id=' + $(this).val();
    });

    $('#version-select').change(function() {
    	window.location = "{{ route('home') }}" + '?version_id=' + $(this).val();
    });

    $('#url-upload').on('click', function() {
        $('#url-upload-modal').modal('show');
    });

    $('#url-upload-confirm').on('click', function() {
        $('#url-upload-form').submit();
    });

    $("#buttonClick input[type='checkbox']").iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'iradio_flat-blue'
    });
    $('.checkbox-toggle').on('click', function() {
        var clicks = $(this).data('clicks');
        if(clicks) {
            $("#buttonClick input[type='checkbox']").iCheck("uncheck");
            $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
        } else {
            $("#buttonClick input[type='checkbox']").iCheck("check");
            $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
        }
        $(this).data("clicks", !clicks);
    });

    @if($files != null && $files->count() != 0)
    $('#multi-destroy').on('click', function() {
        $checkboxs = $("#table-body input[type='checkbox']:checked");
        files = '';
        is_admin = parseInt('{{ $is_admin }}');
        user_id = parseInt('{{ $user_id }}');
        if($checkboxs.length != 0) {
            for(i = 0; i < $checkboxs.length; ++i) {
                if(!is_admin && user_id != $($checkboxs[i]).attr('data-id')) continue;
                files += $($checkboxs[i]).attr('data-name') + '.' + $($checkboxs[i]).attr('data-type').toLowerCase() + "<br>";
                
            }
            if($files != '') {
                info = "确定要删除以下文件吗？";
                $('#destroy-multi-modal-label').text(info);
                $('#destroy-multi-modal .modal-body').html(files);
                $('#destroy-multi-modal').modal('show');
            }
        }
    });
    @endif

    $('#destroy-multi-confirm-btn').on('click', function() {
        ids = new Array();
        is_admin = parseInt('{{ $is_admin }}');
        user_id = parseInt('{{ $user_id }}');
        $checkboxs = $("#table-body input[type='checkbox']:checked");
        for(i = 0; i < $checkboxs.length; ++i) {
            if(!is_admin && user_id != $($checkboxs[i]).attr('data-id')) continue;
            ids.push($($checkboxs[i]).attr('data-id'));
        }

        if(ids.length != 0) {
            $.ajax({
                type: 'POST',
                url: "{{ route('files.multi-delete') }}",
                data: {
                    ids: ids
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    window.location = window.location.href;
                }
            });
        }
    });

    $('#multi-download').on('click', function() {
        ids = '';
        $checkboxs = $("#table-body input[type='checkbox']:checked");
        if($checkboxs.length != 0) {
            for(i = 0; i < $checkboxs.length; ++i) {
                if($($checkboxs[i]).attr('data-type').trim() != 'CHECKLIST') {
                    ids += $($checkboxs[i]).attr('data-id') + ',';
                }
            }
            window.location = "{{ route('files.multi-download') }}" + '?ids=' + ids;
        }
    });
    @if($files != null && $files->count() != 0)
    $('#buttonClick').DataTable({
        'columnDefs': [
            {
                'targets': [0, 7],
                'orderable': false
            }
        ],
        'paging': false,
        'searching': false,
        'info': false,
        'order': [[ 4, "desc" ]],
    });
    @endif
});
</script>
@endsection