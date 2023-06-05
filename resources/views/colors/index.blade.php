@extends ('layout.console')

@section ('content')

<div class="row wide-xl " >
    <ul>
        @foreach( $data as $color)
            <li>{{$color->name}}</li>
            {{-- <li>{{$color->external_ids->BrickLink->ext_ids}}</li> --}}
         @endforeach
    </ul>
</div>  

