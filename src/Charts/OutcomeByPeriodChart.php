<?php

namespace SpiritSystems\DayByDay\Core\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use SpiritSystems\DayByDay\Core\Support\Livewire\ChartComponentData;

/**
 * Class WanSpeedTestsChart
 *
 * @package App\Charts
 */
class OutcomeByPeriodChart extends Chart
{

    protected $data;

    /**
     * WanSpeedTestsChart constructor.
     *
     * @param SpiritSystems\DayByDay\Core\Support\Livewire\ChartComponentData $data
     */
    public function __construct(ChartComponentData $data)
    {
        parent::__construct();

        $this->data = $data;

        $this->loader(false);

        $this->options([
            'type' => 'bar',
            'legend' => false,
            'scales' => [
                'yAxes' => [
                    ['stacked' => true ]
                ],
                'xAxes' => [
                    [
                        'stacked' => true,
                    ],
                ],
            ],
        ]);

        $this->labels($data->labels());

        foreach($data->datasets() as $dataset){
            $this->dataset($dataset['title'], 'bar', $dataset['data'])->options([
                'backgroundColor' => $dataset['color']
            ]);
        }
    }

    public function getData(){
        return $this->data;
    }

    public function getDatasets(){
        return $this->data->datasets();
    }
}