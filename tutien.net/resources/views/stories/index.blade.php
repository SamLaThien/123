@extends('layouts.app')

@section('content')
<div class="container">
    {!! Form::open(['url' => 'stories', 'method' => 'post']) !!}
        <div class="form-group" >
            <label for="story_id">ID Truyện</label>
            <input type="text" class="form-control" id="story_id" name="story_id">
            <input type="hidden" name="name" value="">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    {!! Form::close() !!}

    <hr>
    <table class="table table-bordered table-sm">
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID Truyện</th>
                <th scope="col">Tên truyện</th>
                <th scope="col">Số chương</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stories as $key => $story)
                <tr>
                    <th scope="row">{{ $story['story_id'] }}</th>
                    <td>
                        <a href="https://truyencv.com/{{ vn_to_str($story['name']) }}">
                            {{ $story['name'] }}
                        </a>
                    </td>
                    <td>{{ $story['total_chapter'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
