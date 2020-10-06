@extends('voyager::master')

@section('css')

	<style type="text/css">
		.poll-results-meter{
			width:100%;
			float:right;
		}
		.label-bar{
			display:block;
			text-align:left;
			padding: 8px 10px;
    		margin-top: 2px;
			margin-bottom:10px;
		}
		ul.poll-results:after{
			content:'';
			clear:both;
			display:block;
		}
		ul.poll-results{
			margin-bottom: 40px;
    		margin-top: 20px;
		}
		.polls-back{
			position: relative;
    		top: -18px;
		}
		.polls-back i{
			position: relative;
    		top: 1px;
		}
		.poll-container h1{
			margin-top:0px;
		}
	</style>

@endsection

@section('content')

<div class="padding-top">

	<div id="app">

		<h1 class="page-title">
        	<i class="voyager-bar-chart"></i> {{ $poll->name }}
        </h1>


		<div id="polls">
			<div class="container-fluid">
				<div class="col-md-12">
					<a href="{{ route('voyager.polls') }}" class="polls-back"><i class="voyager-angle-left"></i> Назад</a>
				</div>
				<div class="col-md-6">
					<poll slug="{{ $poll->slug }}" prefix="{{ env('ROUTE_PREFIX') }}"></poll>
				</div>
                @if($poll->type == 'variant')

                <table id="dataTable" width="100%" class="table table-hover dataTable no-footer type-{{ $poll->type }}" role="grid" aria-describedby="dataTable_info">
                    <thead>
                    <tr role="row">
                        <th>#</th>
                        <th class="sorting">Имя</th>
                        <th class="sorting">Ответ</th>
                        <th class="sorting">Время</th>
                    </thead>
                    <tbody>
                    @if($answer_correct)
                        @php $i=1; @endphp
                        @foreach($answer_correct as $answer)
                            <tr role="row" class="@if($answer->id == $answer->correct) correct @else not_correct @endif">
                                <td>{{ $answer->answer_idx }}</td>
                                <td>{{ $answer->name }} ( {{ $answer->login }} )</td>
                                <td>@if($answer->id == $answer->correct) <b>Верно = </b> @else Не Верно =  @endif {{ $answer->answer }}</td>
                                <td>{{ $answer->created_at }}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>

                @else
                <table id="dataTable" width="100%" class="table table-hover dataTable no-footer" role="grid" aria-describedby="dataTable_info">
                    <thead>
                    <tr role="row">
                        <th>#</th>
                        <th class="sorting">Имя</th>
                        <th class="sorting">Ответ</th>
                        <th class="sorting">Время</th>
                    </thead>
                    <tbody>
                    @php // dd($pollx) @endphp
                    @if(isset($pollx))
                        @php $i=1; @endphp
                        @foreach($pollx as $answer)
                            <tr role="row" class="odd">
                                <td>{{ $answer->answer_idx }}</td>
                                <td>{{ $answer->name }} ( {{ $answer->login }} )</td>
                                @if($answer->answer) <td>{{ $answer->answer }}</td> @endif
                                <td>{{ $answer->created_at }}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
				<div class="col-md-6">
					<div class="panel panel-default" data-margin-top="80">
						<div class="panel-title">
							<h2>Результаты опроса</h2>
						</div>
						<div class="panel-body">
							@foreach($poll->questions as $question)
								@php $total_votes = $question->totalVotes(); @endphp
								<h4>{{ $question->question }} <small class="label label-success">{{ $total_votes }}
{{--										{{ Str::plural( __('polls.total'), $total_votes)  }}--}}
									</small></h4>
								<ul class="poll-results">
								@foreach($question->answers as $answer)
									@php $percentage = 0; @endphp
									@if($total_votes != 0)
										@php $percentage = intval(100*($answer->votes/$total_votes)); @endphp
									@endif
									<li>{{ $answer->answer }}<span class="poll-results-meter"><span class="label label-default label-bar" style="width:{{ $percentage }}%">{{ $percentage }}% {{ __('polls.with') }} <b>{{ $answer->votes }}</b> {{ __('polls.votes') }}</span></span></li>
								@endforeach
								</ul>
							@endforeach
						</div>

					</div>
				</div>
                @endif
			</div>
		</div>


	</div>
</div>


@endsection

@section('javascript')
    <!-- DataTables -->
    <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
    <link href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css" rel="stylesheet">
	<script type="text/javascript" src="{{ asset('/js/polls-app.js') }}"></script>
    <script>
        $(document).ready(function () {
            var table = $('#dataTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel'
                ]
            });

            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });
        });
    </script>
@endsection
