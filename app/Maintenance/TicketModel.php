<?php

namespace App\Maintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketModel extends Model
{
    use HasFactory;
    protected $table = 'maintenanceticket';
    protected $fillable = ['nomor','tanggal'];
}
