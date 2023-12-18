<?php

namespace App\Oli;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanOli extends Model
{
    use HasFactory;
    protected $table = 'delivery_oli';
    protected $fillable = ['tanggal','pengirim','jenis_oli','jumlah','receive_status'];
}
