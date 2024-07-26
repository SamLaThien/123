@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @foreach($inventories as $key => $data)
                <div class="col-sm-6">
                <span class="inventory-item">{{ $key }} ( {{ $data->sum('amount') }} )</span>
                <table class="table table-bordered">
                    <tbody>
                    @foreach($data as $inventory)
                        <tr>
                            @php
                                $account = $inventory->account;
                                $cookie = $account['cookie'];
                                $cookie = substr($cookie, strpos($cookie, 'USER=') + 5);
                                if (str_contains($cookie, ';')) {
                                    $cookie = substr($cookie, 0, strpos($cookie, ';'));
                                }
                                $cookie = 'document.cookie="USER=";document.cookie="PHPSESSID=";document.cookie="USER=' . $cookie . '"';
                            @endphp
                            <td class="{{ $account->bang_phai }}">
                                {{ $inventory->account ? $inventory->account->account_name . ' (' . $inventory->account->account_id . ')' : '' }}
                                <input class="form-control" type="text" value="{{ $cookie }}" >
                            </td>
                            <td>
                                <button id="btn-chuyen-kt-{{ $account->account_id }}" class="btn btn-sm btn-outline-success" onclick="chuyenKt({{ $account->account_id }}, {{$inventory->amount}}, '{{$account->bang_phai}}')">
                                    Chuyển {{ $inventory->amount }} kt
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            @endforeach
        </div>
    </div>
@endsection
<script>
    function chuyenKt(accountId, amount, bangPhai) {
        axios.post('/chuyen-do', {
            itemId: 57,
            accountId,
            amount,
            bangPhai
        }).then(() => {
            toastr.success('Đã chuyển ' + amount + ' cho id: ' + accountId);
            //$('#btn-chuyen-kt-' + accountId).style();
        }).catch(() => {
            toastr.error('Có lỗi xảy ra!!!');
        });
    }

    function inviteTang(elementId) {
        const accountId = $('#' + elementId).val();
        console.log(accountId);
    }
</script>
<style>
    label {
        font-size: 16px;
    }
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
</style>