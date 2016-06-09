@extends(Config::get('sms.layout') ? Config::get('sms.layout') : 'sms::layout')

@section('content')

<div class="row">
	<div class="col-lg-6 col-lg-offset-3">
		<div class="panel panel-default">
			<div class="panel-heading">Зурвас бичих</div>
			<div class="panel-body">

				@if(Session::has('error_message'))
					<div class="alert alert-danger">
			 			{{ Session::get('error_message') }}
					</div>
				@endif

				<form action="{{ route('sms.send') }}" method="post" onsubmit="$(this).find('button').button('loading')" enctype="multipart/form-data">
					{!! csrf_field() !!}

					<div class="form-group">
						<label for="phone_number" class="form-label">Утасний дугаар:</label>
						<input type="text" class="form-control" name="phone_number" placeholder="Утасны дугаар" value="{{ old('phone_number') }}"/>
						<div class="help-block">, тэмдэгтээр тусгаарлагдсан дугаарууд.</div>
					</div>

					<div class="form-group">
						<label for="number_file">Файл ашиглан:</label>
						<input type="file" class="form-control" name="number_file" />
					</div>

					<div class="form-group">
						<label for="text" class="form-label">Зурвас:</label>
						<textarea name="text" id="text" class="form-control" rows="5">{{ old('text') }}</textarea>
						<div class="help-block">1 зурвас ихдээ 160 тэмдэгтийн урттай байна.</div>
					</div>

					<div class="form-group">
						<button type="submit" class="btn btn-primary" data-loading-text="Илгээж байна...">
							<i class="glyphicon glyphicon-send"></i> 
							Зурвас илгээх
						</button>
						<a href="{{ route('sms.log') }}" class="btn btn-default">
							Болих
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@stop