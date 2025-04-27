<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';
    protected $fillable = 
    [
        'user_id',
        'tanggal',
        'jam_masuk', 
        'jam_pulang', 
        'foto_masuk', 
        'foto_pulang', 
        'latitude',
        'longitude',
        'status',
        'status_kehadiran',
        'menit_terlambat'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
