<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'date',
        'total_orders',
        'total_sales',
        'average_order_value',
        'report_type',
        'filters',
        'daily_sales',
        'monthly_sales',
        'yearly_sales',
    ];

    protected $casts = [
        'date' => 'date',
        'total_orders' => 'integer',
        'total_sales' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'filters' => 'array',
        'daily_sales' => 'decimal:2',
        'monthly_sales' => 'decimal:2',
        'yearly_sales' => 'decimal:2',
    ];

    public function getKeyName()
    {
        return 'date';
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
