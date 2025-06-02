<h2>Values for: {{ $lookup }}</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Value</th>
        <th>Name</th>
        <th>Short</th>
    </tr>
    @foreach($items as $item)
    <tr>
        <td>{{ $item['id'] }}</td>
        <td>{{ $item['value'] }}</td>
        <td>{{ $item['name'] }}</td>
        <td>{{ $item['short'] }}</td>
    </tr>
    @endforeach
</table>
