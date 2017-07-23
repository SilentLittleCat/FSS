@extends('layouts.master')

@section('content')
<section class="content-header">
	<h1>{{ $project->name . '|' . $project->en_name }}</h1>
	<ol class="breadcrumb">
		<li><a href="{{ route('projects.index') }}"><i class="fa fa-dashboard"></i>{{ __('messages.project.manage') }}</a></li>
		<li class="active">{{ __('messages.project.info') }}</li>
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
		<div class="box-header with-border">
			<h3 class="box-title">
				{{ __('messages.project.version') }}
			</h3>
			@if(Auth::user()->isProjectAdmin($project->id))
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
							<td>{{ $project->created_at }}</td>
							<td>
								@if(Auth::user()->isProjectAdmin($project->id))
									<div class="btn-group">
										<button type="button" class="btn btn-success btn-sm edit-version" data-name="{{ $version->name }}" data-en-name="{{ $version->en_name }}" data-version="{{ $version->id }}">
											{{ __('messages.version.edit') }}
										</button>
										<button type="button" class="btn btn-danger btn-sm destroy-version" data-name="{{ $version->name . '|' . $version->en_name }}" data-id="{{ $version->id }}">{{ __('messages.version.destroy') }}</button>
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

	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">
				{{ __('messages.project.member') }}
			</h3>
			@if(Auth::user()->isProjectAdmin($project->id))
				<div class="box-tools">
					<div class="input-group">
						<select class="form-control select2" name="member" style="width: 100%" id="add-member">
                        	@if($can_add_users != null && $can_add_users->count() != 0)
                   				<option value="empty">
                   					{{ __('messages.member.select') }}
                   				</option>
                        		@foreach($can_add_users as $user)
                       				<option value="{{ $user->id }}" data-name="{{ $user->username }}">
                       					{{ '用户ID：' . $user->uid_number . '&nbsp;&nbsp;&nbsp;用户名：' . $user->username }}
                       				</option>
                        		@endforeach
                        	@endif
                        </select>
						<div class="input-group-btn">
							<button type="button" class="btn btn-primary" id="synchronize-btn">
								{{ __('messages.member.synchronize') }}
							</button>
						</div>
					</div>
				</div>
			@endif
		</div>
		<div class="box-body">
			<table class="table table-hover table-striped">
				<thead>
					<tr>
						<th>{{ __('messages.numberid') }}</th>
						<th>{{ __('messages.member.name') }}</th>
						<th>{{ __('messages.member.uid') }}</th>
						<th>{{ __('messages.member.created_at') }}</th>
						<th>{{ __('messages.operation') }}</th>
					</tr>
				</thead>
				<tbody id="buttonClickMember">
					@if($members == null || $members->count() == 0)
						<tr>
							<td colspan="5">{{ __('messages.member.no') }}</td>
						</tr>
					@else
						@foreach($members as $member)
						<tr>
							<td>{{ $loop->index + 1 }}</td>
							@if($member->is_admin)
								<td>{{ $member->username . '（超级管理员）' }}</td>
							@elseif($member->isProjectAdmin($project->id))
								<td>{{ $member->username . '（项目管理员）' }}</td>
							@else
								<td>{{ $member->username }}</td>
							@endif
							<td>{{ $member->uid_number }}</td>
							<td>{{ $member->created_at }}</td>
							<td>
								<div class="btn-group">
									@if(Auth::user()->is_admin && !$member->is_admin && !$member->isProjectAdmin($project->id))
										<button type="button" class="btn btn-info btn-sm button-link">
											<a href="{{ route('projects.members.permission', ['id' => $project->id, 'user_id' => $member->id]) . '?status=set' }}">
												{{ __('messages.member.set-admin') }}
											</a>
										</button>
									@endif
									@if(Auth::user()->is_admin && !$member->is_admin && $member->isProjectAdmin($project->id))
										<button type="button" class="btn btn-info btn-sm  button-link">
											<a href="{{ route('projects.members.permission', ['id' => $project->id, 'user_id' => $member->id]) . '?status=cancel' }}">
												{{ __('messages.member.cancel-admin') }}
											</a>
										</button>
									@endif
									@if(!$member->is_admin && (Auth::user()->is_admin || (Auth::user()->isProjectAdmin($project->id) && !$member->isProjectAdmin($project->id))))
										<button type="button" class="btn btn-danger btn-sm destroy-member" data-id="{{ $member->id }}" data-name="{{ $member->username }}">{{ __('messages.member.destroy') }}</button>
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
	            <form class="form-horizontal" role="form" method="POST" action="{{ route('projects.versions.store', ['id' => $project->id]) }}">
	                {{ csrf_field() }}
	                <div class="form-group">
	                    <label for="version-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.version.name') }}</label>
	                    <div class="col-md-6">
	                        <input id="version-name" type="text" class="form-control" name="version-name" required autofocus>
	                    </div>
	                </div>
	                <div class="form-group">
	                    <label for="version-en-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.version.en-name') }}</label>
	                      <div class="col-md-6">
	                        <input id="version-en-name" type="text" class="form-control" name="version-en-name" required>
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
<div class="modal fade modal-margin-top" id="create-member-modal" tabindex="-1" role="dialog" aria-labelledby="create-member-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="create-member-modal-label">
				</h4>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">
					{{ __('messages.cancel') }}
				</button>
				<button type="button" class="btn btn-primary" id="create-member-confirm-btn">
					{{ __('messages.create') }}
				</button>
			</div>
		</div>
	</div>
</div>
<form id="destroy-form" method="POST" style="display: none;">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
</form>
<form id="store-member-form" method="POST" style="display: none;">
    {{ csrf_field() }}
    <input id="member-id" type="text" class="form-control" name="member-id" required> 
</form>
<form id="synchronize-member-form" method="POST" style="display: none;" action="{{ route('members.synchronize') }}">
    {{ csrf_field() }}
</form>
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
		url = "{{ route('index') }}" + '/projects/' + "{{ $project->id }}" + '/versions/' + $('#edit-version-modal').attr('data-version');
		$('form', '#edit-version-modal').attr('action', url).submit();
	});

	$('#buttonClickMember').on('click', '.destroy-member', function() {
		$('#destroy-modal').attr('data-id', $(this).attr('data-id'));
		$('#destroy-modal').attr('data-type', 'members');
		header = "确定要删除《" + $(this).attr('data-name') + "》吗？";
		warn = "{{ __('messages.member.delete-warn') }}";
		$('#destroy-modal-label').text(header);
		$('#destroy-modal .modal-body').text(warn);
		$('#destroy-modal').modal('show');
	});

	$('#destroy-confirm-btn').on('click', function() {
		url = "{{ route('projects.index') }}" + "/" + "{{ $project->id }}" + "/" + $('#destroy-modal').attr('data-type') + "/" + $('#destroy-modal').attr('data-id');
		$('#destroy-form').attr('action', url).submit();
	});

	$('#create-member-confirm-btn').on('click', function() {
		url = "{{ route('projects.show', ['id' => $project->id]) }}" + "/members" ;
		$('#member-id').val($('#create-member-modal').attr('data-id'));
		$('#store-member-form').attr('action', url).submit();
	});

	$('.select2').select2();

	$('#add-member').change(function() {
		user_id = $(this).val();
		if(user_id != 'empty') {
			$item = $('option[value=' + user_id + ']', this);
			info = "确定要添加《" + $item.attr('data-name') + "》为项目成员吗？";
			$('#create-member-modal-label').text(info);
			$('#create-member-modal').attr('data-id', user_id).modal('show');
		}
	});

	$('#synchronize-btn').on('click', function() {
		$('#synchronize-member-form').submit();
	});
});
</script>
@endsection