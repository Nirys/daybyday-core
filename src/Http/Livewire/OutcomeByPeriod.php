<?php
namespace SpiritSystems\DayByDay\Core\Http\Livewire;

use App\Models\Lead;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use SpiritSystems\DayByDay\Core\Charts\OutcomeByPeriodChart;
use SpiritSystems\DayByDay\Core\Support\Livewire\ChartComponent;
use SpiritSystems\DayByDay\Core\Support\Livewire\ChartComponentData;

/**
 * Class WanSpeedTests
 *
 * @package App\Http\Livewire
 */
class OutcomeByPeriod extends ChartComponent
{
    public $start, $end;
    protected $lastData;

    /**
     * @return string
     */
    protected function view(): string
    {
        return 'livewire.outcome-by-period';
    }

    /**
     * @return string
     */
    protected function chartClass(): string
    {
        return OutcomeByPeriodChart::class;
    }

    /**
     * @return 
     */
    protected function chartData(): ChartComponentData
    {
        try{
            $this->verifyParams();

            $months = [];
            $labels = new Collection();
            $interDate = $this->start->clone();
            while($interDate < $this->end){
                $months[] = "SELECT " . $interDate->format('Y-m') . ' as title, ' . $interDate->format('Y') . ' as y, ' . $interDate->format('m') . ' as m';
                $labels->add($interDate->format('Y-m'));
                $interDate->addMonths(1);
            }

            $sql = "SELECT ifnull(sum(leads.lead_amount), 0) AS total FROM (" . implode(" UNION ", $months) . ') as Months ';
            $sql .= ' LEFT JOIN leads on Months.y = year(leads.deadline) and Months.m = month(leads.deadline)';

            $statuses = Status::where('source_type', Lead::class)->get();
            $data = new Collection();
            foreach($statuses as $status){
                $results = DB::select(DB::raw($sql . ' and status_id=' . $status->id . ' group by Months.m, Months.y, Months.title'));
                $results = collect($results)->pluck('total')->toArray();
                $data->add( [
                    'startDate' => $this->start->format('Y-m-d'),
                    'endDate' => $this->end->format('Y-m-d'),
                    'title' => $status->title,
                    'color' => $status->color,
                    'data' => $results
                ] );
            }
            
            $this->lastData =  (new ChartComponentData($labels, $data));
        }catch(\Exception $e){

        }
        return $this->lastData;
    }

    protected function verifyParams(){
        if(!$this->start){
            $this->start = new Carbon();
        }elseif(!$this->start instanceof Carbon){
            $this->start = new Carbon($this->start);
        }

        if(!$this->end){
            $this->end = $this->start->clone()->addMonths(6);
        }elseif(!$this->end instanceof Carbon){
            $this->end = new Carbon($this->end);
        }
    }    
}
