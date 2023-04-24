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
                <div class="card-header bg-warning text-white">
                    <span class="fa fa-calendar"></span> Edit Holiday
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.holidays.update', $holiday->id) }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="date">Date</label>
                            <div class="controls">
                                <input type="date" id="date" name="date" class="form-control" value="{{ old('date', $holiday->date) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <div class="controls">
                                <input type="text" id="title" name="title" class="form-control" placeholder="Enter the holiday title" value="{{ $holiday->title }}">
                            </div>
                        </div>
                         <div class="form-group">
                             <label for="description">Description</label>
                             <div class="controls">
                                 <textarea name="description" id="description" cols="30" rows="3"
                                           class="form-control" placeholder="Enter a description for the holiday">{{ $holiday->description }}</textarea>
                             </div>
                         </div>
                        <div class="form-group">
                            <button type="submit" id="update_holiday" name="update_holiday" class="btn btn-warning">
                                <span class="fa fa-calendar-times"></span> Update Holiday
                            </button>
                            <a href="{{ route('settings.holidays') }}" class="href btn btn-primary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card" id="holiday_delete_panel">
                <div class="card-header bg-danger text-white">
                    <span class="fa fa-calendar"></span> Delete Holiday
                </div>
                <div class="card-body">
                    <p class="text-danger">Are you sure you want to delete <strong>{{ $holiday->title }}</strong>?</p>
                    <p>It will not be permanently deleted, but be moved to trash and no access from the system is available after.</p>
                    <form action="{{ route('settings.holidays.delete', $holiday->id) }}" method="post">
                        @csrf
                        <button class="btn btn-block btn-danger">
                            <span class="fa fa-trash"></span> Move this Holiday to trash.
                        </button>
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
