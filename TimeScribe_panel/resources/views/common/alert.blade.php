{{-- Alerta bootstrap básica --}}
<div class="alert alert-{{$style}} alert-dismissible fade show" role="alert">
    {{$content}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
