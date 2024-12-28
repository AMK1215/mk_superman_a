<?php

namespace App\Models\Admin;

use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['account_name', 'account_number', 'payment_type_id', 'agent_id'];

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function scopeAgent($query)
    {
        return $query->where('agent_id', auth()->user()->id);
    }

    public function scopeAgentPlayer($query)
    {
        return $query->where('agent_id', auth()->user()->agent_id);
    }

    public function scopeMaster($query)
    {
        $agents = User::find(auth()->user()->id)->agents()->pluck('id')->toArray();

        return $query->whereIn('agent_id', $agents);
    }
}
