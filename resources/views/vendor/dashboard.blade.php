@extends('vendor/layouts.backend')
@section('title', 'Escx')
@section('content')
     <div class="content-wrapper admin-dashboard-content">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <!-- ./col -->
                    <div class="col-lg-3 col-3">
                        <!-- small box -->
                        <div class="small-box bg-warning text-center">
                            <div class="inner">
                                <h3>Stores</h3>

                                <p>{{$totalStores}}</p>
                            </div>

                            <a href="{{url('vendor/store')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->


                    <div class="col-lg-3 col-3">
                        <!-- small box -->
                        <div class="small-box bg-danger text-center">
                            <div class="inner">
                                <h3>Orders</h3>

                                <p>{{$totalOrder}}</p>
                            </div>

                            <a href="{{url('vendor/product-orders')}}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-3">
                        <!-- small box -->
                        <div class="small-box bg-success text-center">
                            <div class="inner">
                                <h3>Products</h3>

                                <p>{{$totalProducts}}</p>
                            </div>

                            <a href="{{url('vendor/products')}}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-3">
                        <!-- small box -->
                        <div class="small-box bg-info text-center">
                            <div class="inner">
                                <h3>Revenue</h3>
                                @foreach($revenues as $rev)
									<strong> {{ $rev->currency }}{{ number_format($rev->total, 2) }}</strong><br/>
								@endforeach
                            </div>

                           <!-- <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>
                  
                    <!-- ./col -->
                </div>
				
				<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

<!-- Calendar Container -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Calendar</h3>
      </div>
      <div class="card-body">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</div>

<!-- FullCalendar JS (global build) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    // ✅ Use FullCalendar.Calendar from global bundle
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth'
    });

    calendar.render();
  });
</script>

<!-- Optional styling -->
<style>
  #calendar {
    padding: 10px;
  }
</style>
				
            </div><!-- /.container-fluid -->
        </div>

    </div>
    @endsection
