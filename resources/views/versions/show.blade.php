@extends('layouts.master')

@section('content')
<section class="content-header">
	<h1>{{ $project->name . '|' . $project->en_name }}</h1>
	<ol class="breadcrumb">
		<li><a href="{{ route('home') }}"><i class="fa fa-home"></i>{{ __('messages.home') }}</a></li>
		<li><a href="{{ route('projects.show', ['id' => $project->id]) }}"><i class="fa fa-dashboard"></i>{{ __('messages.version.manage') }}</a></li>
		<li class="active">{{ __('messages.version.brower') }}</li>
	</ol>
</section>
<div class="main-content-padding main-content-table">
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">
				{{ $version->name . '|' . $version->en_name }}
			</h3>
			<div class="box-tools">
				<div class="btn-group pull-right">
					<button id="upload-file" type="button" class="btn btn-default">
						{{ __('messages.file.upload') }}
					</button>
				</div>
			</div>
		</div>
		<div class="box-body">
			<table class="table table-hover table-striped"  id="buttonClick">
				<thead>
					<tr>
						<th>{{ __('messages.numberid') }}</th>
						<th>{{ __('messages.file.name') }}</th>
						<th>{{ __('messages.file.type') }}</th>
						<th>{{ __('messages.file.username') }}</th>
						<th>{{ __('messages.file.created_at') }}</th>
						<th>{{ __('messages.operation') }}</th>
					</tr>
				</thead>
				<tbody>
					@if($files->count() == 0)
						<tr>
							<td colspan="6">{{ __('messages.file.no') }}</td>
						</tr>
					@else
						@foreach($files as $file)
						<tr>
							<td>{{ $loop->index + 1 }}</td>
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
							<td>
							<div class="btn-group">
                                @if($file->type == 'checklist')
                                    <button type="button" class="btn btn-info btn-sm button-link">
                                        <a href="{{ route('checklists.export', ['id' => $file->checklist_id]) }}">{{ __('messages.checklist.export') }}</a>
                                    </button>
                                @else
								    <button type="button" class="btn btn-success btn-sm button-link">
									   <a href="{{ route('files.download', ['id' => $file->id]) }}">{{ __('messages.file.download') }}</a>
								    </button>
								    <button type="button" class="btn btn-danger btn-sm destroy-file" data-id="{{ $file->id }}" data-name="{{ $file->name . '.' . $file->type }}">{{ __('messages.file.destroy') }}</button>
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
@if(Auth::user()->is_admin || Auth::user()->isProjectAdmin())
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
                    window.location = "{{ route('projects.versions.show', ['id' => $project->id, 'version_id' => $version->id]) }}";
                }
        	}
        },
        validation: {
        	allowedExtensions: ['doc', 'docx', 'ppt', 'pptx', 'pdf', 'checklist', 'nas_rul', 'xls', 'xlsx'],
        	allowEmpty: true,
        },
        messages: {
        	noFilesError: '没有需要上传的文件',
        	sizeError: '{file}过大',
        	typeError: '{file}上传错误！支持的文件类型：{extensions}',
        }
    });

    $('#trigger-upload').click(function() {
        uploader.uploadStoredFiles();
    });

    $('#upload-file').on('click', function() {
        $('#file-upload-modal').modal('show');
    });

    $('#buttonClick').on('click', '.destroy-file', function() {
        $('#destroy-modal').attr('data-id', $(this).attr('data-id'));
        header = "确定要删除《" + $(this).attr('data-name') + "》吗？";
        warn = "注意！你将删除文件" + $(this).attr('data-name');
        $('#destroy-modal-label').text(header);
        $('#destroy-modal .modal-body').text(warn);
        $('#destroy-modal').modal('show');
    });

    $('#destroy-confirm-btn').on('click', function() {
        url = "{{ route('index') }}" + "/files/" + $('#destroy-modal').attr('data-id');
        $('#destroy-form').attr('action', url).submit();
    });
});
</script>
@endsection