@extends('layouts.master')

@section('title') Holidays @endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Holiday Calendar @endslot
        @slot('li_1') Manage holidays @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-4">
            <div class="card" id="holiday_edit_panel">
                <div class="card-header bg-success text-white">
                    <span class="fa fa-calendar"></span> Add Holiday
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.holidays.create') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="date">Date</label>
                            <div class="controls">
                                <input type="date" id="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <div class="controls">
                                <input type="text" id="title" name="title" class="form-control" placeholder="Enter the holiday title">
                            </div>
                        </div>
                         <div class="form-group">
                             <label for="description">Description</label>
                             <div class="controls">
                                 <textarea name="description" id="description" cols="30" rows="3"
                                           class="form-control" placeholder="Enter a description for the holiday"></textarea>
                             </div>
                         </div>
                        <div class="form-group">
                            <button type="submit" id="create_holiday" name="create_holiday" class="btn btn-success">
                                <span class="fa fa-calendar-plus"></span> Create Holiday
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            @include('settings.holiday-calendar')
        </div>
    </div>
@endsection

@section('styles-top')
    <style>
        #calendar tr, #calendar th, #calendar td{
            border: 1px solid #ced4da;
            border-collapse: collapse;
            background-color: #ffffff;
        }
         #calendar thead th {
             font-weight: lighter;
             font-size: 11px;
         }
        #calendar tbody td {
            height: 100px;
            width: 14.286%;
            font-size: 0.8em;
        }
        #calendar tbody td:hover {
            background-color: #f8f8fb;
        }
        #calendar tbody td.holiday .day-num {
            font-size: 2em;
        }
        #calendar tbody td .badge {
            display: block;
        }
        #calendar tbody td.holiday {
            background-color: #ffffb2;
            overflow-wrap: break-word;
            word-break: break-all;
        }
        #calendar tbody td.holiday:hover {
            background-color: #e8e895;
        }
    </style>
@endsection

@section('script-bottom')
@endsection
