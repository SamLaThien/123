@extends('layouts.app')

@section('content')
<div class="container">
    <p>
        @foreach($accounts as $account)
        {{ $account['progress'] }} {{ $account['account_id'] }} {{ $account['cookie'] }}
        <br>
        @endforeach
    </p>
</div>
@endsection