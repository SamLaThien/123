@extends('layouts.app')

@section('content')
<div class="container">
    {!! Form::open(['url' => 'accounts/' . $account['id'], 'method' => 'put']) !!}
        <div class="form-group">
            <label for="group">Phân nhóm</label>
            <input type="number" class="form-control" id="group" placeholder="Chọn nhóm" name="group" value="{{ $account->group }}">
        </div>
        <div class="form-group">
            <label for="cookie">Cookie</label>
            <textarea class="form-control" type="text" id="cookie" name="cookie" placeholder="Enter web cookie" row="8">
                {{ $account->cookie }}
            </textarea>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-outline-primary btn-sm">Cập nhật</button>
        </div>
    {!! Form::close() !!}
</div>
@endsection
