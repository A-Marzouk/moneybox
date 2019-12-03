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
        <div id="editPortfolioComponent">
            <edit-client-portfolio :user="{{$user}}"></edit-client-portfolio>
        </div>
    </div>
@endsection
