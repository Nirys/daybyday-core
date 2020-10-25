<?php
namespace SpiritSystems\DayByDay\Core\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessLine extends Model {
    protected $fillable = [
        'name',
        'external_id'
    ];
}