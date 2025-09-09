<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'external_id',
        'status',
        'total',
        'fiscal_receipt_number',
        'shift_number',
        'receipt_datetime',
        'fn_number',
        'ecr_registration_number',
        'fiscal_document_number',
        'fiscal_document_attribute',
        'fns_site',
        'ofd_inn',
        'ofd_receipt_url',
    ];

    protected $casts = [
        'receipt_datetime' => 'datetime',
        'total' => 'decimal:2',
    ];
}

