<?php

use App\Models\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;
use SpiritSystems\DayByDay\Contacts\Models\BusinessLine;
use SpiritSystems\DayByDay\Contacts\Models\Currency;

class AddOpportunityItems extends Migration {

    public function up(){        
        Status::where('source_type','App\Models\Lead')->delete();

        $statuses = [
            ['title' => '0. Prospecting','color' => '#2FA599'],
            ['title' => '1. Qualification','color' => '#2FA599'],
            ['title' => '2. Needs Analysis','color' => '#2FA599'],
            ['title' => '3. Value Proposition','color' => '#2FA599'],
            ['title' => '4. Id. Decision Makers &amp; Perception Analysis','color' => '#2FA599'],
            ['title' => '5. Solution Proposal','color' => '#2FA599'],
            ['title' => '6. Price Quotation','color' => '#2FA599'],
            ['title' => '7. Negotiation/Review','color' => '#2FA599'],
            ['title' => '8. Closed Won','color' => '#2FA599'],
            ['title' => '9. Closed Lost','color' => '#2FA599'],
            ['title' => '10. Closed No Bid','color' => '#2FA599'],
            ['title' => '11. Closed Rejected','color' => '#2FA599'],            
        ];

        foreach($statuses as $status){
            $model = new Status();
            $model->title = $status['title'];
            $model->source_type = 'App\Models\Lead';
            $model->color = $status['color'];
            
            $model->external_id = Uuid::uuid4()->toString();
            $model->save();
        }

        Schema::create('currencies', function(Blueprint $table){
            $table->id();
            $table->string('external_id');
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->timestamps();

            $table->unique(['external_id']);
        });

        $currencies = [
            ['name' => 'US Dollars', 'symbol' => '$'],
            ['name' => 'AU Dollars', 'symbol' => 'A$'],
            ['name' => 'Euro', 'symbol' => '€'],
            ['name' => 'Japan Yen', 'symbol' => '¥'],
        ];

        foreach($currencies as &$currency){
            $currency['external_id'] = Uuid::uuid4()->toString();
            $model = Currency::create($currency);
            $currency['id'] = $model->id;
        }

        Schema::create('business_lines', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('external_id');
            $table->timestamps();
            $table->unique(['external_id']);
        });

        $lines = [ 'VLAB','ICD','ESW','ASTC','IQONIC' ];
        foreach($lines as &$line){
            $model = BusinessLine::create([
                'external_id' => Uuid::uuid4()->toString(),
                'name' => $line
            ]);
        }

        Schema::table('leads', function(Blueprint $table){
            $table->foreignId('currency_id')->nullable();
            $table->date('expected_close')->nullable();
            $table->float('lead_amount')->nullable();
            $table->boolean('is_new')->nullable();
            $table->foreignId('business_line_id')->nullable();
            $table->integer('probability')->nullable()->default(0);
            $table->float('amount_invoiced')->nullable()->default(0);
            $table->date('final_invoice_date')->nullable();
            $table->text('next_steps')->nullable();
        });
        
    }

    public function down(){
        Schema::table('leads', function(Blueprint $table){
            $table->dropColumn('currency_id');
            $table->dropColumn('expected_close');
            $table->dropColumn('lead_amount');
            $table->dropColumn('is_new');
            $table->dropColumn('business_line_id');
            $table->dropColumn('probability');
            $table->dropColumn('amount_invoiced');
            $table->dropColumn('final_invoice_date');
            $table->dropColumn('next_steps');
        });

        Schema::dropIfExists('currencies');
        Schema::dropIfExists('business_lines');
    }
}