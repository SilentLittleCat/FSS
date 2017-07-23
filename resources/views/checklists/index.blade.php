@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>{{ __('messages.checklist.brower-template') }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('checklists.index') }}"><i class="fa fa-table"></i>{{ __('messages.checklist-self') }}</a></li>
        <li class="active">{{ __('messages.checklist.brower-template') }}</li>
    </ol>
</section>
<div class="main-content-padding main-content-table">
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">
				{{ __('messages.checklist.brower-template') }}
			</h3>
			<div class="box-tools">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-primary pull-right button-link">
						<a href="{{ route('checklists.template.create') }}">
							{{ __('messages.checklist.create-template') }}
						</a>
					</button>
				</div>
			</div>
		</div>
		<div class="box-body">
			<table class="table table-hover table-striped"  id="buttonClick">
				<thead>
					<tr>
						<th>{{ __('messages.numberid') }}</th>
						<th>{{ __('messages.checklist.template-name') }}</th>
						<th>{{ __('messages.checklist.username') }}</th>
						<th>{{ __('messages.checklist.created_at') }}</th>
						<th>{{ __('messages.checklist.updated_at') }}</th>
						<th>{{ __('messages.operation') }}</th>
					</tr>
				</thead>
				<tbody>
					@if($checklists->count() == 0)
						<tr>
							<td colspan="5">{{ __('messages.checklist.no-template') }}</td>
						</tr>
					@else
						@foreach($checklists as $checklist)
						<tr>
							<td>{{ $loop->index + 1 }}</td>
							<td>
                                <a href="{{ route('checklists.edit',  ['id' => $checklist->id]) }}">
                                    {{ $checklist->name }}
                                </a>                     
                            </td>
                            <td>{{ $checklist->username }}</td>
							<td>{{ $checklist->created_at }}</td>
							<td>{{ $checklist->updated_at }}</td>
							<td>
    							<div class="btn-group">
    								<button type="button" class="btn btn-info btn-sm button-link">
    									<a href="{{ route('checklists.edit',  ['id' => $checklist->id]) }}">
    										{{ __('messages.template.edit') }}
    									</a>
    								</button>
    								@if(Auth::user()->is_admin || Auth::user()->id == $checklist->user_id)
    									<button type="button" class="btn btn-danger btn-sm destroy-template" data-id="{{ $checklist->id }}" data-name="{{ $checklist->name }}">
    										{{ __('messages.template.destroy') }}
    									</button>
    								@else
    									<button type="button" class="btn btn-danger btn-sm destroy-template disabled" id="delete-template-btn" data-id="{{ $checklist->id }}" data-name="{{ $checklist->name }}">
    										{{ __('messages.template.destroy') }}
    									</button>
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
<div class="modal fade modal-margin-top modal-warning" id="destroy-modal" tabindex="-1" role="dialog" aria-labelledby="destroy-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="destroy-modal-label"></h4>
            </div>
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
@endsection

@section('script')
<script type="text/javascript">
$(function() {
	$('#buttonClick').on('click', '.destroy-template:not(.disabled)', function() {
		$('#destroy-modal').attr('data-id', $(this).attr('data-id'));
		info = '确定要删除模板《' + $(this).attr('data-name') + '》吗？'
		$('.modal-title', '#destroy-modal').text(info);
		$('#destroy-modal').modal('show');
	});

	$('#destroy-confirm-btn').on('click', function() {
		url = "{{ route('checklists.index') }}" + '/template/' + $('#destroy-modal').attr('data-id');
		$('#destroy-form').attr('action', url).submit();
	});

	@if($checklists->count() != 0)
    $('#buttonClick').DataTable({
        'columnDefs': [
            {
                'targets': [5],
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