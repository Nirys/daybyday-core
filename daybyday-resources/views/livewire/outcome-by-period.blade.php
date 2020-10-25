<div class="tablet">
    <div class="tablet__head">
        <div class="tablet__head-label"><h3 class="tablet__head-title">Outcomes By Period</h3></div>        
    </div>
    <div class="tablet__body" wire:ignore wire:key="chart">
        @if($chart)
            {!! $chart->container() !!}
            <div style="position: absolute; top: 20px; right: 30px; background: rgba(255,255,255,0.7);">
                <div class="panel panel-default">
                    <div class="panel-heading" data-toggle="collapse" data-target="#{{$chart_id}}_legend">Legend</div>
                    <div class="panel-body collapse" id="{{$chart_id}}_legend">
                        @foreach($chart->getDatasets() as $dataset)
                            <div style="padding-bottom: 4px;">
                                <span style="display: inline-block; width: 20px; height: 20px; background-color: {!! $dataset['color'] !!}">&nbsp;</span>
                                {{ $dataset['title'] }}
                            </div>
                        @endforeach                        
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="tablet__footer">
        From: <input type="date" id="{{ $chart_id}}_actStartDate" value="{{$start->format('Y-m-d')}}"/>
        To: <input type="date" id="{{ $chart_id}}_actEndDate" value="{{$end->format('Y-m-d')}}"/>
        <button id="{{$chart_id}}_refresh">Refresh</button>
    </div>
</div>

@if($chart)
    @push('scripts')    
        <script>
            var {{ $chart_id }}_Script = function(){
                this.chartId = "{{ $chart_id}}";
                var me = this;
                jQuery(document.body).on('click', function(e){
                    if(e.target.id !== me.chartId + '_refresh'){
                        return;
                    }                    
                    @this.start = jQuery('#' + me.chartId + '_actStartDate').val();
                    @this.end = jQuery('#' + me.chartId + '_actEndDate').val();
                });
            }();
        </script>

        {!! $chart->script() !!}
    @endpush
@endif