<table class="table table-bordered table-sm tableFixHead" id="acc-table">
    <thead class="thead-dark">
    <tr>
        <th scope="col">#</th>
        <th scope="col">ID</th>
        <th scope="col">Ngoại hiệu</th>
        <th scope="col">Tiến độ</th>
        <th scope="col">Tài sản</th>
        <th scope="col">NSĐ</th>
        <th scope="col">NVD</th>
        <th scope="col">Nộp Bạc</th>
        <th scope="col">Nộp Đồ</th>
        <th scope="col">Thao tác</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($accounts as $key => $account)
        <tr>
            <th scope="row">{{ $key + 1 }}</th>
            <td>
                <a href="/accounts/{{ $account['id'] }}">
                    {{ $account['account_id'] }}
                </a>
            </td>
            <td>
                <a href={{"https://tutien.net/member/" . $account['account_id']}} class="{{ $account->bang_phai }}" target="_blank">
                    {{ $account['account_name'] }}
                </a>
                @php
                    $cookie = $account['cookie'];
                    $cookie = substr($cookie, strpos($cookie, 'USER=') + 5);
                    if (str_contains($cookie, ';')) {
                        $cookie = substr($cookie, 0, strpos($cookie, ';'));
                    }
                @endphp
                <button class="btn" onclick="alert('{{ $cookie }}')">
                    <img height="16px" src="https://clipboardjs.com/assets/images/clippy.svg" alt="Copy to cookie">
                </button>
                @if($account->is_dt)
                    <span class="text-success font-weight-bold">&#10003;</span>
                @else
                    <span class="text-danger font-weight-bold">&#10003;</span>
                @endif
            </td>
            <td>
                {{ $account['progress'] }}
            </td>
            <td>{{ $account['tai_san'] }} bạc</td>
            <td>
                <div class="reward">
                    <input
                        name="is_nsd"
                        onclick="updateNsd({{ $account['id'] }})"
                        type="checkbox" {{ $account['is_nsd'] ? 'checked' : ''}}
                    >
                </div>
            </td>
            <td>
                <div class="reward">
                    <input
                        name="is_nvd"
                        onclick="updateNvd({{ $account['id'] }})"
                        type="checkbox" {{ $account['is_nvd'] ? 'checked' : ''}}
                    >
                </div>
            </td>
            <td>
                <div class="reward">
                    <input
                        name="is_nopbac"
                        onclick="updateNopBac({{ $account['id'] }})"
                        type="checkbox" {{ $account['is_nopbac'] ? 'checked' : ''}}
                    >
                </div>
            </td>
            <td>
                <div class="reward">
                    <input
                        name="is_nopdo"
                        onclick="updateNopDo({{ $account['id'] }})"
                        type="checkbox" {{ $account['is_nopdo'] ? 'checked' : ''}}
                    >
                </div>
            </td>
            <td>
                <div class="actions">
                    <a href="{{ '/accounts/' . $account['id'] . '/update' }}" class="btn btn-sm btn-outline-success" target="_blank">Cập Nhật</a>
                    <a class="btn btn-sm btn-outline-primary" href="{{ '/accounts/' . $account['id'] . '/nop_bac' }}" target="_blank">Nộp Bạc</a>
                    <a class="btn btn-sm btn-outline-info" href="{{ '/accounts/' . $account['id'] . '/nop_do' }}" target="_blank">Nộp đồ</a>
                    <br>
                    <a class="btn btn-sm btn-outline-dark" href="{{ '/accounts/' . $account['id'] . '/nop_do_all' }}" target="_blank">Nộp All</a>
                    <button class="btn btn-outline-success btn-sm" type="button" onclick="congCongHien({{$account->id}})">Cộng CH</button>
                    @if (strpos($account->progress, '100%') !== false)
                        <a class="btn btn-sm btn-success" href="{{'/accounts/' . $account['id'] . '/dot_pha'}}" target="_blank">Đột Phá</a>
                    @endif
                    {!! Form::open([
                        'url' => '/accounts/' . $account['id'],
                        'method' => 'delete',
                        'class' => 'delete-form'
                    ]) !!}
                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                    {!! Form::close() !!}
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    let selectedAccountId = 0;
    function openAcc(accountId, accountName, progress) {
        selectedAccountId = accountId;
        $('#accountInfo').text(`${accountName} - ${progress}`);
        $('#accountModal').modal('toggle');
    }

    function congCongHien(selectedAccountId) {
        axios.post(`/accounts/${selectedAccountId}/cong_ch`, {dch: 50000})
            .then((res) => {
                toastr.success(res.data);
            })
            .catch(error => {
                toastr.error('Có lỗi xảy ra, vui lòng thực hiện lại!');
            })
    }
</script>
<style>

</style>