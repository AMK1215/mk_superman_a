<?php

namespace App\Models;

use App\Models\Admin\Bank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithDrawRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_id', 'agent_id', 'user_id', 'account_name', 'account_no', 'amount', 'status', 'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
