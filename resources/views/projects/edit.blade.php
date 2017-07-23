@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>{{ __('messages.project.edit') }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('projects.index') }}"><i class="fa fa-dashboard"></i>{{ __('messages.project.manage') }}</a></li>
        <li class="active">{{ __('messages.project.edit') }}</li>
    </ol>
</section>
<div id="editProject" class="main-content-padding">
    <div class="box box-default">
        <div class="box-header with-border"></div>
        <div class="box-body">
            <form class="form-horizontal" role="form" method="POST" id="edit-form" action="{{ route('projects.update', ['id' => $edit_project->id]) }}">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="form-group{{ $errors->has('project-name') ? ' has-error' : '' }}">
                    <label for="project-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.project.select') }}</label>
                    <div class="col-md-6">
                        <select id="select-project" class="form-control select2" name="project-name" style="width: 100%">
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" data-name="{{ $project->name }}" data-en-name="{{ $project->en_name }}">
                                    {{ $project->name . '|' . $project->en_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group{{ $errors->has('project-name') ? ' has-error' : '' }}">
                    <label for="project-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.project.name') }}</label>
                    <div class="col-md-6">
                        <input id="project-name" type="text" class="form-control" name="project-name" value="{{ $edit_project->name }}" required autofocus>
                        @if ($errors->has('project-name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('project-name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('project-en-name') ? ' has-error' : '' }}">
                    <label for="project-en-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.project.en-name') }}</label>

                      <div class="col-md-6">
                        <input id="project-en-name" type="text" class="form-control" name="project-en-name"  value="{{ $edit_project->en_name }}" required>   

                        @if ($errors->has('project-en-name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('project-en-name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('messages.update') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
$(function() {
    $('#select-project').change(function() {
        $item = $('option[value=' + $(this).val() + ']', this);
        $('#project-name').val($item.attr('data-name'));
        $('#project-en-name').val($item.attr('data-en-name'));
        url = "{{ route('projects.index') }}" + '/' + $(this).val();
        $('#edit-form').attr('action', url);
    });

    $('.select2').select2();
});
</script>
@endsection