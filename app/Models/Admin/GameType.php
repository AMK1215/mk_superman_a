<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'order', 'img'];

    protected $appends = ['img_url'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'game_type_product')->withPivot('image');
    }

    public function getImgUrlAttribute()
    {
        return asset('assets/img/game_type/'.$this->img);
    }
}
