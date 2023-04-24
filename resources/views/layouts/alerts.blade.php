@if(session('success'))
    <div class="alert alert-success">
        <button class="close" data-dismiss="alert">&times;</button>
        <strong>Success! </strong>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        <button class="close" data-dismiss="alert">&times;</button>
        <strong>Error: </strong>{{ session('error') }}
    </div>
@endif
@if(session('warning'))
    <div class="alert alert-warning">
        <button class="close" data-dismiss="alert">&times;</button>
        <strong>Warning: </strong>{{ session('warning') }}
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info">
        <button class="close" data-dismiss="alert">&times;</button>
        {{ session('info') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>You have following errors in your input values.</strong>
        <ul class="m-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
