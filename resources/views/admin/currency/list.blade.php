@extends('admin/layouts.backend')
@section('title', 'All Curency')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>All Curency</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">All Curency</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>


        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- Menu List -->
                    <div class="col-md-12">
                        <div class="card">
							<div class="card-header ">
								<h3 class="card-title">All Curency</h3>
									<a href="{{ url('/admin/currency/add-currency') }}" class="btn btn-success btn float-right">
										<i class="fas fa-plus"></i> Create
									</a>
							</div>

						<div class="card">
 
						<div class="card-body">
							@if(session()->has('success'))
								<div class="alert alert-success">
									<strong>Success!</strong> {{ session()->get('success') }}
								</div>
							@endif
							@if(session()->has('error'))
								<div class="alert alert-danger">
									<strong>Error!</strong> {{ session()->get('error') }}
								</div>
							@endif

							<table id="currencyTable" class="table table-bordered table-hover dt-responsive nowrap w-100">
								<thead>
									<tr>
										<th>Code</th>
										<th>Symbol</th>
										<th>Rate</th>
										<th>Is default</th>
										<th>Display Order</th>
										<th>Created At</th>
										<th>Action</th>
									</tr>
								</thead>
							</table>
					</div>
				</div>
						</div>	
					</div>	
						
                    </div>
                    <!-- /.col-md-8 -->
                </div>
                <!-- /.row -->
            </div>
             
        </section>
    </div>
	<script>
	  function validateDelete(){ 
		var confirms = confirm('Do you want to delete ?');
		if(confirms==false){
			return false;
		}
	  }
	</script>
	<script>
    $(document).ready(function () {
        $('#currencyTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("/admin/currency") }}',
            columns: [
                { data: 'code', name: 'code', orderable: false, searchable: false },
                { data: 'symbol', name: 'symbol' },
                { data: 'rate', name: 'rate' },
                { data: 'is_default', name: 'is_default' },
                { data: 'display_order', name: 'display_order' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>



@endsection
