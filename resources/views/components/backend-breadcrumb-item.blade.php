@props(["route"=>"#", "icon"=>"", "title"=>"", "type"=>""])

@if($type)
<li class="breadcrumb-item active">
    <span>
        {{ $slot }}
    </span>
</li>
@else
<li class="breadcrumb-item">
    <a href='{{$route}}'>
        {{ $slot }}
    </a>
</li>
@endif
