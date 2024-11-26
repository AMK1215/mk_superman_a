<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerAds extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'agent_id',
    ];

    protected $appends = ['img_url'];

    protected $table = 'banner_ads';

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id'); // The admin that owns the banner
    }

    public function getImgUrlAttribute()
    {
        return asset('assets/img/banners_ads/'.$this->image);
    }

    public function scopeAgent($query)
    {
        return $query->where('agent_id', auth()->user()->id);
    }

    public function scopeMaster($query)
    {
        $agents = User::find(auth()->user()->id)->agents()->pluck('id')->toArray();
        return $query->whereIn('agent_id', $agents);
    }
}
