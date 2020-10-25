                <!-- small box -->
                <div class="small-box bg-white">
                    <div class="inner" style="min-height: 100px">
                        <h3>
                            {{$totalTasks}}
                        </h3>

                        <p>{{ __('Total tasks') }}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-book-outline"></i>
                    </div>
                    <a href="{{route('tasks.index')}}" class="small-box-footer">{{ __('All Tasks') }} <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
