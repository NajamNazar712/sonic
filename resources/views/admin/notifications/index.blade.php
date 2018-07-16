@extends('admin.layout.master')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Notifications
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							AAA
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

	<style>
		table.table.table-sm td {
			padding: .3rem;
		}

		table.dataTable {
			margin: 0 !important;
		}

		table.dataTable thead tr th {
			padding-left: 0.3em !important;
			white-space: normal;
			word-wrap: break-word;
			border: 0 !important;
		}

		table.dataTable thead tr th:before,
		table.dataTable thead tr th:after {
			height: 20px;
			margin-bottom: -10px;
			top: auto !important;
			bottom: 50% !important;
		}

		table.dataTable thead tr th:before {
			right: 0.65em !important;
		}

		table.dataTable thead tr th:after {
			right: 0.3em !important;
		}

		table.dataTable tbody tr td {
			padding-left: 0.3em;
			padding-right: 0.3em;
		}

		#toast-bottom-center.toast-container {
			text-align: center;
		}

		#toast-bottom-center.toast-container .toast {
			display: table;
			width: auto !important;
			text-align: left;
		}
	</style>
@endsection

@section('js')
	<script>
		$(document).ready(function() {
		});
	</script>
@endsection