                <!-- small box -->
                <div class="small-box bg-white">
                    <div class="inner">
                        <h3>
                            {{$totalLeads}}
                         </h3>

                        <p>{{ __('Total leads') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{route('leads.unqualified')}}" class="small-box-footer">{{ __('All Leads') }} <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>