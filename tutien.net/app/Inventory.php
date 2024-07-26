<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'account_id',
        'item_id',
        'amount'
    ];

    public function setItemIdAttribute($value)
    {
        $value = strtoupper($value);
        switch ($value) {
            case 'TBD':
                $this->attributes['item_id'] = 1;
                break;
            case 'TBDC':
                $this->attributes['item_id'] = 2;
                break;
            case 'LB':
                $this->attributes['item_id'] = 3;
                break;
            case 'QG':
                $this->attributes['item_id'] = 4;
                break;
            case 'LTCP':
                $this->attributes['item_id'] = 5;
                break;
            case 'LTTHP':
                $this->attributes['item_id'] = 6;
                break;
            case 'CTD':
                $this->attributes['item_id'] = 7;
                break;
            default:
                $this->attributes['item_id'] = $value;
        }
    }

    public function getItemIdAttribute($value)
    {
        switch ($value) {
            case 1:
                return 'Tàng';
            case 2:
                return 'Tàng Cao';
            case 3:
                return 'La Bàn';
            case 4:
                return 'Quy Giáp';
            case 5:
                return 'DMC';
            case 6:
                return 'BHN';
            case 7:
                return 'TSV';
            case 8:
                return 'TTA';
            case 9:
                return 'TPB';
            case 10:
                return 'TGCT';
            case 11:
                return 'TBBR';
            case 12:
                return 'Gân Gà';
            case 13:
                return 'TTD';
            case 14:
                return 'TCD';
            case 15:
                return 'BND';
            case 16:
                return 'BAD';
            case 17:
                return 'HND';
            case 18:
                return 'HỢP';
            case 19:
                return 'ĐÀO';
            case 20:
                return 'ĐỀ';
            case 21:
                return 'CÁ';
        }
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
