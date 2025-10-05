<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    /** @use HasFactory<\Database\Factories\FacturaFactory> */
    use HasFactory;
    protected $fillable = [
        'documento_id',
        'numero_factura',
        'fecha_emision',
        'monto_total',
        'impuesto',
        'estado',
        'monto_neto',
    ];
    
}
