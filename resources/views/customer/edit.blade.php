@extends('layouts.master')

@section('title') Edit Customer Details @endsection

@section('content')


    @component('common-components.breadcrumb')
        @slot('title') Edit Customer Details @endslot
        @slot('li_1') Edit Details of Customers @endslot
    @endcomponent


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('customers.process-update', $customer->id) }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="customer_name">Name</label>
                            <input class="form-control @error('customer_name') is-invalid @enderror" id="customer_name"
                                   name="customer_name" required="" placeholder="Enter full name of the customer here"
                                   value="{{ old('customer_name', $customer->name) }}">
                            @error('customer_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="nic_no">N.I.C. No.</label>
                                    <input class="form-control @error('nic_no') is-invalid @enderror" id="nic_no"
                                           name="nic_no"
                                           placeholder="Enter N.I.C. number here"
                                           value="{{ old('nic_no', $customer->nic_no) }}">
                                    @error('nic_no')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="telephone">Telephone</label>
                                <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                       id="telephone" name="telephone"
                                       placeholder="Enter telephone number here"
                                       value="{{ old('telephone', $customer->telephone) }}">
                                @error('telephone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-lg-5">
                                <label for="email_address">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email_address" id="email_address"
                                       placeholder="Enter email Address of customer here"
                                       value="{{ old('email', $customer->email) }}">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-lg-7">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" cols="30" rows="3"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Enter customer address here">{{ old('address', $customer->address) }}</textarea>
                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-lg-5">
                                <label for="location">Location / Whereabouts</label>
                                <textarea name="location" id="location" cols="30" rows="3"
                                          class="form-control @error('location') is-invalid @enderror"
                                          placeholder="Enter customer's location details such as landmarks, whereabouts details here..">{{ old('location', $customer->location) }}</textarea>
                                @error('location')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="form-group">
                                <button type="submit" id="update_customer" class="btn btn-warning">Update Customer
                                    Details
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Documents</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <form action="{{ route('customers.process-document-upload', $customer->id) }}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="title">Document Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           placeholder="Enter the document title here. Ex: N.I.C. copy" required>
                                </div>
                                <div class="form-group">
                                    <input type="file" name="uploadfile" id="uploadfile" required accept="image/*,.pdf">
                                    {{-- Change into dropzone later--}}
                                    <p class="small muted mt-3"><strong>NOTE: </strong>Files of standard image types and
                                        PDF only.</p>
                                </div>
                                <div class="form-group">
                                    <button type="submit" id="upload" class="btn btn-primary"><span
                                            class="fa fa-file-upload"></span> Upload
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-8">
                            <table class="table table-striped table-bordered table-hover dataTable">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($customerDocuments as $doc)
                                    <tr>
                                        <td>
                                            {!! $doc->is_locked ? '<span class="fa fa-lock"></span>' : '' !!} {{ $doc->name }}
                                        </td>
                                        <td>
                                            @if($doc->is_locked)
                                                <form action="{{ route('customers.unlock-document', $doc->id) }}"
                                                      class="d-inline" method="post"
                                                      onsubmit="return confirm('Are you sure you want to unlock this document?')">
                                                    @csrf
                                                    <input type="hidden" name="customer_id" value="{{$customer->id}}">
                                                    <button type="submit"
                                                            class="btn btn-info btn-sm"
                                                            title="Locking this document prevents the document being deleted.">
                                                        <span class="fa fa-lock-open"></span> Unlock
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('customers.lock-document', $doc->id) }}"
                                                      class="d-inline" method="post"
                                                      onsubmit="return confirm('Are you sure you want to lock this document?')">
                                                    @csrf
                                                    <input type="hidden" name="customer_id" value="{{$customer->id}}">
                                                    <button type="submit" class="btn btn-warning btn-sm"
                                                            title="Locking this document prevents accidential deletion.">
                                                        <span class="fa fa-lock"></span> Lock
                                                    </button>
                                                </form>
                                                <form action="{{ route('customers.delete-document', $doc->id) }}"
                                                      class="d-inline" method="post"
                                                      onsubmit="return confirm('Are you sure you want to delete this document?')">
                                                    @csrf
                                                    <input type="hidden" name="customer_id" value="{{$customer->id}}">
                                                    <button type="submit" class="btn btn-danger btn-sm delete_custdoc"
                                                            title="Delete this document from the system. Needs administrator access level.">
                                                        <span class="fa fa-trash"></span> Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">No documents to display yet.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Loans</h4> </div>
                <div class="card-body">
               

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    {{-- parsleyjs validation library --}}
    <script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
@endsection
