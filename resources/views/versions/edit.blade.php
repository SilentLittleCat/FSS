@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>{{ __('messages.version.edit') }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('projects.index') }}"><i class="fa fa-dashboard"></i>{{ __('messages.project.manage') }}</a></li>
        <li><a href="{{ route('versions.manage') }}"><i class="fa fa-vimeo"></i>{{ __('messages.version.manage') }}</a></li>
        <li class="active">{{ __('messages.version.edit') }}</li>
    </ol>
</section>
<div class="main-content-padding">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{ __('messages.version.edit') }}
            </h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" method="POST" id="update-form">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <div class="form-group{{ $errors->has('project') ? ' has-error' : '' }}">
                    <label for="project" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.project.select') }}</label>
                    <div class="col-md-6">
                        <select class="form-control select2" name="project" style="width: 100%" id="select-project">
                            @foreach($projects as $project)
                                @if($project->id == $edit_version->project_id)
                                    <option value="{{ $project->id }}" selected="">
                                    {{ $project->name . '|' . $project->en_name }}
                                    </option>
                                @else
                                    <option value="{{ $project->id }}">
                                    {{ $project->name . '|' . $project->en_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group{{ $errors->has('version') ? ' has-error' : '' }}">
                    <label for="version" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.version.select') }}</label>
                    <div class="col-md-6">
                        <select class="form-control select2" name="version" style="width: 100%" id="select-version">
                            @foreach($versions as $version)
                                @if($version->id == $edit_version->id)
                                    <option value="{{ $version->id }}" selected="" data-name="{{ $version->name }}" data-en-name="{{ $version->en_name }}">
                                    {{ $version->name . '|' . $version->en_name }}
                                    </option>
                                @else
                                    <option value="{{ $version->id }}" data-name="{{ $version->name }}" data-en-name="{{ $version->en_name }}">
                                    {{ $version->name . '|' . $version->en_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group{{ $errors->has('version-name') ? ' has-error' : '' }}">
                    <label for="version-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.version.name') }}</label>
                    <div class="col-md-6">
                        <input id="version-name" type="text" class="form-control" name="version-name" value="{{ $edit_version->name }}" required autofocus>
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
                        <input id="version-en-name" type="text" class="form-control" name="version-en-name" value="{{ $edit_version->en_name }}" required>   

                        @if ($errors->has('version-en-name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('version-en-name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" id="update-version">
                            {{ __('messages.store') }}
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

    $('#update-version').on('click', function() {
        url = "{{ route('index') }}" + '/projects/' + $('#select-project').val() + '/versions/' + $('#select-version').val();
        $('#update-form').attr('action', url).submit();
    });

    $('#select-version').change(function() {
        $('#version-name').val($('#select-version option:selected').attr('data-name'));
        $('#version-en-name').val($('#select-version option:selected').attr('data-en_name'));
    });

    $('#select-project').change(function() {
        window.location = "{{ route('versions.edit') }}" + "?project_id=" + $(this).val();
    });

    $('#select-version').change(function() {
        version_id = $(this).val();
        $item = $('option[value=' + version_id + ']', this);
        $('#version-name').val($item.attr('data-name'));
        $('#version-en-name').val($item.attr('data-en-name'));
    });
});
</script>
@endsection