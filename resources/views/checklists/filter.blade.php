@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>{{ __('messages.checklist.filter') }}</h1>
    <ol class="breadcrumb">
        @if($checklist->is_template)
            <li><a href="{{ route('home') }}"><i class="fa fa-home"></i>{{ __('messages.home') }}</a></li>
        @else
            <li><a href="{{ route('home', ['version_id' => $checklist->version_id]) }}"><i class="fa fa-home"></i>{{ __('messages.home') }}</a></li>
        @endif
        <li class="active">{{ __('messages.checklist.filter') }}</li>
    </ol>
</section>
<div class="main-content-padding main-content-table">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title" id="file-name">
                {{ $checklist->name }}
            </h3>
            <div class="box-tools">
                <form class="form-horizontal pull-left" style="min-width: 800px;">
                    <div class="form-group">
                        <label class="control-label col-md-2">
                            {{ __('messages.column.select') }}
                        </label>
                        <div class="col-md-4">
                            <select class="form-control select2" name="version" style="width: 100%" id="column-select">
                                @foreach($data[0] as $item)
                                    @if($item != '' && $item != null)
                                        <option value="{{ $loop->index }}">
                                            {{ $item }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" style="width: 100%" placeholder="请输入筛选关键字..." id="keyword">
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-primary" type="button" id="filter-btn">
                                筛选
                            </button>
                            <button class="btn btn-info button-link" type="button">
                                <a href="{{ route('checklists.edit', ['id' => $checklist->id]) }}">
                                    编辑
                                </a>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="box-body" class="checklist-table">
            <div id="table"></div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
$(function() {

    data = {!! $checklist->content !!};

    var container = document.getElementById('table'),
        hot;

    hot = new Handsontable(container, {
        defaultData: '',
        startCols: data.data === undefined ? 20 : data.data.length,
        startRows:  data.data === undefined ? 20 : data.data[0].length,
        rowHeaders: true,
        colHeaders: true,
        className: "htLeft htTop",
        data: data.data,
    });

    $('.select2').select2();

    $('#filter-btn').on('click', function() {
        keyword = $('#keyword').val();
        pattern = new RegExp(keyword, 'i');
        column = $('#column-select').val();
        if(keyword == '') {
            hot.updateSettings({
                data: data.data,
            });
        } else {
            data1 = new Array();
            data1.push(data.data[0]);
            for(i = 1; i < data.data.length; ++i) {
                if(pattern.test(data.data[i][column])) {
                    data1.push(data.data[i]);
                }
            }
            hot.updateSettings({
                data: data1,
            });
        }
    });
});
</script>
@endsection