@extends('layouts.app')

@section('content')
    <div class="container" id="vqmm">
        <h3>Vòng Quay May Mắn</h3>
        <hr>

        <div class="row limit-height">
            <div class="col-md-6 border-right">
                <div class="title font-weight-bold">Danh Sách Account</div>
                <div class="form-check">
                    <input type="checkbox" onclick="selectAll()" id="selectAll">
                    <label class="form-check-label" for="selectAll">
                        Chọn Tất
                    </label>
                </div>
                <hr>
                @foreach($accounts as $account)
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value="{{ $account->id }}"
                            id="defaultCheck-{{ $account->id }}"
                            data-id="{{ $account->id }}"
                            data-name="{{ $account->account_name }}"
                            data-account-id="{{ $account->account_id }}"
                        >
                        <label class="form-check-label" for="defaultCheck-{{ $account->id }}">
                            {{ $account->account_name }} ({{ $account->tai_san }} bạc)
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="col-md-6" >
                <div class="title font-weight-bold">Danh Sách Quay</div>
                <ul id="list-acc"></ul>
                <button class="btn btn-sm btn-outline-success" id="btn-action" onclick="startVqmm()">Start</button>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    
<script type="text/javascript">
    // try {
    //     window.$ = window.jQuery = $;
    // } catch (e) {
    // }
    let accs = [];
    const intervalList = [];
    let isStart = false;
    let isSelectAll = false;

    $(document).on("click", "input.form-check-input", function (e) {
        if (isStart) {
            alert("Stop rồi hẵng thay đổi account /chui");
            window.location.reload(true);
        }
        const accId = $(this).data("id");
        if ($(this).is(':checked')) {
            accs.push({
                id: accId,
                account_name: $(this).data("name"),
                account_id: $(this).data("account-id")
            })
        } else {
            const index = accs.findIndex(acc => acc.id == accId);
            if (index != -1) {
                accs.splice(index, 1);
            }
        }
        updateListQuay();
    });

    function selectAll() {
        $("input.form-check-input").click();
    }

    function updateListQuay() {
        const rootItem = $("#list-acc");
        rootItem.empty();

        const menu = document.querySelector('#list-acc');
        for (let i = 0; i < accs.length; i ++) {
            const acc = accs[i];
            menu.appendChild(createMenuItem(acc.account_name + " (" + acc.account_id + ")"));
        }
    }

    function createMenuItem(name) {
        let li = document.createElement('li');
        li.textContent = name;
        return li;
    }

    function startVqmm() {
        if (isStart) {
            $("#btn-action").text("Start");
            isStart = false;
            axios.post('vqmm', {
                accs: []
            }).then(() => {
                toastr.success('Đã dừng!');
            });
            return;
        }

        isStart = true;
        $("#btn-action").text("Stop");
        axios.post('vqmm', {
            accs
        }).then(() => {
            toastr.success('Đang bắt đầu quay!');
        });
    }
</script>
@endpush

<style>
    .limit-height > div {
        height: calc(100vh - 200px);
        overflow: auto;
    }
</style>
