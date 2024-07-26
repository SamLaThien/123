@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col col-md-8">
            <h3>Danh sách luyện đan sư</h3>
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card card-body">
            
        </div>
    </div>
    <hr />
    <table class="table table-bordered table-sm">
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Ngoại hiệu</th>
                <th scope="col">Đan phương</th>
                <th scope="col">Đang luyện</th>
            </tr>
        </thead>
    </table>
</div>
@endsection
<style>
    .tu-luyen {
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .tien-do {
        width: 44px;
    }

    .reward {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .reward form {
        margin: 0;
        margin-top: 6px;
    }
</style>
<script>
    function updateReward(accountId) {
        console.log(accountId);
        $('form#form-' + accountId).submit();
    }
</script>
