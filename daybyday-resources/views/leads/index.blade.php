@extends('layouts.master')
@section('heading')
    {{__('All Opportunities')}}
@stop

@section('content')
    <table class="table table-hover" id="leads-table">
        <thead>
        <tr>
            <th>{{ __('Title') }}</th>
            <th>{{ __('Deadline') }}</th>
            <th>{{ __('Probability') }}</th>
            <th>{{ __('Status') }}</th>
        </tr>
        </thead>
    </table>    
@stop

@push('scripts')
    <style type="text/css">
        .table > tbody > tr > td {
            border-top: none !important;
        }
        .table-actions {
            opacity: 0;
        }

        #leads-table tbody tr:hover .table-actions {
            opacity: 1;
        }
    </style>
    <script>
        $(function () {
            var table = $('#leads-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: '{!! route('leads.allData') !!}',
                language: {
                    url: '{{ asset('lang/' . (in_array(\Lang::locale(), ['dk', 'en']) ? \Lang::locale() : 'en') . '/datatable.json') }}'
                },
                drawCallback: function () {
                    var length_select = $(".dataTables_length");
                    var select = $(".dataTables_length").find("select");
                    select.addClass("tablet__select");
                },
                columns: [
                    {data: 'namelink', name: 'title'},
                    {data: 'deadline', name: 'deadline',},
                    {data: 'probability'},
                    {data: 'statustext'}
                ]
            });

            $('#leads-table').on('click', function(){
                console.log( table.cell(this))
            });

            table.columns(4).search('^' + 'Open' + '$', true, false).draw();
            $('#status-lead').change(function () {
                selected = $("#status-lead option:selected").val();
                if (selected == "all") {
                    table.columns(4).search('').draw();
                } else {
                    table.columns(4).search(selected ? '^' + selected + '$' : '', true, false).draw();
                }
            });
        });
    </script>
@endpush
