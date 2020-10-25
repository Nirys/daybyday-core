<?php

use App\Models\Integration;
use App\Models\Status;
use App\Services\Storage\Local;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;
use SpiritSystems\DayByDay\Contacts\Models\BusinessLine;
use SpiritSystems\DayByDay\Contacts\Models\Currency;

class AddLocalStorage extends Migration {

    public function up(){
        Integration::create(['name' => Local::class ,'api_type' => 'file']);
    }

    public function down(){
        Integration::where('name', Local::class)->where('api_type','file')->delete();
    }

}
