@extends(Config::get('sms.layout') ? Config::get('sms.layout') : 'sms::layout')

@section('head-title', 'Зурвасын түүх')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="pull-right">
			<a data-toggle="modal" data-target="#add-search" class="btn btn-primary" style="margin-left: 10px;padding: 5px 15px">
				<i class="fa fa-search"></i>
				Хайлт хийх
			</a>
			<a href="{{ route('sms.compose') }}" class="btn btn-primary">
				<i class="glyphicon glyphicon-pencil"></i>
				Зурвас бичих
			</a>

			<a href="{{ route('sms.clear') }}" class="btn btn-danger" onclick="if(! confirm('Та итгэлтэй байна уу?')) { return false;}">
				<i class="glyphicon glyphicon-pencil"></i>
				Түүх цэвэрлэх
			</a>

		</div>


		<form class="form-inline pull-left">
			<div class="form-group">
				<label for="type">Зурвасын төрөл:</label>
				<select name="type" class="form-control" onchange="$(this).parents('form').submit()">
					<option value="">Бүгд</option>
					@foreach(Config::get('sms.types') as $type => $label)
					<option value="{{ $type }}" {{ $currentType == $type || $currentModalType == $type ? 'selected' : '' }}>{{ $label }}</option>
					@endforeach
				</select>
			</div>
		</form>
	</div>
</div>

<hr>

<h3>Статистик</h3>

<div class="row">
	<div class="col-lg-3">
		<dl>
			<dt>Өнөөдөр:</dt>
			<dd>{{ Selmonal\SMS\Message::countToday($currentType) }}</dd>
		</dl>
	</div>
	<div class="col-lg-3">
		<dl>
			<dt>Өчигдөр:</dt>
			<dd>{{ Selmonal\SMS\Message::countYesterday($currentType) }}</dd>
		</dl>
	</div>
	<div class="col-lg-3">
		<dl>
			<dt>Энэ сард:</dt>
			<dd>{{ Selmonal\SMS\Message::countThisMonth($currentType) }}</dd>
		</dl>
	</div>
	<div class="col-lg-3">
		<dl>
			<dt>Нийт:</dt>
			<dd>{{ Selmonal\SMS\Message::whereType($currentType)->count() }}</dd>
		</dl>
	</div>
</div>

<hr>

<div class="row">
	<div class="col-lg-12">
		
		@if(count($messages) > 0)

		<table class="table table-striped">
			<thead>
				<th class="text-center">#</th>
				<th>Утасны дугаар</th>
				<th>Текст</th>
				<th>Байгууллага</th>
				<th class="text-center">Төлөв</th>
				<th class="text-center">Төрөл</th>
				<th class="text-right">Огноо</th>
			</thead>
			<tbody>
				@foreach($messages as $message)
				<tr>
					<td class="text-center">{{ $message->id }}</td>
					<td>{{ $message->phone_number }}</td>
					<td style="width: 40%;">{{ $message->text }}</td>
					<td>{{ $message->vendor }}</td>
					<td>{{ $message->type }}</td>
					<td class="text-center">
						@if($message->status == 'pending')
						<span class="label label-warning">
							Хүлээгдэж байна
						</span>
						@endif
						@if($message->status == 'sent')
						<span class="label label-success">
							Илгээгдсэн
						</span>
						@endif
						@if($message->status == 'failed')
						<span class="label label-danger">
							Алдаа гарсан
						</span>
						@endif
					</td>
					<td class="text-right">{{ $message->sent_at }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>

		{!! $messages->render() !!}

		@else

		<div class="alert alert-warning">
			<p class="text-center">Одоогоор илгээгдсэн зурвас байхгүй байна.</p>
		</div>

		@endif
	</div>
</div>
<div class="modal fade" role="dialog" id="add-search">
	{!! Form::open(['route' => ['sms.log'], 'method' => 'get']) !!}
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Мессеж хайх</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="type">Зурвасын төрөл:</label>
					<select name="modaltype" class="form-control">
						<option value="">Бүгд</option>
						@foreach(Config::get('sms.types') as $type => $label)
						<option value="{{ $type }}" {{ $currentModalType == $type || $currentType == $type ? 'selected' : '' }}>{{ $label }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					{!! Form::label('date', 'Огноо:', ['class' => 'control-label']) !!}
					{!! Form::text('date', isset($date) ? $date : '' , ['class' => 'form-control']) !!}
				</div>
				<div class="form-group">
					{!! Form::label('text', 'Агуулга:', ['class' => 'control-label']) !!}
					{!! Form::text('text', isset($text) ? $text : '', ['class' => 'form-control']) !!}
				</div>
				<div class="form-group">
					{!! Form::label('number', 'Утасны дугаар:', ['class' => 'control-label']) !!}
					{!! Form::text('number', isset($number) ? $number : '', ['class' => 'form-control']) !!}
				</div>
			</div>
			<div class="modal-footer">
				<!-- 	<button type="submit" class="btn btn-primary">Хайх</button> -->
				<button type="submit" data-loading-text="хайж байна..." class="btn btn-primary" autocomplete="off">
					Хайх
				</button>

				<button type="button" class="btn btn-default" data-dismiss="modal">Болих</button>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>
@stop

@section('js')
	<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
	<script type="text/javascript">
	   $( "button[data-loading-text]" ).on( "click", function( event ) {
			$(this).button( "loading" );
		});
		$(function() {
			$('input[name="date"]').daterangepicker({
				autoUpdateInput: false,
				locale: {
					cancelLabel: ' Гарах',
					applyLabel:' Сонгох'
				}
			});
			$('input[name="date"]').on('apply.daterangepicker', function(ev, picker) {
				$(this).val(picker.startDate.format('YYYY-MM-DD') + ',' + picker.endDate.format('YYYY-MM-DD'));
			});
			$('input[name="date"]').on('cancel.daterangepicker', function(ev, picker) {
				$(this).val('');
			});
		});
	</script>
@stop