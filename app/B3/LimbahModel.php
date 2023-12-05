<?php

namespace App\B3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LimbahModel extends Model
{
    use HasFactory;
    protected $table = 'limbahData';
    protected $fillable = ['tanggal','jenis','asal','jumlah'];
}
