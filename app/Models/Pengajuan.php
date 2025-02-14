<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';

    protected $fillable = [ 
        'user_id',
        'tanggal_izin',
        'status',
        'deskripsi',
        'gambar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
   
}
