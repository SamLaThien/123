@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col col-md-6">
            <h3>Danh sách account</h3>
        </div>
        <div class="col col-md-6 btn-acction">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-outline-primary btn-sm float-right" data-toggle="modal" data-target="#exampleModal">
                Thêm mới account
            </button>
            <div class="btn-group float-right" role="group" aria-label="Basic example" data-toggle="tooltip" data-placement="top" title="Click vào sẽ cập thao tác trên toàn bộ account">
                <a href="/nop_bac_all" class="btn btn-outline-dark btn-sm float-right">Nộp bạc</a>
                <a href="/nop_do_all" class="btn btn-outline-dark btn-sm float-right">Nộp đồ</a>
            </div>
            <button type="button" class="mr-2 btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#importModal">
                Import
            </button>
            <button type="button" class="mr-2 btn btn-outline-success btn-sm float-right" data-toggle="modal" data-target="#importCookieModal">
                Động
            </button>
        </div>
    </div>
    <div class="rơw">
	    Tai san: {{ number_format($taiSan) }} bac
    </div>
    <div class="rơw text-center">
        @foreach($inventories as $key => $inventory)
            <span class="inventory-item" data-toggle="modal" data-target="#modal-{{ str_replace(' ', '-', vn_to_str($inventory[0]->item_id)) }}">
                {{ $key }} ( {{ $inventory->sum('amount') }} )
            </span>
        @endforeach
    </div>
    <hr/>
    
    @include('account.account_table', ['accounts' => $accounts])
    
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm mới account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '/accounts', 'method' => 'post']) !!}
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <div class="form-group">
                        <label for="account_id">TruyenCV ID</label>
                        <input type="text" class="form-control" id="account_id" placeholder="Nhập ID truyencv" name="account_id">
                    </div>
                    <div class="form-group">
                        <label for="cookie">Cookie</label>
                        <textarea rows="4" class="form-control" type="text" id="cookie" name="cookie" placeholder="Nhập web cookie" row="8">cookie: USER=</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    {!! Form::open([
                        'url' => 'import',
                        'method' => 'post',
                    ]) !!}
                    <div class="form-group">
                        <label for="accounts">Account & Cookie</label>
                        <textarea rows="8" class="form-control" type="text" name="accounts" placeholder="TCV_ID COOKIE"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importCookieModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    {!! Form::open([
                        'url' => 'import-dong',
                        'method' => 'post',
                    ]) !!}
                    <div class="form-group">
                        <label>Danh sách cookie động hiện tại</label>
                        <ol>
                            @foreach ($cookies as $cookie)
                                <li>{{ $cookie }}</li>
                            @endforeach
                        </ol>
                    </div>
                    <div class="form-group">
                        <label for="cookies">Cookie động</label>
                        <textarea rows="8" class="form-control" type="text" name="cookies" placeholder="Mỗi cookie 1 dòng"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <button type="button" class="btn btn-success" onclick="restartDong()">Restart động</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    @foreach($inventories as $key => $data)
        @include('account.inventories', ['inventories' => $data, 'type' => str_replace(' ', '-', vn_to_str($data[0]->item_id)) ])
    @endforeach

</div>
@endsection
@push('scripts')
    <!-- <script src="{{ asset('js/datatables.min.js') }}"></script> -->
    <script type="text/javascript" >
        $(document).ready( function () {
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

    .pagination svg {
        height: 20px;
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

    .table td, .table th {
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

    table.dataTable>thead .sorting:before, table.dataTable>thead .sorting_asc:before, table.dataTable>thead .sorting_desc:before, table.dataTable>thead .sorting_asc_disabled:before, table.dataTable>thead .sorting_desc_disabled:before {
        content: "";
    }
    table.dataTable>thead .sorting:after, table.dataTable>thead .sorting_asc:after, table.dataTable>thead .sorting_desc:after, table.dataTable>thead .sorting_asc_disabled:after, table.dataTable>thead .sorting_desc_disabled:after {
        content: "";
    }

    .delete-form {
        display: inline;
    }
    .btn-cong-ch {
        margin-left: 6px;
    }
    input[name="dch"] {
        width: 120px;
        display: inline !important;
    }
</style>
<script>
    function updateReward(accountId) {
        $('form#form-' + accountId).submit();
    }

    function updateNsd(accountId) {
        //$('form#form-nsd-' + accountId).submit();
        axios.put(`/accounts/${accountId}/is_nsd`).then(() => { toastr.success('Done!'); });
    }

    function updateNvd(accountId) {
        //$('form#form-nsd-' + accountId).submit();
        axios.put(`/accounts/${accountId}/is_nvd`).then(() => { toastr.success('Done!'); });
    }

    function updateNopDo(accountId) {
        //$('form#form-nsd-' + accountId).submit();
        axios.put(`/accounts/${accountId}/nop_do`).then(() => { toastr.success('Done!'); });
    }

    function updateNopBac(accountId) {
        //$('form#form-nsd-' + accountId).submit();
        axios.put(`/accounts/${accountId}/is_nopbac`).then(() => { toastr.success('Done!'); });
    }

    function updateDt(accountId) {
        $('form#form-dt-' + accountId).submit();
    }

        
    function restartDong() {
        axios.get('/restart-dong').then(() => { toastr.success('Done!') });
    }
</script>
