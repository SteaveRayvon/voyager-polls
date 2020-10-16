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
                    <div class="col-md-12">
                        <ul class="nav nav-pills">
                            <li class="active"><a data-toggle="pill" href="#home">Результаты опроса</a></li>
                            <li><a data-toggle="pill" href="#menu1">График 1</a></li>
                            <li><a data-toggle="pill" href="#menu2">График 2</a></li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div id="home" class="tab-pane fade in active">

                        <div class="container-fluid">
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
                                        @php
                                            $i=1;
                                            $correct_num = 0;
                                            $incorrect_num = 0;
                                        @endphp
                                        @foreach($answer_correct as $answer)
                                            <tr role="row" class="@if($answer->id == $answer->correct) correct @else not_correct @endif">
                                                <td>{{ $answer->answer_idx }}</td>
                                                <td>{{ $answer->name }} ( {{ $answer->login }} )</td>
                                                <td>@if($answer->id == $answer->correct) <b>Верно = </b> @else Не Верно =  @endif {{ $answer->answer }}</td>
                                                <td>{{ $answer->created_at }}</td>
                                            </tr>
                                            @php
                                                if($answer->id == $answer->correct) {
                                                    $correct_num += 1;
                                                } else {
                                                    $incorrect_num += 1;
                                                }
                                            @endphp
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
                    <div id="menu1" class="tab-pane fade">
                        <div class="container-fluid">
                            @if($poll->type == 'variant')
                                <div class="col-md-6">

                                    <canvas id="myChart" width="400" height="400"></canvas>

                                </div>
                            @endif
                        </div>
                    </div>
                    <div id="menu2" class="tab-pane fade">
                        <div class="container-fluid">
                            <div class="col-md-6">

                                <canvas id="myChart2" width="400" height="400"></canvas>

                            </div>
                        </div>
                    </div>

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
	@if($poll->type == 'variant')
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.0/Chart.min.js"></script>
		<script>
			Chart.pluginService.register({
				beforeRender: function (chart) {
					if (chart.config.options.showAllTooltips) {
						// create an array of tooltips
						// we can't use the chart tooltip because there is only one tooltip per chart
						chart.pluginTooltips = [];
						chart.config.data.datasets.forEach(function (dataset, i) {
							chart.getDatasetMeta(i).data.forEach(function (sector, j) {
								chart.pluginTooltips.push(new Chart.Tooltip({
									_chart: chart.chart,
									_chartInstance: chart,
									_data: chart.data,
									_options: chart.options,
									_active: [sector]
								}, chart));
							});
						});

						// turn off normal tooltips
						chart.options.tooltips.enabled = false;
					}
				},
				afterDraw: function (chart, easing) {
					if (chart.config.options.showAllTooltips) {
						// we don't want the permanent tooltips to animate, so don't do anything till the animation runs atleast once
						if (!chart.allTooltipsOnce) {
							if (easing !== 1)
								return;
							chart.allTooltipsOnce = true;
						}

						// turn on tooltips
						chart.options.tooltips.enabled = true;
						Chart.helpers.each(chart.pluginTooltips, function (tooltip) {
							tooltip.initialize();
							tooltip.update();
							// we don't actually need this since we are not animating tooltips
							tooltip.pivot();
							tooltip.transition(easing).draw();
						});
						chart.options.tooltips.enabled = false;
					}
				}
			});
		</script>
		<script>
			var ctx = document.getElementById('myChart').getContext('2d');
			var myChart = new Chart(ctx, {
				// type: 'bar',
				type: 'pie',
				data: {
					labels: ['Верно', 'Не верно',],
					datasets: [{
						label: '# of Votes',
						data: [{{ $correct_num }}, {{$incorrect_num}}],
						backgroundColor: [
							'rgb(0,208,34)',
							'rgb(193,0,0)',
						],
						borderColor: [
							'rgb(0,208,34)',
							'rgb(193,0,0)',
						],
						borderWidth: 1
					}]
				},
				options: {
					showAllTooltips: true,
					scales: {
						yAxes: [{
							ticks: {
								beginAtZero: true
							}
						}]
					}
				}
			});
		</script>
		@php $i=1; @endphp
		<script>
			var ctx2 = document.getElementById('myChart2').getContext('2d');
			var myChart2 = new Chart(ctx2, {
				type: 'horizontalBar',
				data: {
					labels: [@foreach($poll->questions as $question)@foreach($question->answers as $answer)'{{ $answer->answer }}'@if(count($question->answers) != $i), @endif @php $i++; @endphp @endforeach @endforeach],
					// labels: ['17%','25%', '42%'],
					datasets: [{
						label: 'Количество голосов',
						data: [@foreach($poll->questions as $question)@foreach($question->answers as $answer){{ $answer->votes }}@if(count($question->answers) != $i), @endif @php $i++; @endphp @endforeach @endforeach],
						//   data: [41, 150, 918],
						backgroundColor: [
							"#FF6384",
							"#36A2EB",
							"#FFCE56"
						],
						borderColor: [
							"#FF6384",
							"#36A2EB",
							"#FFCE56"
						],
						borderWidth: 1
					}]
				},
				options: {
					showAllTooltips: true
				}
			});
		</script>
	@endif
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
