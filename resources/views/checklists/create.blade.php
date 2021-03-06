@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>{{ __('messages.checklist.create') }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('checklists.index') }}"><i class="fa fa-table"></i>{{ __('messages.checklist-self') }}</a></li>
        <li class="active">{{ __('messages.checklist.create') }}</li>
    </ol>
</section>
<div class="main-content-padding">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{ __('messages.checklist.create') }}
            </h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('checklists.store') }}">
                {{ csrf_field() }}

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

                <div class="form-group{{ $errors->has('template') ? ' has-error' : '' }}">
                    <label for="template" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.template.select') }}</label>
                    <div class="col-md-6">
                        <select class="form-control select2" name="template" style="width: 100%">
                            <option value="empty" selected>{{ __('messages.template.empty') }}</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group{{ $errors->has('template-name') ? ' has-error' : '' }}">
                    <label for="template-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.checklist.name') }}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="template-name" value="{{ old('template-name') }}" required autofocus>
                        @if ($errors->has('template-name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('template-name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
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

    $('#select-project').change(function() {
        window.location = "{{ route('checklists.create') }}" + "?project_id=" + $(this).val();
    });
});
</script>
@endsection