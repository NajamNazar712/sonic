@extends('admin.layout.master')

@section('title', 'Ticker')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Ticker
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<div class="row justify-content-center">
								<div class="col-12 col-sm-12 col-md-12 col-lg-12">
									<form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.ticker.store') }}" novalidate="novalidate">
										{{ csrf_field() }}

										<div class="form-group">
											<input type="text" name="admin_ticker" class="form-control admin_ticker" placeholder="Admin Ticker" value="{{ $admin_ticker }}">
										</div>

										<div class="form-group">
											<input type="text" name="shipper_ticker" class="form-control shipper_ticker" placeholder="Shipper Ticker" value="{{ $shipper_ticker }}">
										</div>

										<button type="submit" class="btn btn-primary">Update</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection