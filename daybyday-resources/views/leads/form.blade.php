<div class="col-sm-8">
                <div class="tablet">
                    <div class="tablet__body">
                            <div class="form-group">
                                <label for="title" class="control-label thin-weight">@lang('Opportunity Name')</label>
                                {!! Form::text('title', null, ['class' => 'form-control']) !!}
                            </div>


                            <div class="form-group">
                                <label for="next_steps" class="control-label thin-weight">@lang('Next Steps')</label>
                                {!! Form::textarea('next_steps', null, ['class' => 'form-control', 'rows' => 4]) !!}
                            </div>

                            <div class="form-group">
                                <label for="description" class="control-label thin-weight">@lang('Description')</label>
                                {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 10, 'cols'=>'50', 'id'=>'description']) !!}
                            </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="tablet">
                    <div class="tablet__body">
                        <div class="form-group">
                            <label for="deadline" class="control-label thin-weight">@lang('Status')</label>
                            <select name="status_id" id="status" class="form-control">
                                @foreach($statuses as $status => $statusK)
                                    <option value="{{$status}}">{{$statusK}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="probability" class="control-label thin-weight">@lang('Probability (%)')</label>
                            {!! Form::text('probability', null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label for="user_assigned_id" class="control-label thin-weight">@lang('Assigned To')</label>
                            <select name="user_assigned_id" id="user_assigned_id" class="form-control">
                                @foreach($users as $user => $userK)
                                    <option value="{{$user}}">{{$userK}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            @if(Request::get('client') != "" || isset($client))
                                <input type="hidden" name="client_external_id" value="{!! Request::get('client') ?: $client->external_id !!}">
                            @else
                            
                                <label for="client_external_id" class="control-label thin-weight">@lang('Client')</label>
                                <select name="client_external_id" id="client_external_id" class="form-control">
                                    @foreach($clients as $client => $clientK)
                                        <option value="{{$client}}">{{$clientK}}</option>
                                @endforeach
                                </select>
                            @endif

                        </div>
                        <div class="form-inline">
                            <div class="form-group col-sm-7" style="padding-left: 0px;">
                                <label for="deadline" class="control-label thin-weight">@lang('Expected Close')</label>
                                <input type="text" id="deadline" name="deadline" data-value="{{now()->addDays(3)}}" class="form-control">
                            </div>
                            <div class="form-group col-sm-5">
                                <label for="contact_time" class="control-label thin-weight">@lang("O'clock")</label>
                                <input type="text" id="contact_time" name="contact_time" value="{{\Carbon\Carbon::today()->setTime(15, 00)->format(carbonTime())}}" class="form-control">
                            </div>
                        </div>

                        <div class="form-inline">
                            <div class="form-group col-sm-6" style="padding-left: 0px;">
                                <label for="currency" class="control-label thin-weight">@lang('Currency')</label>
                                {!! Form::select('currency_id', SpiritSystems\DayByDay\Core\Models\Currency::all()->pluck('name','id')->toArray(), null, ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="lead_amount" class="control-label thin-weight">@lang('Opportunity Amount')</label>
                                {!! Form::text('lead_amount', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>

                       
                        {{csrf_field()}}
                        <div class="form-group">
                            <input type="submit" class="btn btn-md btn-brand movedown" id="createTask" value="{{__('Store lead')}}">
                        </div>
                    </div>
                </div>
            </div>