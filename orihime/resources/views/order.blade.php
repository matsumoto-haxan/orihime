@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card cardmargin">
                <div class="card-header">オーダーページ</div>
                <div class="card-body">
                    <p>{{ $message }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">オーダーページ</div>
                <div class="card-body">
                    @foreach ($testarray as $testword)
                        {{ $testword }}<br>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
