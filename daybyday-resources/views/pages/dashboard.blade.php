@extends('layouts.master')

@section('content')
@push('scripts')
    <script>
        $(document).ready(function () {
            if(!'{{$settings->company}}') {
                $('#modal-create-client').modal({backdrop: 'static', keyboard: false})
                $('#modal-create-client').modal('show');
            }
            $('[data-toggle="tooltip"]').tooltip(); //Tooltip on icons top

            $('.popoverOption').each(function () {
                var $this = $(this);
                $this.popover({
                    trigger: 'hover',
                    placement: 'left',
                    container: $this,
                    html: true,

                });
            });
        });
        $(document).ready(function () {
            if(!getCookie("step_dashboard") && "{{$settings->company}}") {
                $("#clients").addClass("in");
                // Instance the tour
                var tour = new Tour({
                    storage: false,
                    backdrop: true,
                    steps: [
                        {
                            element: ".col-lg-12",
                            title: "{{trans("Dashboard")}}",
                            content: "{{trans("This is your dashboard, which you can use to get a fast and nice overview, of all your tasks, leads, etc.")}}",
                            placement: 'top'
                        },
                        {
                            element: "#myNavmenu",
                            title: "{{trans("Navigation")}}",
                            content: "{{trans("This is your primary navigation bar, which you can use to get around Daybyday CRM")}}"
                        }
                    ]
                });

                var canCreateClient = '{{ auth()->user()->can('client-create') }}';
                if(canCreateClient) {
                    tour.addSteps([
                        {
                            element: "#newClient",
                            title: "{{trans("Create New Client")}}",
                            content: "{{trans("Let's take our first step, by creating a new client")}}"
                        },
                        {
                            path: '/clients/create'
                        }
                    ])
                }

                // Initialize the tour
                tour.init();

                tour.start();
                setCookie("step_dashboard", true, 1000)
            }
            function setCookie(key, value, expiry) {
                var expires = new Date();
                expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 2000));
                document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
            }

            function getCookie(key) {
                var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
                return keyValue ? keyValue[2] : null;
            }
        });
    </script>
@endpush
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                @include('dashlets.tasks')
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                @include('dashlets.leads')
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                @include('dashlets.projects')
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                @include('dashlets.clients')
            </div>
            <!-- ./col -->
            <div class="col-lg-6 col-xs-6">
                @include('dashlets._outcome_by_month')
            </div>
            <div class="col-lg-6 col-xs-6">
                @include('pages._users')
            </div>
            @if(auth()->user()->can('absence-view'))
                <div class="col-lg-4 col-xs-6">
                    @include('pages._absent')
                </div>
            @endif
        </div>
        <!-- /.row -->
@if(!$settings->company)
<div class="modal fade" id="modal-create-client" tabindex="-1" role="dialog">
    @include('pages._firstStep')
</div>
@endif
@endsection
