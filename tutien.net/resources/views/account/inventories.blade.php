<!-- Modal -->
<div class="modal fade" id="modal-{{ $type }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Danh sách account
                    @if ($type != 'tang' && $type != 'tang-cao' && $type != 'tbbr')
                    <span class="text-sm" style="margin-left: 54px;">
                        <a target="_blank" href={{ "/nop-do?item=" . $type }} class="btn btn-outline-success btn-sm">Nộp Hết</a>
                    </span>
                    @endif
                </h5>
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <hr>
                    <table class="table table-bordered">
                        <tbody>
                        @foreach($inventories as $inventory)
                            @if ($inventory->account)
                            <tr>
                                @php
                                    $account = $inventory->account;
                                @endphp
                                    <td class="{{ $account ? $account->bang_phai : '' }}">
                                    {{ $inventory->account ? $inventory->account->account_name . ' (' . $inventory->account->account_id . ')' : '' }}
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
                                </td>
                                <td>{{ $inventory->amount }} Cái</td>
                                @if ($type == 'tbbr')
                                <td>
                                    <a class="btn btn-sm btn-outline-success" href="{{ '/accounts/' . $account->id . '/mo_ruong' }}" target="_blank">Mở Rương</a>
                                </td>
                                @elseif ($type == 'banh-tet')
                                <td>
                                    <a class="btn btn-sm btn-outline-success" href="{{ '/accounts/' . $account->id . '/su_dung_event_item' }}" target="_blank">Sử dụng</a>
                                </td>
                                @endif
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
