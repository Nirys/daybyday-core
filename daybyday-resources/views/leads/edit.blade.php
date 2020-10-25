@extends('layouts.master')
@section('heading')
    {{ __('Update opportunity') }} <span class="small">{{$lead ? '(' . $lead->name . ')': ''}}</span>
@stop

@section('content')
    <div class="row">
        
        {!! Form::model($lead, [
            'method' => 'PATCH',
            'route' => ['leads.update', $lead->external_id],
            ]) !!}
            @include('leads.form')
        </form>
    </div>
@stop
@push('style')
    <style>
        .picker, .picker__holder {
            width: 128%;
        }
        .picker--time .picker__holder {
            width: 30%;
        }
        .picker--time {
            min-width: 0px;
            max-width: 0px;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $('#description').summernote({
            toolbar: [
                [ 'fontsize', [ 'fontsize' ] ],
                [ 'font', [ 'bold', 'italic', 'underline','clear'] ],   
                [ 'color', [ 'color' ] ],
                [ 'para', [ 'ol', 'ul', 'paragraph'] ],
                [ 'table', [ 'table' ] ],
                [ 'insert', [ 'link'] ],
                [ 'view', [ 'fullscreen' ] ]
            ],
             height:300,
             disableDragAndDrop: true

           });
        $('#deadline').pickadate({
            hiddenName:true,
            format: "{{frontendDate()}}",
            formatSubmit: 'yyyy/mm/dd',
            closeOnClear: false,
        });
        $('#contact_time').pickatime({
            format:'{{frontendTime()}}',
            formatSubmit: 'HH:i',
            hiddenName: true
        })
    </script>
@endpush

