<ul class="ks-cboxtags">
    @foreach($tags as $tag)
    <li><input type="checkbox" class="checkbox_tags" value="{{ $tag }}" id="checkbox{{ $tag }}"><label for="checkbox{{ $tag }}">{{ $tag }}</label></li>
    @endforeach
</ul> 