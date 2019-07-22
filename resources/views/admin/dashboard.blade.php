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
        <div id="productsList" class="pb-2">
            <products-list></products-list>
        </div>

        <hr>

        <div id="managersList" class="pt-2">
            <managers-list></managers-list>
        </div>
    </div>
@endsection
