@extends('layouts.master')

@section('content')
<section class="content-header">
	<h1>{{ __('messages.version.brower') }}</h1>
	<ol class="breadcrumb">
		<li><a href="{{ route('projects.index') }}"><i class="fa fa-dashboard"></i>{{ __('messages.project.manage') }}</a></li>
		<li><a href="{{ route('versions.manage') }}"><i class="fa fa-dashboard"></i>{{ __('messages.version.manage') }}</a></li>
		<li class="active">{{ __('messages.version.brower') }}</li>
	</ol>
</section>
<div class="main-content-padding main-content-table">
	@if(isset($errors) && !$errors->isEmpty())
		<div class="box box-danger box-solid">
			<div class="box-header with-border">
				<h3 class="box-title">{{ __('messages.something-error') }}</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				@foreach($errors->keys() as $key)
                    <span class="help-block">
                        <strong>{{ $errors->first($key) }}</strong>
                    </span>
				@endforeach
			</div>
		</div>
	@endif
	<div class="box box-success">
		<div class="box-header with-border" style="padding-bottom: 0px;">
			<h3 class="box-title"></h3>
            <form class="form-horizontal pull-left" style="min-width: 800px;" role="form" id="edit-version">
                <div class="form-group{{ $errors->has('project') ? ' has-error' : '' }}">
                    <label for="project" class="col-md-2 control-label">{{ __('messages.project.select') }}</label>
                    <div class="col-md-6">
                        <select class="form-control select2" name="project" style="width: 100%" id="select-project">
                        	@if($projects != null && $projects->count() != 0)
                        		@foreach($projects as $project)
                        			@if($manage_project->id == $project->id)
                        				<option value="{{ $project->id }}" selected>
                        					{{ $project->name . '|' . $project->en_name }}
                        				</option>
                        			@else
                        				<option value="{{ $project->id }}">
                        					{{ $project->name . '|' . $project->en_name }}
                        				</option>                        			
                        			@endif
                        		@endforeach
                        	@endif
                        </select>
                    </div>
                </div>
            </form>
			@if($projects != null && $projects->count() != 0)
				<div class="box-tools">
					<button id="create-version" type="button" class="btn btn-primary pull-right">
						{{ __('messages.version.create') }}
					</button>
				</div>
			@endif
		</div>
		<div class="box-body">
			<table class="table table-hover table-striped">
				<thead>
					<tr>
						<th>{{ __('messages.numberid') }}</th>
						<th>{{ __('messages.version.name') }}</th>
						<th>{{ __('messages.version.username') }}</th>
						<th>{{ __('messages.version.created_at') }}</th>
						<th>{{ __('messages.operation') }}</th>
					</tr>
				</thead>
				<tbody id="buttonClickVersion">
					@if($versions == null || $versions->count() == 0)
						<tr>
							<td colspan="5">{{ __('messages.version.no') }}</td>
						</tr>
					@else
						@foreach($versions as $version)
						<tr>
							<td>{{ $loop->index + 1 }}</td>
							<td>
								<a href="{{ route('home', ['version_id' => $version->id]) }}">{{ $version->name . '|' . $version->en_name }}</a>
							</td>
							<td>{{ $version->username }}</td>
							<td>{{ $version->created_at }}</td>
							<td>
								<div class="btn-group">
									<button type="button" class="btn btn-success btn-sm edit-version" data-name="{{ $version->name }}" data-en-name="{{ $version->en_name }}" data-version="{{ $version->id }}">
										{{ __('messages.version.edit') }}
									</button>
									<button type="button" class="btn btn-danger btn-sm destroy-version" data-id="{{ $version->id }}" data-name="{{ $version->name . '|' . $version->en_name }}">{{ __('messages.version.destroy') }}</button>
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

<div class="modal fade modal-margin-top" id="create-version-modal" tabindex="-1" role="dialog" aria-labelledby="create-version-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="create-version-modal-label">
					{{ __('messages.version.create') }}
				</h4>
			</div>
			<div class="modal-body">
	            <form class="form-horizontal" role="form" method="POST" action="{{ route('projects.versions.store', ['id' => $manage_project->id, 'from' => 'manage']) }}">
	                {{ csrf_field() }}
	                <div class="form-group{{ $errors->has('version-name') ? ' has-error' : '' }}">
	                    <label for="version-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.version.name') }}</label>
	                    <div class="col-md-6">
	                        <input id="version-name" type="text" class="form-control" name="version-name" value="{{ old('version-name') }}" required autofocus>
	                        @if ($errors->has('version-name'))
	                            <span class="help-block">
	                                <strong>{{ $errors->first('version-name') }}</strong>
	                            </span>
	                        @endif
	                    </div>
	                </div>
	                <div class="form-group{{ $errors->has('version-en-name') ? ' has-error' : '' }}">
	                    <label for="version-en-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.version.en-name') }}</label>
	                      <div class="col-md-6">
	                        <input id="version-en-name" type="text" class="form-control" name="version-en-name" required>   		
	                          @if ($errors->has('version-en-name'))
	                            <span class="help-block">
	                                <strong>{{ $errors->first('version-en-name') }}</strong>
	                            </span>
	                        @endif
	                    </div>
	                </div>
	                <div class="form-group">
	                    <div class="col-md-6 col-md-offset-4">
	                        <button type="submit" class="btn btn-primary">
	                            {{ __('messages.create') }}
	                        </button>
	                    </div>
	                </div>
	            </form>
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
<form id="destroy-form" method="POST" style="display: none;">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
</form>
<div class="modal fade modal-margin-top" id="edit-version-modal" tabindex="-1" role="dialog" aria-labelledby="edit-version-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="edit-version-modal-label">
					{{ __('messages.version.edit') }}
				</h4>
			</div>
			<div class="modal-body">
	            <form class="form-horizontal" role="form" method="POST">
	                {{ csrf_field() }}
	                {{ method_field('PUT') }}

	                <div class="form-group">
	                    <label for="version-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.version.name') }}</label>
	                    <div class="col-md-6">
	                        <input id="edit-version-name" type="text" class="form-control" name="version-name" required autofocus>
	                    </div>
	                </div>
	                <div class="form-group">
	                    <label for="version-en-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.version.en-name') }}</label>
	                      <div class="col-md-6">
	                        <input id="edit-version-en-name" type="text" class="form-control" name="version-en-name" required>
	                    </div>
	                </div>
	                <div class="form-group">
	                    <div class="col-md-6 col-md-offset-4">
	                        <button type="submit" class="btn btn-primary" id="edit-version-confirm">
	                            {{ __('messages.store') }}
	                        </button>
	                    </div>
	                </div>
	            </form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
$(function() {
	$('#create-version').on('click', function() {
		$('#create-version-modal').modal('show');
	});

	$('#buttonClickVersion').on('click', '.destroy-version', function() {
		$('#destroy-modal').attr('data-id', $(this).attr('data-id'));
		$('#destroy-modal').attr('data-type', 'versions');
		header = "确定要删除《" + $(this).attr('data-name') + "》吗？";
		warn = "{{ __('messages.version.delete-warn') }}";
		$('#destroy-modal-label').text(header);
		$('#destroy-modal .modal-body').text(warn);
		$('#destroy-modal').modal('show');
	}).on('click', '.edit-version', function() {
		$('#edit-version-modal').attr('data-version', $(this).attr('data-version'));
		$('#edit-version-name').val($(this).attr('data-name'));
		$('#edit-version-en-name').val($(this).attr('data-en-name'));
		$('#edit-version-modal').modal('show');
	});

	$('#edit-version-confirm').on('click', function() {
		url = "{{ route('index') }}" + '/projects/' + "{{ $manage_project->id }}" + '/versions/' + $('#edit-version-modal').attr('data-version') + '?from=manage';
		$('form', '#edit-version-modal').attr('action', url).submit();
	});

	$('#destroy-confirm-btn').on('click', function() {
		url = "{{ route('projects.index') }}" + "/" + "{{ $manage_project->id }}" + '/versions/' + $('#destroy-modal').attr('data-id') + '?from=manage';
		$('#destroy-form').attr('action', url).submit();
	});

	$('.select2').select2();

	$('#select-project').change(function() {
		window.location = "{{ route('versions.manage') }}" + '?project_id=' + $(this).val();
	});
});
</script>
@endsection