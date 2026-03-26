@extends('vendor/layouts.backend')
@section('title','Import Errors')
@section('content')
<div class="content-wrapper admin-dashboard-content">
    <section class="content-header">
        <div class="container-fluid">
            <x-sweet-alert />
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Import Errors</h1></div>
                <div class="col-sm-6 text-right">
                    <form method="POST" action="{{ route('vendor.partner-products.clear_import_errors') }}">
                        @csrf
                        <button class="btn btn-danger">Clear All</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="content container-fluid">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Below are the rows that failed to import and the reason. Fix the spreadsheet and try again.</p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Reason</th>
                            <th>Meta</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->product_name ?? '—' }}</td>
                            <td>{{ $log->reason }}</td>
                            <td>
                                @if($log->meta)
                                    <button class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#meta-{{ $log->id }}">Show</button>
                                    <div id="meta-{{ $log->id }}" class="collapse mt-2">{{ $log->meta }}</div>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
