@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>{{ __('messages.checklist.edit') }}</h1>
    <ol class="breadcrumb">
        @if($checklist->is_template)
            <li><a href="{{ route('home') }}"><i class="fa fa-home"></i>{{ __('messages.home') }}</a></li>
        @else
            <li><a href="{{ route('home', ['version_id' => $checklist->version_id]) }}"><i class="fa fa-home"></i>{{ __('messages.home') }}</a></li>
        @endif
        <li class="active">{{ __('messages.checklist.edit') }}</li>
    </ol>
</section>
<div class="main-content-padding main-content-table">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title" contenteditable="true" id="file-name">
                {{ $checklist->name }}
            </h3>
            <div class="box-tools">
                <div class="btn-group pull-right">
                    <button id="store-checklist" type="button" class="btn btn-primary">
                        {{ __('messages.checklist.store') }}
                    </button>
                    <button id="store-template" type="button" class="btn btn-success">
                        {{ __('messages.checklist.store-template') }}
                    </button>
                    <button id="export-checklist" type="button" class="btn btn-info">
                        {{ __('messages.checklist.export') }}
                    </button>
                    <button type="button" class="btn btn-warning button-link">
                        <a href="{{ route('checklists.filter', ['id' => $checklist->id]) }}">
                            {{ __('messages.checklist.filter') }}
                        </a>
                    </button>
                </div>
                <div class="input-group color-picker">
                    <label>单元格颜色：</label>
                    <input id='colorpicker' class="form-control">
                </div>
            </div>
        </div>
        <div class="box-body" class="checklist-table">
            <div id="table"></div>
        </div>
    </div>
</div>
<div class="modal fade modal-margin-top modal-success" id="success-info-modal" tabindex="-1" role="dialog" aria-labelledby="success-info-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="success-info-modal-label">
                    {{ __('messages.checklist.store-success') }}
                </h4>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-margin-top modal-default" id="store-template-modal" tabindex="-1" role="dialog" aria-labelledby="store-template-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="store-template-modal-label">
                    {{ __('messages.checklist.store-template') }}
                </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="template-name" class="col-md-2 col-md-offset-2 control-label">{{ __('messages.template.name') }}</label>
                        <div class="col-md-6">
                            <input id="template-name" type="text" class="form-control" name="template-name" required autofocus>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="store-template-btn">
                    {{ __('messages.store') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
$(function() {
    $("#colorpicker").spectrum({
        showPalette: true,
        color: 'blanchedalmond',
        palette: [
            ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
            ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
            ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
            ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
            ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
            ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
            ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
            ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
        ]
    });

    function getSelectedColor() {
        return $('#colorpicker').spectrum("get").toHexString();
    }

    var TableStyles = function(hot) {
        var self = this;
        
        var _colorStyles = [];
        var _alignStyles = [];
        
        var _createStyle = function(row, col, color) {
            var _color = color;
            
            var style = {
                row: row,
                col: col,
                mycolor: color,
                renderer:   function (instance, td, row, col, prop, value, cellProperties) {
                                Handsontable.renderers.TextRenderer.apply(this, arguments);
                                td.style.backgroundColor = _color;
                            },
                color: function(c) { _color = c; }                
            };       
            
            return style;
        };
        
        self.getColorStyles = function() {
            return _colorStyles;
        };

        self.getAlignStyles = function() {
            return _alignStyles;
        };

        self.setAlignStyles = function() {
            return _alignStyles;
        };
        
        self.setColorStyle = function(row, col, color, updateTable) {
            var _color = color;
                        
            if (_colorStyles.length == 0) {
                _colorStyles.push(_createStyle(row, col, color));
            } else {
                var found = _colorStyles.some(function(cell) {
                    if (cell.row == row && cell.col == col) {                        
                        cell.color(color);
                        cell.mycolor = color;
                        return true;
                    }
                });
                
                if (!found) {
                    _colorStyles.push(_createStyle(row, col, color));
                }
            }                
            
            if (updateTable!=false) {
                hot.updateSettings({cell: self.getColorStyles()});
                hot.render();                        
            };                
        };

        self.setAlignStyle = function(row, col, className) {
                        
            if (_alignStyles.length == 0) {
                _alignStyles.push({row: row, col: col, className: className});
            } else {
                var found = _alignStyles.some(function(cell) {
                    if (cell.row == row && cell.col == col) {                        
                        cell.className = className;
                        return true;
                    }
                });
                if (!found) {
                    _alignStyles.push({row: row, col: col, className: className});
                }
            }                            
        };
        
        self.setRowStyle = function(row, color) {
            for (var col=0; col<hot.countCols(); col++)
                self.setColorStyle(row, col, color, false);
            
            hot.updateSettings({cell: self.getColorStyles()});
            hot.render();                        
        };
        
        self.setColStyle = function(col, color) {
            for (var row=0; row<hot.countCols(); row++)
                self.setColorStyle(row, col, color, false);
            
            hot.updateSettings({cell: self.getColorStyles()});
            hot.render();                        
        };

        self.setAllCellsStyle = function(start_row, start_col, end_row, end_col, color) {
            for(var row = start_row; row <= end_row; ++row) {
                for(var col = start_col; col <= end_col; ++col) {
                    self.setColorStyle(row, col, color, false);
                }
            }

            self.updateSettings();
        };

        self.init = function(colorStyles, alignStyles) {
            for(var i = 0; i < colorStyles.length; ++i) {
                self.setColorStyle(colorStyles[i].row, colorStyles[i].col, colorStyles[i].color);
            }
            _alignStyles = alignStyles;
            self.updateSettings();
        };

        self.updateSettings = function() {
            hot.updateSettings({cell: self.getColorStyles().concat(self.getAlignStyles()), mergeCells: hot.mergeCells.mergedCellInfoCollection});
            hot.render();
        };
    };

    data = {!! $checklist->content !!};
    mergeCells = data.mergeInfo === undefined ? true : data.mergeInfo;
    colorStyles = data.colorInfo === undefined ? [] : data.colorInfo;
    alignStyles = data.alignInfo === undefined ? [] : data.alignInfo;

    for(var i = 0; i < mergeCells.length; ++i) {
        mergeCells[i].col = parseInt(mergeCells[i].col);
        mergeCells[i].row = parseInt(mergeCells[i].row);
        mergeCells[i].colspan = parseInt(mergeCells[i].colspan);
        mergeCells[i].rowspan = parseInt(mergeCells[i].rowspan);
    }


    var container = document.getElementById('table'),
        hot,
        settings = {
            defaultData: '',
            startCols: data.data === undefined ? 20 : data.data.length,
            startRows:  data.data === undefined ? 20 : data.data[0].length,
            rowHeaders: true,
            colHeaders: true,
            contextMenu: true,
            mergeCells: mergeCells,
            className: "htLeft htTop",
            data: data.data,
        };

    hot = new Handsontable(container, settings);

    var styles = new TableStyles(hot);

    styles.init(colorStyles, alignStyles);

    hot.updateSettings({
        contextMenu: {
            callback: function (key, options) {
                if(key == 'set_color') {
                    setTimeout(function () {                        
                        styles.setAllCellsStyle(options.start.row, options.start.col, options.end.row, options.end.col, getSelectedColor());
                    }, 100);
                }
            },
            items: {
                'row_above': {},
                'row_below': {},
                'hsep1': "---------",
                'col_left': {},
                'col_right': {},
                'hsep2': "---------",
                'remove_row': {},
                'remove_col': {},
                'hsep3': "---------",
                'undo': {},
                'redo': {},
                'hsep4': "---------",
                'make_read_only': {},
                'hsep5': "---------",
                'alignment': {},
                'hsep6': "---------",
                'mergeCells': {},
                'hsep7': "---------",
                'set_color': {
                    name: '设置颜色',
                },
            },
        },
        afterSetCellMeta: function (row, col, key, val) {
            if(key == 'className') {
                styles.setAlignStyle(row, col, val);
            }
        }
    });

    $('#store-checklist').on('click', function() {
        var mergeInfo = new Array(),
            colorInfo = new Array(),
            rowInfo = new Array(),
            colInfo = new Array(),
            alignInfo = styles.getAlignStyles(),
            colorStyles = styles.getColorStyles(),
            merges = hot.mergeCells.mergedCellInfoCollection;
            for(var i = 0; i < colorStyles.length; ++i) {
                colorInfo.push({row: colorStyles[i].row, col: colorStyles[i].col, color: colorStyles[i].mycolor});
            }

            for(var i = 0; i < merges.length; ++i) {
                mergeInfo.push(merges[i]);
            }

        hot.getPlugin('autoRowSize').recalculateAllRowsHeight();

        for(var i = 0; i < hot.countRows(); ++i) {
            rowInfo[i] = hot.getPlugin('autoRowSize').getRowHeight(i);
        }

        for(var i = 0; i < hot.countCols(); ++i) {
            colInfo[i] = hot.getColWidth(i);
        }

        fileName = $('#file-name').text().trim();
        $.ajax({
            method: 'POST',
            url: "{{ route('checklists.update', ['id' => $checklist->id]) }}",
            data: {
                data: hot.getData(),
                mergeInfo: mergeInfo,
                colorInfo: colorInfo,
                alignInfo: alignInfo,
                rowInfo: rowInfo,
                colInfo: colInfo,
                fileName: fileName,
                _method: 'PUT',
            },
            crossDomain: true,
            timeout: 5000,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                '_method': 'PUT',
            },
            success: function(response, textStatus, jqXHR) {
                $('#success-info-modal').modal('show');
                window.location = window.location.href;
            },
            error: function(jqXHR, textStatus, errorThrown) {

            }
        });
    });

    $('#file-name').blur(function() {
        text = $(this).text().trim();
        if(text == '') {
            $(this).text('默认');
        }
    });

    $('#store-template').on('click', function() {
        $('#template-name').val($('#file-name').text().trim());
        $('#store-template-modal').modal('show');
    });

    $('#store-template-btn').on('click', function() {
        var mergeInfo = new Array(),
            colorInfo = new Array(),
            rowInfo = new Array(),
            colInfo = new Array(),
            alignInfo = styles.getAlignStyles(),
            colorStyles = styles.getColorStyles(),
            merges = hot.mergeCells.mergedCellInfoCollection;
            for(var i = 0; i < colorStyles.length; ++i) {
                colorInfo.push({row: colorStyles[i].row, col: colorStyles[i].col, color: colorStyles[i].mycolor});
            }

            for(var i = 0; i < merges.length; ++i) {
                mergeInfo.push(merges[i]);
            }

        hot.getPlugin('autoRowSize').recalculateAllRowsHeight();

        for(var i = 0; i < hot.countRows(); ++i) {
            rowInfo[i] = hot.getPlugin('autoRowSize').getRowHeight(i);
        }

        for(var i = 0; i < hot.countCols(); ++i) {
            colInfo[i] = hot.getColWidth(i);
        }

        fileName = $('#file-name').text().trim();
        $.ajax({
            method: 'POST',
            url: "{{ route('checklists.update', ['id' => $checklist->id]) }}",
            data: {
                data: hot.getData(),
                mergeInfo: mergeInfo,
                colorInfo: colorInfo,
                alignInfo: alignInfo,
                rowInfo: rowInfo,
                colInfo: colInfo,
                fileName: fileName,
                _method: 'PUT',
                template_name: $('#template-name').val(),
            },
            crossDomain: true,
            timeout: 5000,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                '_method': 'PUT',
            },
            success: function(response, textStatus, jqXHR) {
                $('#store-template-modal').modal('hide');
                $('#success-info-modal').modal('show');
                window.location = window.location.href;
            },
            error: function(jqXHR, textStatus, errorThrown) {

            }
        });
    });

    $('#export-checklist').on('click', function() {
        window.location = "{{ route('checklists.export', ['id' => $checklist->id]) }}";
    });

});
</script>
@endsection