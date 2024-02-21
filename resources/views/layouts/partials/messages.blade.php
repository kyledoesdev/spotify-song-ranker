<div class="container justify-content-center">
    <div class="col d-flex justify-content-center">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{session()->get('success')}}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('errors'))
            <div class="d-flex justify-content-center col-md-6 alert alert-warning alert-dismissible fade show mb-2" role="alert" style="margin: 0 auto;">
                @foreach ($errors->all() as $error)
                    @if(!$loop->first)
                        <br>
                    @else
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    @endif
                    <h5>{{ $error }}</h5>
                @endforeach
            </div>
        @endif
    </div>                
</div>