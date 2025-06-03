<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DDF Metadata Viewer</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        ul { list-style: none; padding-left: 20px; }
        li { margin: 5px 0; }
        a.key-link { color: #0077cc; text-decoration: none; }
        a.key-link:hover { text-decoration: underline; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #eee; }
        .back { margin-bottom: 15px; display: inline-block; }
    </style>
</head>
<body>

<h2>DDF Metadata Viewer</h2>

@if($level === 1)
    <h3>المفاتيح الرئيسية:</h3>
    <ul>
        @foreach($keys as $key)
            <li><a class="key-link" href="{{ url('/metadata/view?key=' . urlencode($key)) }}">{{ $key }}</a></li>
        @endforeach
    </ul>

@elseif($level === 2)
    <a class="back" href="{{ url('/metadata/view') }}">⬅️ رجوع</a>
    <h3>المفاتيح داخل: {{ $title }}</h3>
    <ul>
        @foreach($keys as $subKey)
            <li><a class="key-link" href="{{ url('/metadata/view?key=' . urlencode($parentKey) . '&sub=' . urlencode($subKey)) }}">{{ $subKey }}</a></li>
        @endforeach
    </ul>

@elseif($level === 3)
    <a class="back" href="{{ url('/metadata/view?key=' . urlencode(request('key'))) }}">⬅️ رجوع</a>
    <h3>تفاصيل: {{ $title }}</h3>

    @if(empty($records))
        <p>⚠️ لا يوجد بيانات متاحة.</p>
    @else
        <table>
            <thead>
            <tr>
                @foreach(array_keys($records[0]) as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($records as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endif

</body>
</html>
