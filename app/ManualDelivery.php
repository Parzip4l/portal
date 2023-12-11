<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualDelivery extends Model
{
    use HasFactory;
    protected $table = 'manual_deliveries';
    protected $fillable = ['tanggal_order','tanggal_kirim','target_kirim','customer','driver','status','nomor_so','ekspedisi','keterangan','items'];
}
