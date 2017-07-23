@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>{{ __('messages.version.add') }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('projects.index') }}"><i class="fa fa-dashboard"></i>{{ __('messages.project.manage') }}</a></li>
        <li class="active">{{ __('messages.version.add') }}</li>
    </ol>
</section>
<div id="addProject" class="main-content-padding">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{ __('messages.version.add') }}
            </h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" method="POST" action="" id="add-form">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('project-name') ? ' has-error' : '' }}">
                    <label for="project-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.project.name') }}</label>
                    <div class="col-md-6">
                        <select id="project-name" class="form-control select2" name="project-name" style="width: 100%">
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name . '|' . $project->en_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
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
                        <input id="version-en-name" type="text" class="form-control" name="version-en-name"  value="{{ old('version-en-name') }}" required>   

                        @if ($errors->has('version-en-name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('version-en-name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" id="add-version">
                            {{ __('messages.add') }}
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
    $(".select2").select2();

    $('#add-version').on('click', function() {
        url = "{{ route('projects.index') }}" + '/' + $('#project-name').val() + '/versions'; 
        $('#add-form').attr('action', url).submit();
    });
});
</script>
@endsection