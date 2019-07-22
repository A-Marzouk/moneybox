@extends('layouts.app')

@section('content')
<div class="container">
    <div>
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
    </div>
    <div id="salesList">
        <sales-list></sales-list>
    </div>
</div>
@endsection
