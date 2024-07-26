@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col col-md-6">
            <h3>Danh sách account</h3>
        </div>
    </div>
    <table class="table table-bordered table-sm">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">ID</th>
            <th scope="col">Ngoại hiệu</th>
            <th scope="col">Tiến độ</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($accounts as $key => $account)
            <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <td>
                    <a href="{{ action('AccountController@show', ['id' => $account['id']]) }}">
                        {{ $account['account_id'] }}
                    </a>
                </td>
                <td>
                    <a href={{"https://truyencv.com/member/" . $account['account_id']}}>
                        {{ $account['account_name'] }}
                    </a>
                </td>
                <td>{{ $account['progress'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    const accountIds = {!! $accounts->pluck('id')->toJson() !!};
    const intervals = [];
    setTimeout(function() {
        if ($ !== undefined) {
            for (let index = 0; index < accountIds.length; index++) {
                const accountId = accountIds[index];
                let iii = setInterval(function () {
                    $.get('accounts/' + accountId + '/nsd');
                }, 12000);
            }
        }
    }, 5000);
</script>
@endpush