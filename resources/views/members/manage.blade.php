@extends('layouts.master')

@section('content')
<section class="content-header">
	<h1>{{ __('messages.member.manage') }}</h1>
	<ol class="breadcrumb">
		<li><a href="{{ route('projects.index') }}"><i class="fa fa-dashboard"></i>{{ __('messages.project.manage') }}</a></li>
		<li class="active"><i class="fa fa-users"></i>{{ __('messages.member.manage') }}</li>
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

	<div class="box box-info" style="min-width: 1000px;">
		<div class="box-header with-border" style="padding-bottom: 0px;">
			<h3 class="box-title"></h3>
            <form class="form-horizontal pull-left" style="min-width: 800px;">
                <div class="form-group">
                    <label class="control-label col-md-2">{{ __('messages.project.select') }}</label>
                    <div class="col-md-4">
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
                    <label class="col-md-2 control-label">{{ __('messages.member.add') }}</label>
                    <div class="col-md-4">
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
                    </div>
                </div>
            </form>
			<div class="box-tools">
				<div class="btn-group">
					<button type="button" class="btn btn-primary pull-right" id="synchronize-btn">
						{{ __('messages.member.synchronize') }}
					</button>
				</div>
			</div>
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
					@if($members->count() == 0)
						<tr>
							<td colspan="5">{{ __('messages.member.no') }}</td>
						</tr>
					@else
						@foreach($members as $member)
						<tr>
							<td>{{ $loop->index + 1 }}</td>
							@if($member->is_admin)
								<td>{{ $member->username . '（超级管理员）' }}</td>
							@elseif($member->isProjectAdmin($manage_project->id))
								<td>{{ $member->username . '（项目管理员）' }}</td>
							@else
								<td>{{ $member->username }}</td>
							@endif
							<td>{{ $member->uid_number }}</td>
							<td>{{ $member->created_at }}</td>
							<td>
								<div class="btn-group">
									@if(Auth::user()->is_admin && !$member->is_admin && !$member->isProjectAdmin($manage_project->id))
										<button type="button" class="btn btn-info btn-sm button-link">
											<a href="{{ route('projects.members.permission', ['id' => $manage_project->id, 'user_id' => $member->id]) . '?status=set&from=manage' }}">
												{{ __('messages.member.set-admin') }}
											</a>
										</button>
									@endif
									@if(Auth::user()->is_admin && !$member->is_admin && $member->isProjectAdmin($manage_project->id))
										<button type="button" class="btn btn-info btn-sm  button-link">
											<a href="{{ route('projects.members.permission', ['id' => $manage_project->id, 'user_id' => $member->id]) . '?status=cancel&from=manage' }}">
												{{ __('messages.member.cancel-admin') }}
											</a>
										</button>
									@endif
									@if(!$member->is_admin && (Auth::user()->is_admin || (Auth::user()->isProjectAdmin($manage_project->id) && !$member->isProjectAdmin($manage_project->id))))
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
<form id="synchronize-member-form" method="POST" style="display: none;" action="{{ route('members.synchronize') }}">
    {{ csrf_field() }}
</form>
@if(Auth::user()->is_admin || Auth::user()->isProjectAdmin($manage_project->id))
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

@endif
@endsection

@section('script')
<script type="text/javascript">
$(function() {

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
		url = "{{ route('projects.index') }}" + "/" + "{{ $manage_project->id }}" + "/" + $('#destroy-modal').attr('data-type') + "/" + $('#destroy-modal').attr('data-id') + '?from=manage';
		$('#destroy-form').attr('action', url).submit();
	});

	$('#create-member-confirm-btn').on('click', function() {
		url = "{{ route('projects.show', ['id' => $manage_project->id]) }}" + "/members" + '?from=manage';
		$('#member-id').val($('#create-member-modal').attr('data-id'));
		$('#store-member-form').attr('action', url).submit();
	});

	$('.select2').select2();

	$('#select-project').change(function() {
		window.location = "{{ route('members.manage') }}" + '?project_id=' + $(this).val();
	});

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