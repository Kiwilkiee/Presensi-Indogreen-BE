<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengingatPesan extends Model
{
    use HasFactory;

    protected $table = 'pengingat_pesans';

    protected $fillable = ['pesan'];
}
