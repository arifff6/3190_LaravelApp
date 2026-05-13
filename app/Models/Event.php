<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'category_id', 'title', 'description', 'date', 
        'location', 'price', 'stock', 'poster_path'
    ];

    // TAMBAHKAN INI (Sesuai Modul 5.4.3 Halaman 33)
    protected $casts = [
        'date' => 'datetime',
    ];

    // Relasi ke Category
    public function category() {
        return $this->belongsTo(Category::class);
    }
}