<h2>Available DDF Metadata Lookups</h2>
<ul>
@foreach($lookupTypes as $lookup)
    <li><a href="{{ url('/ddf/metadata-values/' . $lookup) }}">{{ $lookup }}</a></li>
@endforeach
</ul>
