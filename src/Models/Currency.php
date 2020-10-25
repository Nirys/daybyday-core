<?php
namespace SpiritSystems\DayByDay\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model {

    protected $fillable = [
        'name',
        'symbol',
        'external_id'
    ];
    
}