@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>{{ __('messages.project.brower') }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('projects.index') }}"><i class="fa fa-dashboard"></i>{{ __('messages.project.manage') }}</a></li>
        <li class="active">{{ __('messages.project.brower') }}</li>
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
	<div class="box box-default">
		<div class="box-header with-border" style="padding-bottom: 0px;">
			<h3 class="box-title"></h3>
			<form class="form-horizontal pull-left" style="min-width: 800px;">
				<div class="form-group">
					<label class="col-md-2 control-label">{{ __('messages.project.search') }}</label>
					<div class="col-md-8">
						<select class="form-control select2" name="version" style="width: 100%" id="project-select">
							<option>
								{{ __('messages.project.search-hint') }}
							</option>
							@if($all_projects != null && $all_projects->count() != 0)
	                            @foreach($all_projects as $item)
                                	<option value="{{ $item->id }}">
                                    	{{ $item->name . '|' . $item->en_name }}
                                	</option>
	                            @endforeach
							@endif
						</select>
					</div>
				</div>
			</form>
			<div class="box-tools">
				<div class="btn-group pull-right">
					@if(Auth::user()->is_admin)
						<button type="button" class="btn btn-primary pull-right" id="create-project">
							{{ __('messages.project.create') }}
						</button>
					@endif
				</div>
			</div>
		</div>
		<div class="box-body">
			<table class="table table-hover table-striped" id="projects-table">
				<thead>
					<tr>
						<th>{{ __('messages.numberid') }}</th>
						<th>{{ __('messages.project.name') }}</th>
						<th>{{ __('messages.project.username') }}</th>
						<th>{{ __('messages.project.created_at') }}</th>
						<th>{{ __('messages.operation') }}</th>
					</tr>
				</thead>
				<tbody id="buttonClick">
					@if($projects == null || $projects->count() == 0)
						<tr>
							<td colspan="5">{{ __('messages.project.no') }}</td>
						</tr>
					@else
						@foreach($projects as $project)
						<tr>
							<td>{{ $loop->index + 1 }}</td>
							<td>
								<a href="{{ route('projects.show', ['id' => $project->id]) }}">{{ $project->name . '|' . $project->en_name }}</a>
							</td>
							<td>{{ $project->username }}</td>
							<td>{{ $project->created_at }}</td>
							<td>
								@if(Auth::user()->isProjectAdmin($project->id))
									<div class="btn-group">
										<button type="button" class="btn btn-success btn-sm button-link">
											<a href="{{ route('versions.manage', ['project_id' => $project->id]) }}">{{ __('messages.version.manage') }}</a>
										</button>
										<button type="button" class="btn btn-info btn-sm button-link">
											<a href="{{ route('members.manage', ['project_id' => $project->id]) }}">{{ __('messages.member.manage') }}</a>
										</button>
										<button type="button" class="btn btn-primary btn-sm edit-project" data-id="{{ $project->id }}" data-name="{{ $project->name }}" data-en-name="{{ $project->en_name }}">
											{{ __('messages.project.edit') }}
										</button>
										<button type="button" class="btn btn-danger btn-sm delete-project" data-id="{{ $project->id }}" data-name="{{ $project->name . '|' . $project->en_name }}">{{ __('messages.project.delete') }}</button>
									</div>
								@endif
							</td>
						</tr>
						@endforeach
					@endif
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="modal fade modal-margin-top" id="create-project-modal" tabindex="-1" role="dialog" aria-labelledby="create-project-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="create-project-modal-label">
					{{ __('messages.project.create') }}
				</h4>
			</div>
			<div class="modal-body">
	            <form class="form-horizontal" role="form" method="POST" action="{{ route('projects.store') }}" id="create-project-form">
	                {{ csrf_field() }}
	                <div class="form-group">
	                    <label for="project-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.project.name') }}</label>
	                    <div class="col-md-6">
	                        <input id="project-name" type="text" class="form-control" name="project-name" required autofocus>
	                    </div>
	                </div>
	                <div class="form-group">
	                    <label for="project-en-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.project.en-name') }}</label>	

	                      <div class="col-md-6">
	                        <input id="project-en-name" type="text" class="form-control" name="project-en-name" required>
	                    </div>
	                </div>
	                <div class="form-group">
	                    <div class="col-md-6 col-md-offset-4">
	                        <button type="submit" class="btn btn-primary" id="create-project-confirm">
	                            {{ __('messages.create') }}
	                        </button>
	                    </div>
	                </div>
	            </form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade modal-margin-top" id="edit-project-modal" tabindex="-1" role="dialog" aria-labelledby="edit-project-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="edit-project-modal-label">
					{{ __('messages.project.edit') }}
				</h4>
			</div>
			<div class="modal-body">
	            <form class="form-horizontal" role="form" method="POST" id="edit-project-form">
	                {{ csrf_field() }}
	                {{ method_field('PUT') }}

	                <div class="form-group">
	                    <label for="project-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.project.name') }}</label>
	                    <div class="col-md-6">
	                        <input id="edit-project-name" type="text" class="form-control" name="project-name" required autofocus>
	                    </div>
	                </div>
	                <div class="form-group">
	                    <label for="project-en-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.project.en-name') }}</label>	

	                      <div class="col-md-6">
	                        <input id="edit-project-en-name" type="text" class="form-control" name="project-en-name" required>
	                    </div>
	                </div>
	                <div class="form-group">
	                    <div class="col-md-6 col-md-offset-4">
	                        <button type="submit" class="btn btn-primary" id="edit-project-confirm">
	                            {{ __('messages.store') }}
	                        </button>
	                    </div>
	                </div>
	            </form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade modal-margin-top modal-warning" id="delete-project-modal" tabindex="-1" role="dialog" aria-labelledby="delete-project-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="delete-project-modal-label">
					{{ __('messages.project.delete-confirm-info') }}
				</h4>
			</div>
			<div class="modal-body">
				<p>{{ __('messages.project.delete-warn') }}</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">
					{{ __('messages.cancel') }}
				</button>
				<button type="button" class="btn btn-outline" id="delete-confirm-btn">
					{{ __('messages.delete') }}
				</button>
			</div>
		</div>
	</div>
</div>
<form method="POST" style="display: none;" id="delete-project-form">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
</form>

@endsection

@section('script')
<script type="text/javascript">
$(function() {
	$('#buttonClick').on('click', '.delete-project', function() {
		info = '确定要删除《' + $(this).attr('data-name') + '》吗？';
		$('#delete-project-modal-label').text(info);
		$('#delete-project-modal').attr('data-id', $(this).attr('data-id')).modal('show');
	}).on('click', '.edit-project', function() {
		$('#edit-project-modal').attr('data-id', $(this).attr('data-id'));
		$('#edit-project-name').val($(this).attr('data-name'));
		$('#edit-project-en-name').val($(this).attr('data-en-name'));
		url = "{{ route('projects.index') }}" + '/' + $('#edit-project-modal').attr('data-id');
		$('form', '#edit-project-modal').attr('action', url);
		$('#edit-project-modal').modal('show');
	});

	$('#delete-confirm-btn').on('click', function() {
		url = "{{ route('projects.index') }}" + "/" + $('#delete-project-modal').attr('data-id');
		$('#delete-project-form').attr('action', url);
		$('#delete-project-form').submit();
	});

	$('.pagination-input').on('keyup', function() {
		page = parseInt($(this).val());
		if(isNaN(page)) {
			$(this).val(1);
		} else if(page > $(this).attr('data-total')) {
			$(this).val($(this).attr('data-total'));
		}
	});

	$('.pagination-input-btn').on('click', function() {
		page = parseInt($(this).parent().prev().val());
		if(isNaN(page)) {
			page = 1;
		}
		window.location = "{{ route('projects.index') }}" + '/?page=' + page;
	});

	$(".select2").select2();

	$('#project-select').change(function() {
		window.location = "{{ route('projects.index') }}" + "/" + $(this).val();
	});

	$('#create-project').on('click', function() {
		$('#create-project-modal').modal('show');
	});

	$('#create-project-confirm').on('click', function() {
		$('#create-project-form').submit();
	});

	@if($projects != null && $projects->count() != 0)
    $('#projects-table').DataTable({
        'columnDefs': [
            {
                'targets': [4],
                'orderable': false
            }
        ],
        'paging': false,
        'searching': false,
        'info': false,
        'order': [[ 3, "desc" ]],
    });
	@endif
});
</script>
@endsection