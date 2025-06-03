<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DDF Metadata Viewer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        ul { list-style: none; padding-left: 20px; }
        li { margin: 5px 0; }
        .key { color: #0077cc; cursor: pointer; }
        .key:hover { text-decoration: underline; }
        .key.disabled { color: #999; cursor: default; text-decoration: none; }
        #path { margin-bottom: 10px; }
        pre { background: #eee; padding: 10px; border-radius: 5px; overflow: auto; }
        button { margin-bottom: 10px; }
    </style>
</head>
<body>
<h2>DDF Metadata Viewer</h2>

<div id="path"><strong>Path:</strong> <span id="current-path">/</span></div>
<button onclick="goBack()">⬅️ Back</button>
<ul id="browser"></ul>
<div id="details"></div>

<script>
    const browser = document.getElementById('browser');
    const pathSpan = document.getElementById('current-path');
    const detailsDiv = document.getElementById('details');
    let currentPath = '';
    const historyStack = [];

    function fetchData(path = '') {
        const url = '/metadata/view' + (path ? '?key=' + encodeURIComponent(path) : '');

        fetch(url)
            .then(res => res.json())
            .then(data => {
                browser.innerHTML = '';
                detailsDiv.innerHTML = '';
                pathSpan.textContent = '/' + (path || '');

                if (Array.isArray(data)) {
                    data.forEach(key => addKeyToList(key, path));
                } else if (data._keys) {
                    data._keys.forEach(key => addKeyToList(key, path));
                } else {
                    const pre = document.createElement('pre');
                    pre.textContent = JSON.stringify(data, null, 2);
                    detailsDiv.appendChild(pre);
                }
            })
            .catch(err => {
                browser.innerHTML = '<li>⚠️ Failed to load data.</li>';
                console.error(err);
            });
    }

    function addKeyToList(key, parentPath) {
        const li = document.createElement('li');
        const isString = typeof key === 'string';
        const label = isString ? key : '[object]';

        li.innerHTML = `<span class="key ${isString ? '' : 'disabled'}">${label}</span>`;

        if (isString) {
            li.querySelector('.key').onclick = () => {
                historyStack.push(parentPath);
                const nextPath = parentPath ? `${parentPath}.${key}` : key;
                currentPath = nextPath;
                fetchData(nextPath);
            };
        }

        browser.appendChild(li);
    }

    function goBack() {
        if (historyStack.length > 0) {
            const prev = historyStack.pop();
            currentPath = prev;
            fetchData(prev);
        }
    }

    fetchData();
</script>
</body>
</html>
