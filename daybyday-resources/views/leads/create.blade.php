@extends('layouts.master')
@section('heading')
    {{ __('Create opportunity') }} <span class="small">{{$client ? '(' . $client->company_name . ')': ''}}</span>
@stop

@section('content')
    <div class="row">
        <form action="{{route('leads.store')}}" method="POST" id="createTaskForm">
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

