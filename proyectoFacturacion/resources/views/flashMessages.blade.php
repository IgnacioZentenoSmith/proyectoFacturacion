<div class="my-3 text-center">
	@if ($message = Session::get('success'))
	<div class="alert alert-success alert-block shadow-sm">
		<button type="button" class="close" data-dismiss="alert">×</button>	
					<strong>{{ $message }}</strong>
	</div>
	@endif


	@if ($message = Session::get('error'))
	<div class="alert alert-danger alert-block shadow-sm">
		<button type="button" class="close" data-dismiss="alert">×</button>	
					<strong>{{ $message }}</strong>
	</div>
	@endif


	@if ($message = Session::get('warning'))
	<div class="alert alert-warning alert-block shadow-sm">
		<button type="button" class="close" data-dismiss="alert">×</button>	
		<strong>{{ $message }}</strong>
	</div>
	@endif


	@if ($message = Session::get('info'))
	<div class="alert alert-info alert-block shadow-sm">
		<button type="button" class="close" data-dismiss="alert">×</button>	
		<strong>{{ $message }}</strong>
	</div>
	@endif

	@if ($errors->any())
    <div class="alert alert-danger shadow-sm">
        @foreach ($errors->all() as $error)
						<button type="button" class="close" data-dismiss="alert">×</button>
						<strong>{{ $error }}</strong>	
        @endforeach
    </div>
@endif
</div>