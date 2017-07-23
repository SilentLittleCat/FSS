@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>{{ __('messages.member.create') }}</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{ __('messages.home') }}</a></li>
        <li><a href="#"><i class="fa fa-dashboard"></i>{{ __('messages.project.manage') }}</a></li>
        <li class="active">{{ __('messages.member.create') }}</li>
    </ol>
</section>
<div class="main-content-padding">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{ __('messages.member.create') }}
            </h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('projects.members.store') }}">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('project-name') ? ' has-error' : '' }}">
                    <label for="project-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.project.name') }}</label>
                    <div class="col-md-6">
                        <input id="project-name" type="text" class="form-control" name="project-name" value="{{ old('project-name') }}" required autofocus>
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
                        <input id="project-en-name" type="text" class="form-control" name="project-en-name" required>   

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
                            {{ __('messages.create') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection