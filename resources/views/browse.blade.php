@extends('voyager::master')

@section('css')

    <style type="text/css">
        #vueify .panel-body, #vueify .panel-bordered>.panel-body{
            padding:0px;
            padding-top:0px;
        }
    </style>

@endsection

@section('content')
    <div class="page-content read container-fluid">
        <ul class="nav nav-tabs" id="myTab2">
            <li class="active"><a href="/admin/polls">Опросы</a></li>
            <li><a href="/admin/achallenges" >A-Challenges</a></li>
            <li><a href="/admin/charities">Благотворительность</a></li>
            <li><a href="/admin/poll-variants">Опрос со своим вариантом</a></li>
        </ul>
    </div>

    <div class="padding-top">

        <div id="app">

            <h1 class="page-title">
                <i class="voyager-bar-chart"></i> Опросы
                <a href="{{ route('voyager.polls.add') }}" class="btn btn-success">
                    <i class="voyager-plus"></i> Добавить
                </a>
            </h1>

            <div id="polls">
                <div class="container-fluid">

                    <div class="panel panel-bordered">
                        <div class="panel-heading">
                            <h3 class="panel-title">Опросы</h3>
                        </div>
                        <div class="panel-body">

                            <table id="dataTable" class="table table-hover dataTable no-footer" role="grid" aria-describedby="dataTable_info">
                                <thead>
                                <tr role="row">
                                    <th class="sorting">Имя</th>
{{--                                    <th class="sorting">Слаг</th>--}}
                                    <th class="sorting">Создан</th>
                                    <th class="sorting">Обновлен</th>
                                    <th class="actions sorting">Действие</th></tr>
                                </thead>
                                <tbody>

                                @foreach($polls as $poll)

                                    <tr role="row" class="odd">
                                        <td width="150">{{ $poll->name }}</td>
{{--                                        <td>{{ $poll->slug }}</td>--}}
                                        <td>{{ Carbon\Carbon::parse($poll->created_at)->toDayDateTimeString() }}</td>
                                        <td>{{ Carbon\Carbon::parse($poll->modified_at)->toDayDateTimeString() }}</td>
                                        <td>
                                            <div class="btn-sm btn-danger pull-right delete" data-id="{{ $poll->id }}" id="delete-1">
                                                <i class="voyager-trash"></i><span class=" hidden-xs hidden-sm"> Удалить</span>
                                            </div>
                                            <a href="{{ route('voyager.polls.edit', ['id' => $poll->id]) }}" class="btn-sm btn-primary pull-right edit">
                                                <i class="voyager-edit"></i><span class=" hidden-xs hidden-sm"> Редактировать </span>
                                            </a>
                                            <a href="{{ route('voyager.polls.status', ['id' => $poll->id]) }}" class="btn-sm btn-warning pull-right">
                                                <i class="voyager-eye"></i><span class=" hidden-xs hidden-sm"> @php if($poll->status == 1) { echo 'Выключить'; } else { echo 'Включить'; } @endphp</span>
                                            </a>
                                            <a href="{{ route('voyager.polls.read', ['id' => $poll->id]) }}" class="btn-sm btn-warning pull-left">
                                                <i class="voyager-eye"></i><span class=" hidden-xs hidden-sm"> Результаты</span>
                                            </a>
                                        </td>
                                    </tr>


                                @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>

            <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><i class="voyager-trash"></i> Are you sure you want to delete
                                this Poll?</h4>
                        </div>
                        <div class="modal-footer">
                            <form action="{{ route('voyager.polls.delete') }}" id="delete_form" method="POST">
                                {{ method_field("DELETE") }}
                                {{ csrf_field() }}
                                <input type="hidden" value="" id="delete_id" name="id">
                                <input type="submit" class="btn btn-danger pull-right delete-confirm"
                                       value="Yes, delete this poll">
                            </form>
                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

        </div>
    </div>


@endsection
@section('javascript')
    <!-- DataTables -->
{{--    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>--}}
{{--    <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>--}}
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>--}}
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>--}}
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>--}}
{{--    <link href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css" rel="stylesheet">--}}

    <script>
        $('document').ready(function(){
            $('td').on('click', '.delete', function (e) {
                var form = $('#delete_form')[0];

                $('#delete_id').val( $(this).data('id') );
                console.log(form.action);

                $('#delete_modal').modal('show');
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            var table = $('#dataTable').DataTable(
                {!! json_encode(
                array_merge([
                    "order" => [[ 0, "asc" ]],
                    "language" => __('voyager::datatable'),
                    "columnDefs" => [['targets' => -1, 'searchable' =>  true, 'orderable' => true]],
                ],
                config('voyager.dashboard.data_tables', []))
            , true) !!}
            );

            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });
        });
    </script>
@endsection


