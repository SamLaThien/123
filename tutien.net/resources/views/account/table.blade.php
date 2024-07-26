<table class="table table-bordered table-sm tableFixHead" id="acc-table">
    <thead class="thead-dark">
        <tr>
            <!-- <th scope="col"></th> -->
            <th scope="col">#</th>
            <th scope="col">ID</th>
            <th scope="col">Ngoại hiệu</th>
            <th scope="col">Tiến độ</th>
            <th scope="col">Tài sản</th>
            <th scope="col">Cắn Đan</th>
            <th scope="col">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($accounts as $key => $account)
        <tr>
            <!-- <td style="width:32px;text-align:center;">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="accountIds" value="{{ $account->id }}">
                </div>
            </td> -->
            <th scope="row">{{ $key + 1 }}</th>
            <td>
                <a href="/accounts/{{ $account['id'] }}">
                    {{ $account['account_id'] }}
                </a>
            </td>
            <td>
                <button class="btn btn-outline-success btn-sm" type="button" onclick="bangPhai({{ $account->id}}, '{{$account->account_name}}', '{{ $account->bang_phai }}')">Bang Phái</button>
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
                <button class="btn" id="copyBtn" onclick="copyToClipboard('{{ $cookie }}')">
                    <img height="16px" src="https://clipboardjs.com/assets/images/clippy.svg" alt="Copy to cookie">
                </button>
            </td>
            <td>
                <span id="account_progress_{{ $account->account_id }}" @if (str_contains($account['progress'], '✓' )) style="color: red" @endif>
                    {{ $account['progress'] }}
                </span>
            </td>
            <td>
                <span id="account_ts_{{ $account->account_id }}">{{ $account['tai_san'] }}</span> bạc
            </td>
            <td>
                <button class="btn btn-sm btn-outline-success" type="button" onclick="canDan({{ $account->id}}, '{{$account->account_name}}', '{{$account->progress}}', '{{ $account->bang_phai }}')">
                    Cắn Đan
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="changeMember({{$account->id}})">Vào Động</button>
            </td>
            <td>
                <a href="{{ '/accounts/' . $account['id'] . '/update' }}" class="btn btn-sm btn-outline-success" target="_blank">Cập Nhật</a>
                <button class="btn btn-outline-success btn-sm" type="button" onclick="congCongHien({{$account->id}})">Cộng CH</button>

                <a class="btn btn-sm btn-outline-dark" href="{{ '/accounts/' . $account['id'] . '/nop_do_all' }}" target="_blank">Nộp All</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="modal fade" id="canDanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form>
                    <div class="text-center">
                        <p class="" id="accountInfo"></p>
                    </div>
                    <div class="row">
                        <div class="col col-md-4">
                            Chọn Đan Dược
                        </div>
                        <div class="col col-md-8">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="ttd" value="ttd">
                                <label class="form-check-label" for="ttd">
                                    Tẩy Tủy Đan (Cấp 1)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="tcd" value="tcd">
                                <label class="form-check-label" for="tcd">
                                    Trúc Cơ Đan (Cấp 1)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="bnd" value="bnd">
                                <label class="form-check-label" for="bnd">
                                    Bổ Nguyên Đan (Cấp 2)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="bad" value="bad">
                                <label class="form-check-label" for="bad">
                                    Bổ Anh Đan (Cấp 3)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="hnd" value="hnd">
                                <label class="form-check-label" for="hnd">
                                    Hóa Nguyên Đan (Cấp 4)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="ltd" value="ltd">
                                <label class="form-check-label" for="ltd">
                                    Luyện Thần Đan (Cấp 5)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="hnd2" value="hnd2">
                                <label class="form-check-label" for="hnd2">
                                    Hợp Nguyên Đan (Cấp 6)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="dld" value="dld">
                                <label class="form-check-label" for="dld">
                                    Đại Linh Đan (Cấp 7)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="dhd" value="dhd">
                                <label class="form-check-label" for="dhd">
                                    Độ Hư Đan (Cấp 8)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="ltcp" value="ltcp" checked>
                                <label class="form-check-label" for="ltcp">
                                    Linh Thạch CP (Cấp 4)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="tlhp" value="tlhp">
                                <label class="form-check-label" for="tlhp">
                                    Tinh Linh HP (Cấp 5)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="tltp" value="tltp">
                                <label class="form-check-label" for="tltp">
                                    Tinh Linh TP (Cấp 6)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="tlthp" value="tlthp">
                                <label class="form-check-label" for="tlthp">
                                    Tinh Linh THP (Cấp 7)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="tlcp" value="tlcp">
                                <label class="form-check-label" for="tlcp">
                                    Tinh Linh CP (Cấp 8)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="bdq" value="bdq">
                                <label class="form-check-label" for="bdq">
                                    Bàn Đào Quả (Cấp 9)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="bđq" value="bđq">
                                <label class="form-check-label" for="bđq">
                                    Bồ Đề Quả (Cấp 10)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="ndq" value="ndq">
                                <label class="form-check-label" for="ndq">
                                    Ngô Đồng Quả (Cấp 11)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="tuhp" value="tuhp">
                                <label class="form-check-label" for="tuhp">
                                    Tử Tinh HP (Cấp 9)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="danDuoc" id="tutp" value="tutp">
                                <label class="form-check-label" for="tutp">
                                    Tử Tinh TP (Cấp 10)
                                </label>
                            </div>
                            <br>
                            <div class="form-check form-check-inline">
                                <form method="post" action="url">
                                    <select class="form-check-label" id="selDanDuoc" name="selDanDuoc">
                                        <option value="0">-- Đan Dược -- </option>
                                        <option value="bad"> Bổ Anh Đan Cấp 3</option>
                                        <option value="hnd"> Hóa Nguyên Đan Cấp 4</option>
                                        <option value="ltd"> Luyện Thần Đan Cấp 5</option>
                                        <option value="hnd2"> Hợp Nguyên Đan Cấp 6</option>
                                        <option value="dld"> Đại Linh Đan Cấp 7</option>
                                        <option value="dhd"> Độ Hư Đan Cấp 8</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col col-md-4">
                            Chọn buff
                        </div>
                        <div class="col col-md-8">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="buff" id="hkl" value="8">
                                <label class="form-check-label" for="hkd">
                                    Hoàng Kim Lệnh
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="buff" id="hkd" value="10">
                                <label class="form-check-label" for="hkd">
                                    Huyết Khí Đan
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="buff" id="dgt" value="11" checked>
                                <label class="form-check-label" for="dgt">
                                    Đê Giai Thuẫn
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="buff" id="tlc" value="17" checked>
                                <label class="form-check-label" for="tlc">
                                    Tị Lôi Châu
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="buff" id="ttd" value="34" checked>
                                <label class="form-check-label" for="ttd">
                                    Thanh Tâm Đan
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="buff" id="hlt" value="70">
                                <label class="form-check-label" for="hlt">
                                    Hộ Linh Trận
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="buff" id="hnc" value="10152">
                                <label class="form-check-label" for="hnc">
                                    Hỏa Ngọc Châu
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="buff" id="tnc" value="10153">
                                <label class="form-check-label" for="tnc">
                                    Thải Ngọc Châu
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="buff" id="snc" value="10154">
                                <label class="form-check-label" for="snc">
                                    Sa Ngọc Châu
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="buff" id="tlt" value="10155">
                                <label class="form-check-label" for="tlt">
                                    Tán Lôi Trận
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col col-md-4">
                            Động Thiên
                        </div>
                        <div class="col col-md-8">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="is_dt" id="is_dt" checked>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col col-md-4">
                            Vật Phẩm Đặc Biệt
                        </div>
                        <div class="col col-md-8">
                            <form id="number">
                                <input name="number_{{ $account->id }}" type="text" placeholder="Số Lượng"><br>
                            </form>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="item" id="nsvn" value="78">
                                <label class="form-check-label" for="nsvn">
                                    Nhân Sâm Vạn Năm
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="item" id="ntls" value="30503">
                                <label class="form-check-label" for="ntls">
                                    Ngọc Tuyết Linh Sâm
                                </label>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="CanSam({{ $account->id }})">Dùng Sâm</button>
                        </div>
                    </div>

                    <hr>
                    <div class="float-right">
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="startCanDan()">Cắn Đan</button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="startCanDanSll()">Cắn Tới VM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bangPhaiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form>
                    <div class="text-center">
                        <p id="accountBangInfo" class=""></p>
                    </div>
                    <div class="row">
                        <div class="col col-md-4">
                            Chọn Bang Phái
                        </div>
                        <div class="col col-md-8">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="bangPhai" type="radio" id="vtt" value="27" checked>
                                <label class="vo-ta-team" for="vtt">
                                    Vô Tà Team
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="bangPhai" type="radio" id="vhd" value="40">
                                <label class="vinh-hang-dien" for="vhd">
                                    Vĩnh Hằng Điện
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="bangPhai" type="radio" id="dtm" value="34">
                                <label class="de-thien-mon" for="dtm">
                                    Đế Thiên Môn
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="float-right">
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="Move()">Đổi Bang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let isCanSll = false;

    function canDan(accountId, accountName, progress, bangPhai) {
        selectAccountId = accountId;
        $('#canDanModal').modal('toggle');
        var ele = document.getElementById('accountInfo');
        $("p").removeClass();
        ele.classList.toggle(bangPhai);
        $('#accountInfo').text(`${accountName} - ${progress}`);
    }

    function startCanDan() {
        var selDanDuoc = $("#selDanDuoc").val();
        const danDuoc = [];
        const danDuocChecked = $('input[name="danDuoc"]:checked').val();
        if (selDanDuoc != 0) {
            danDuoc.push(selDanDuoc);
        }
        if (danDuocChecked) {
            danDuoc.push(danDuocChecked);
        }
        const buffs = $('input[name="buff"]:checked');
        const is_dt = $('input[name="is_dt"]').is(":checked") ? 1 : 0;
        const vatphamphutro = [];
        for (let i = 0; i < buffs.length; i++) {
            vatphamphutro.push(buffs[i].value);
        }
        const url = `/accounts/${window.selectAccountId}/can_dan`;
        axios.post(url, {
            danDuoc,
            is_dt,
            vatphamphutro,
            accountId: window.selectAccountId
        });
        toastr.info(`Đang cắn đan, chờ tẹo!`);
    }

    function CanSam(account) {
        toastr.info(`Đang cắn sâm, chờ tẹo!`);
        const itemId = $('input[name="item"]:checked').val();
        let number = $(`input[name="number_${account}"]`).val();
        if (!number) {
            number = 1;
        }
        const url = `/accounts/can_sam`;
        axios.post(url, {
                itemId,
                number,
                accountId: window.selectAccountId
            })
            .catch(error => {
                toastr.error('Có lỗi xảy ra, vui lòng thực hiện lại!');
            })
    }


    function startCanDanSll() {
        var selDanDuoc = $("#selDanDuoc").val();
        const danDuocName = [];
        const danDuocChecked = $('input[name="danDuoc"]:checked').val();
        if (selDanDuoc != 0) {
            danDuocName.push(selDanDuoc);
        }
        if (danDuocChecked) {
            danDuocName.push(danDuocChecked);
        }
        const url = `/accounts/can_dan_sll`;
        const is_dt = $('input[name="is_dt"]').is(":checked") ? 1 : 0;
        const buffs = $('input[name="buff"]:checked');
        const vatphamphutro = [];
        for (let i = 0; i < buffs.length; i++) {
            vatphamphutro.push(buffs[i].value);
        }
        axios.post(url, {
            danDuocName,
            is_dt,
            vatphamphutro,
            accountId: window.selectAccountId
        });
        toastr.info("Quá trình cắn đan đang bắt đầu. Có thể delay 1 chút để chuẩn bị!");
    }
    let selectedAccountId = 0;

    function bangPhai(accountId, accountName, progress) {
        selectedAccountId = accountId;
        $('#bangPhaiModal').modal('toggle');
        var ele = document.getElementById('accountBangInfo');
        $("p").removeClass();
        ele.classList.toggle(progress);
        $('#accountBangInfo').text(`${accountName}`);
    }

    function Move() {
        const BangPhai = $('input[name="bangPhai"]:checked').val();
        const url = `/accounts/${selectedAccountId}/bang_phai`;
        axios.post(url, {
                BangPhai
            })
            .then((res) => {
                toastr.success(res.data);
            })
            .catch(error => {
                toastr.error('Có lỗi xảy ra, vui lòng thực hiện lại!');
            })
    }

    function congCongHien(selectedAccountId) {
        axios.post(`/accounts/${selectedAccountId}/cong_ch`, {
                dch: 50000
            })
            .then((res) => {
                toastr.success(res.data);
            })
            .catch(error => {
                toastr.error('Có lỗi xảy ra, vui lòng thực hiện lại!');
            })
    }

    function changeMember(selectedAccountId) {
        axios.post(`/accounts/${selectedAccountId}/vao_dong`, {
                dt: 1
            })
            .then((res) => {
                toastr.success(res.data);
            })
            .catch(error => {
                toastr.error('Có lỗi xảy ra, vui lòng thực hiện lại!');
            })
    }

    function copyToClipboard(text) {
        var input = document.createElement('textarea');
        input.innerHTML = text;
        document.body.appendChild(input);
        input.select();
        var result = document.execCommand('copy');
        document.body.removeChild(input);
        alert(text);
        return result;
    }
</script>