@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row text-center">
        <div class="col col-md-12">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="pn" value="pn" {{ $capDo == 'pn' ? "checked" : '' }}>
                <label class="form-check-label" for="pn">
                    Phàm Nhân
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="lk" value="lk" {{ $capDo == 'lk' ? "checked" : '' }}>
                <label class="form-check-label" for="lk">
                    Luyện Khí
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="tc" value="tc" {{ $capDo == 'tc' ? "checked" : '' }}>
                <label class="form-check-label" for="tc">
                    Trúc Cơ
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="kd" value="kd" {{ $capDo == 'kd' ? "checked" : '' }}>
                <label class="form-check-label" for="kd">
                    Kim Đan
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="na" value="na" {{ $capDo == 'na' ? "checked" : '' }}>
                <label class="form-check-label" for="na">
                    Nguyên Anh
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="ht" value="ht" {{ $capDo == 'ht' ? "checked" : '' }}>
                <label class="form-check-label" for="ht">
                    Hóa Thần
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="lh" value="lh" {{ $capDo == 'lh' ? "checked" : '' }}>
                <label class="form-check-label" for="lh">
                    Luyện Hư
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="hopthe" value="hopthe" {{ $capDo == 'hopthe' ? "checked" : '' }}>
                <label class="form-check-label" for="hopthe">
                    Hợp Thể
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="daithua" value="dt" {{ $capDo == 'dt' ? "checked" : '' }}>
                <label class="form-check-label" for="dt">
                    Đại Thừa
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="dk" value="dk" {{ $capDo == 'dk' ? "checked" : '' }}>
                <label class=" form-check-label" for="dk">
                    Độ Kiếp
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="capDo" id="tien" value="tien" {{ $capDo == 'tien' ? "checked" : '' }}>
                <label class=" form-check-label" for="tien">
                    Vũ Hóa Tiên
                </label>
            </div>
        </div>
    </div>
    @include('account.table', ['accounts' => $accounts])
    <!-- Modal -->
    <!-- End Modal -->
</div>
@endsection



@push('scripts')
<!-- <script src="{{ asset('js/datatables.min.js') }}"></script> -->
<script type="text/javascript">
    $(document).ready(function() {
        $.noConflict();
        $('#acc-table').DataTable({
            fixedHeader: true,
            paging: false
        });

        $('input[name="capDo"]').change((event) => {
            window.location = window.location.pathname + '?cap-do=' + event.target.value;
        });
    });
</script>
@endpush
<style>
    .inventory-item {
        padding: 2px 6px;
        margin-right: 2px;
        color: #212121;
        background-color: transparent;
        background-image: none;
        border: 1px solid #212121;
        border-radius: .2rem;
        cursor: pointer;
        display: inline-block;
        margin-top: 4px;
    }

    .tu-luyen {
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .tien-do {
        width: 44px;
    }

    .btn-acction button {
        margin-left: 8px;
    }

    .reward {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    form {
        margin: 0;
        margin-top: 6px;
    }

    .actions {
        text-align: center;
    }

    .actions form {
        display: inline-block;
    }

    .table td,
    .table th {
        vertical-align: middle !important;
    }

    .tableFixHead {
        overflow-y: auto;
        height: 100px;
    }

    .tableFixHead thead th {
        position: sticky;
        top: 0;
    }

    table.dataTable>thead .sorting:before,
    table.dataTable>thead .sorting_asc:before,
    table.dataTable>thead .sorting_desc:before,
    table.dataTable>thead .sorting_asc_disabled:before,
    table.dataTable>thead .sorting_desc_disabled:before {
        content: "";
    }

    table.dataTable>thead .sorting:after,
    table.dataTable>thead .sorting_asc:after,
    table.dataTable>thead .sorting_desc:after,
    table.dataTable>thead .sorting_asc_disabled:after,
    table.dataTable>thead .sorting_desc_disabled:after {
        content: "";
    }

    #accountInfo {
        font-size: 16px;
        margin-bottom: 24px;
        font-weight: 600;
    }

    #accountBangInfo {
        font-size: 16px;
        margin-bottom: 24px;
        font-weight: 600;
    }
</style>
<script>
    function updateReward(accountId) {
        $('form#form-' + accountId).submit();
    }

    function updateNsd(accountId) {
        $('form#form-nsd-' + accountId).submit();
    }

    function updateDt(accountId) {
        $('form#form-dt-' + accountId).submit();
    }
</script>