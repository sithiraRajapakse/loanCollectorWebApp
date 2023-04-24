@extends('layouts.master')

@section('title') Dashboard @endsection

@section('content')

{{--    <div class="row">--}}
{{--        <div class="col-md-3">--}}
{{--            <div class="card mini-stats-wid">--}}
{{--                <div class="card-body">--}}
{{--                    <div class="media">--}}
{{--                        <div class="media-body">--}}
{{--                            <p class="text-muted font-weight-medium">Total Customers</p>--}}
{{--                            <h4 class="mb-0">60</h4>--}}
{{--                        </div>--}}

{{--                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">--}}
{{--                            <span class="avatar-title"><i class="bx bxs-user-check font-size-24"></i></span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-md-3">--}}
{{--            <div class="card mini-stats-wid">--}}
{{--                <div class="card-body">--}}
{{--                    <div class="media">--}}
{{--                        <div class="media-body">--}}
{{--                            <p class="text-muted font-weight-medium">Total Loans</p>--}}
{{--                            <h4 class="mb-0">$35, 723</h4>--}}
{{--                        </div>--}}

{{--                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">--}}
{{--                            <span class="avatar-title rounded-circle bg-primary"><i class="bx bxs-folder-plus font-size-24"></i></span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-md-3">--}}
{{--            <div class="card mini-stats-wid">--}}
{{--                <div class="card-body">--}}
{{--                    <div class="media">--}}
{{--                        <div class="media-body">--}}
{{--                            <p class="text-muted font-weight-medium">Total Due</p>--}}
{{--                            <h4 class="mb-0">$16.2</h4>--}}
{{--                        </div>--}}

{{--                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">--}}
{{--                            <span class="avatar-title rounded-circle bg-primary"><i class="bx bxs-bell-ring font-size-24"></i></span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-md-3">--}}
{{--            <div class="card mini-stats-wid">--}}
{{--                <div class="card-body">--}}
{{--                    <div class="media">--}}
{{--                        <div class="media-body">--}}
{{--                            <p class="text-muted font-weight-medium">Collected Today</p>--}}
{{--                            <h4 class="mb-0">$16.2</h4>--}}
{{--                        </div>--}}

{{--                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">--}}
{{--                            <span class="avatar-title rounded-circle bg-primary"><i class="bx bx-wallet font-size-24"></i></span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="row">--}}
{{--        <div class="col-lg-4">--}}
{{--            <div class="card border-primary">--}}
{{--                <div class="card-header bg-primary text-white">Holiday Calendar</div>--}}
{{--                <div class="card-body">--}}
{{--                    <table class="table table-bordered">--}}
{{--                        <tbody>--}}
{{--                        {!! \generate_calendar_html() !!}--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-lg-8">--}}
{{--            <div class="card">--}}
{{--                <div class="card-header bg-primary text-white">--}}
{{--                    Monthly Summary 2021--}}
{{--                </div>--}}
{{--                <div class="card-body">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-md-4 text-center">--}}
{{--                            <h3 class="mb-0">0</h3>--}}
{{--                            <p>Total Loans</p>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-4 text-center">--}}
{{--                            <h3 class="mb-0">0</h3>--}}
{{--                            <p>Total Collected</p>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-4 text-center">--}}
{{--                            <h3 class="mb-0">0</h3>--}}
{{--                            <p>Total Arrears</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <canvas id="monthly-summary-chart" height="300"></canvas>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection

{{--@section('script')--}}
{{--    <script src="{{ asset('assets/libs/chart-js/chart-js.min.js') }}"></script>--}}
{{--@endsection--}}

{{--@section('script-bottom')--}}
{{--    <script>--}}
{{--        // init chart js charts--}}

{{--        function generateLineChart() {--}}
{{--            let data = {--}}
{{--                labels: [1500, 1600, 1700, 1750, 1800, 1850, 1900, 1950, 1999, 2050],--}}
{{--                datasets: [{--}}
{{--                    data: [86, 114, 106, 106, 107, 111, 133, 221, 783, 2478],--}}
{{--                    label: "Africa",--}}
{{--                    borderColor: "#3e95cd",--}}
{{--                    fill: false--}}
{{--                }, {--}}
{{--                    data: [282, 350, 411, 502, 635, 809, 947, 1402, 3700, 5267],--}}
{{--                    label: "Asia",--}}
{{--                    borderColor: "#8e5ea2",--}}
{{--                    fill: false--}}
{{--                }, {--}}
{{--                    data: [168, 170, 178, 190, 203, 276, 408, 547, 675, 734],--}}
{{--                    label: "Europe",--}}
{{--                    borderColor: "#3cba9f",--}}
{{--                    fill: false--}}
{{--                }, {--}}
{{--                    data: [40, 20, 10, 16, 24, 38, 74, 167, 508, 784],--}}
{{--                    label: "Latin America",--}}
{{--                    borderColor: "#e8c3b9",--}}
{{--                    fill: false--}}
{{--                }, {--}}
{{--                    data: [6, 3, 2, 2, 7, 26, 82, 172, 312, 433],--}}
{{--                    label: "North America",--}}
{{--                    borderColor: "#c45850",--}}
{{--                    fill: false--}}
{{--                }--}}
{{--                ]--}}
{{--            };--}}

{{--            new Chart(document.getElementById("monthly-summary-chart"), {--}}
{{--                type: 'line',--}}
{{--                data: data,--}}
{{--                options: {--}}
{{--                    // title: {--}}
{{--                    //     display: false,--}}
{{--                    //     text: 'World population per region (in millions)'--}}
{{--                    // }--}}
{{--                }--}}
{{--            });--}}

{{--        }--}}

{{--        $(function(){--}}
{{--            generateLineChart();--}}
{{--        });--}}
{{--    </script>--}}
{{--@endsection--}}
