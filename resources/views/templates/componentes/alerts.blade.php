@if (isset($errors) && $errors->any())
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@elseif(session('success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4 class="text-strong text-center">{{ session('success') }}</h4>
    </div>
@elseif(session('warning'))
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4 class="text-strong text-center">{{ session('warning') }}</h4>
    </div>
@elseif(session('default'))
    <div class="alert alert-default">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4 class="text-strong text-center">{{ session('default') }}</h4>
    </div>
@elseif(session('error'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4 class="text-strong text-center">{{ session('error') }}</h4>
    </div>
@endif