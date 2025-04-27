<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengingatAbsen extends Model
{
    protected $fillable = ['subject', 'body', 'waktu_kirim'];
}