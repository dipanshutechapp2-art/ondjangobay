@extends('admin/layouts.backend')
@section('title', 'Shipping Prices')
@section('content')

    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>Shipping Prices</h1>
                    </div>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Shipping Prices</li>
                        </ol>
                    </div>

                </div>
            </div>
        </section>
		
        <section class="content">
            <div class="container-fluid">
					  <!-- Bootstrap Alerts -->
				@if (session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> {{ session('success') }}
					</div>
				@endif

				@if (session('error'))
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Error!</strong> {{ session('error') }}
					</div>
				@endif

				@if ($errors->any())
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Please fix the following errors:</strong>
						<ul class="mt-2 mb-0">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
                <div class="row">

    <!-- Menu List -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title">Shipping Prices</h3>
            <a href="{{ route('shipping-prices.create') }}" class="btn btn-success btn float-right">
                <i class="fas fa-plus"></i> Create
            </a>
            </div>
            <div class="card-body">
				<table id="shippingPricesTable" class="table table-hover dt-responsive nowrap w-100">
					<thead>
						<tr>
						{{-- <th>Country</th> --}}
							<th>Option</th>
							<th>Price</th>
							<th>ETA</th>
						{{-- <th>Default</th> --}}
							<th>Display Order</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					@foreach($prices as $price)
						<tr>
						{{-- <td>{{ $price->country->name ?? 'GLOBAL' }}</td> --}}
						    <td>{{ $price->option->title }}</td>
						    <td>{{ $price->price }}</td>
						    <td>{{ $price->eta_min }}–{{ $price->eta_max }} days</td>
							{{-- <td class="text-center">
								<span class="badge 
									{{ $price->is_default ? 'badge-success' : 'badge-secondary' }}
									set-default-badge"
									data-id="{{ $price->id }}"
									style="cursor:pointer;">
									
									{{ $price->is_default ? 'DEFAULT' : 'SET DEFAULT' }}
								</span>
							</td> --}}

							<td class="text-center">
								<span class="badge badge-info sort-order-badge"
									  data-id="{{ $price->id }}"
									  style="cursor:pointer;">
									{{ $price->sort_order }}
								</span>

							</td>
						    <td>
								<label class="switch">
									<input type="checkbox"
										   class="toggle-status"
										   data-id="{{ $price->id }}"
										   {{ $price->is_active ? 'checked' : '' }}>
									<span class="slider round"></span>
								</label>
							</td>
							<td>
								<a href="{{ route('shipping-prices.edit', $price->id) }}"
								   class="btn btn-sm btn-primary">
									<i class="fas fa-edit"></i> Edit
								</a>
							</td>
						</tr>
					@endforeach
				</table>
            </div>
        </div>
    </div>
    <!-- /.col-md-8 -->
    </div>
    <!-- /.row -->
    <!-- Button trigger modal -->

      
    </div>

    </section>
    </div>
<script>
    $(document).ready(function () {
        $('#shippingPricesTable').DataTable({
            processing: true,
            serverSide: false,
        });
    });
</script>
<script>
$(document).on('change', '.toggle-status', function () {
    let checkbox = $(this);
    let id = checkbox.data('id');

    $.ajax({
        url: "{{ url('admin/shipping-prices') }}/" + id + "/toggle-status",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function (res) {
            if (res.success) {

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.status ? 'Shipping enabled' : 'Shipping disabled',
                    showConfirmButton: false,
                    timer: 1500
                });

            } else {

                checkbox.prop('checked', !checkbox.prop('checked'));

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Failed to update status.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        },
        error: function () {

            checkbox.prop('checked', !checkbox.prop('checked'));

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Server error. Please try again.',
                showConfirmButton: false,
                timer: 1500
            });
        }
    });
});
</script>

<script>
$(document).on('click', '.set-default-badge', function () {

    let badge = $(this);
    let id = badge.data('id');

    $.post("{{ url('admin/shipping-prices') }}/" + id + "/set-default", {
        _token: "{{ csrf_token() }}"
    }, function (res) {

        if (res.success) {

            // sab badges reset
            $('.set-default-badge')
                .removeClass('badge-success')
                .addClass('badge-secondary')
                .text('SET DEFAULT');

            // selected badge update
            badge
                .removeClass('badge-secondary')
                .addClass('badge-success')
                .text('DEFAULT');

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Default shipping updated',
                showConfirmButton: false,
                timer: 1200
            });
        }
    });
});
</script>

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 55px;
  height: 26px;
}

.switch input { display:none; }

.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 30px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #28a745;
}

input:checked + .slider:before {
  transform: translateX(28px);
}
</style>

@endsection
