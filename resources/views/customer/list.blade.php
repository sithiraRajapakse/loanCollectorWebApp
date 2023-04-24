@extends('layouts.master')

@section('title') Customers List @endsection

@section('css')
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/libs/datatables/datatables.min.css')}}">
@endsection

@section('content')

    @component('common-components.breadcrumb')
        @slot('title') Customers @endslot
        @slot('li_1') List of Customers @endslot
    @endcomponent

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <table id="datatable" class="table table-bordered dt-responsive nowrap"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>N.I.C. No.</th>
                                <th>Telephone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Location</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse ($customers as $customer)
                                <tr>
                                    <td>{{$customer->name}}</td>
                                    <td>{{$customer->nic_no}}</td>
                                    <td>{{$customer->telephone}}</td>
                                    <td>{{$customer->email}}</td>
                                    <td>{{$customer->address}}</td>
                                    <td>{{$customer->location}}</td>
                                    <td>
                                        <a href="{{ route('customers.edit', $customer->id) }}"
                                           class="btn btn-sm btn-primary"><i class="bx bxs-user-detail"></i> Edit</a>
                                        <form action="{{ route('customers.process-delete', $customer->id) }}"
                                              class="d-inline" method="post"
                                              onsubmit="return confirm('Are you sure you need to delete this customer?'); ">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm"><i
                                                    class="bx bx-user-minus"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No customer records found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div>

@endsection

@section('script')

    <!-- Plugins js -->
    <script src="{{ URL::asset('assets/libs/datatables/datatables.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/jszip/jszip.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/pdfmake/pdfmake.min.js')}}"></script>

    <!-- Init js-->
    <script src="{{ URL::asset('assets/js/pages/datatables.init.js')}}"></script>

@endsection
