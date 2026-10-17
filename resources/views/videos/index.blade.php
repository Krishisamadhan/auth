<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Videos</title>
    <style>
        body{font-family:sans-serif;max-width:800px;margin:30px auto;padding:0 12px}
        .row{display:flex;gap:12px;margin:10px 0;align-items:center}
        img{width:160px;height:90px;object-fit:cover;background:#ddd}
        .err{color:red}
        .bar{height:10px;background:#eee;border-radius:5px;overflow:hidden;width:240px;margin-top:4px}
        .bar>i{display:block;height:100%;width:0;background:#2563eb;transition:width .3s}
        #up{display:none;margin:10px 0}
    </style>
</head>
<body>
    <h2>Upload video</h2>

    <form id="f">
        <input name="title" id="title" placeholder="Title" required>
        <input type="file" id="file" accept="video/mp4" required>
        <button id="btn">Upload / Resume</button>
    </form>
    <small>Upload beech mein ruk jaye (refresh/net issue) to same file dobara select karke Upload dabao, wahi se resume hoga.</small>

    <div id="up">
        <span id="uptxt">0%</span>
        <div class="bar"><i id="upbar"></i></div>
    </div>
    <p id="msg" class="err"></p>

    <h2>Videos</h2>
    @foreach($videos as $v)
        <div class="row" data-id="{{ $v->id }}"
             data-active="{{ in_array($v->status, ['pending','processing']) ? 1 : 0 }}">
            <img src="{{ $v->thumb_url }}" alt="">
            <div>
                <b>{{ $v->title }}</b><br>
                Status: <span class="st">{{ $v->status }}</span>
                <span class="pc">@if($v->status === 'processing') {{ $v->progress }}% @endif</span>
                @if($v->duration_seconds) | {{ gmdate('H:i:s', $v->duration_seconds) }} @endif

                @if(in_array($v->status, ['pending','processing']))
                    <div class="bar"><i class="pbar" style="width:{{ $v->progress }}%"></i></div>
                @endif

                @if($v->status === 'failed')
                    <br><small class="err">{{ $v->error }}</small>
                @endif

                @if($v->status === 'ready')
                    <br><a href="{{ route('videos.show', $v) }}">Watch</a>
                    | <a href="{{ $v->url }}" download>Download</a>
                @endif
            </div>
        </div>
    @endforeach

<script>
const CHUNK = 5 * 1024 * 1024; // 5 MB
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
const URLS  = {
    status:   "{{ route('videos.chunk.status') }}",
    chunk:    "{{ route('videos.chunk') }}",
    complete: "{{ route('videos.complete') }}",
    base:     "{{ url('/videos') }}",
};

const $ = id => document.getElementById(id);
let uploading = false;

window.addEventListener('beforeunload', e => {
    if (uploading) { e.preventDefault(); e.returnValue = ''; }
});

function randomId() {
    const a = new Uint8Array(16);
    crypto.getRandomValues(a);
    return Array.from(a, b => b.toString(16).padStart(2, '0')).join('');
}

function sendChunk(id, index, blob, onProgress) {
    return new Promise((resolve, reject) => {
        const fd = new FormData();
        fd.append('id', id);
        fd.append('index', index);
        fd.append('chunk', blob, 'c');

        const xhr = new XMLHttpRequest();
        xhr.open('POST', URLS.chunk);
        xhr.setRequestHeader('X-CSRF-TOKEN', CSRF);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.upload.onprogress = e => e.lengthComputable && onProgress(e.loaded);
        xhr.onload  = () => xhr.status === 200 ? resolve() : reject(new Error('HTTP ' + xhr.status));
        xhr.onerror = () => reject(new Error('Network'));
        xhr.send(fd);
    });
}

async function sendWithRetry(id, index, blob, onProgress) {
    for (let t = 1; t <= 5; t++) {
        try { return await sendChunk(id, index, blob, onProgress); }
        catch (e) {
            if (t === 5) throw e;
            $('msg').textContent = `Chunk ${index} retry ${t}...`;
            await new Promise(r => setTimeout(r, 2000 * t));
        }
    }
}

$('f').addEventListener('submit', async e => {
    e.preventDefault();

    const file  = $('file').files[0];
    const title = $('title').value.trim();
    if (!file || !title) return;

    const key = `up:${file.name}:${file.size}:${file.lastModified}`;
    let id = localStorage.getItem(key);
    if (!id) { id = randomId(); localStorage.setItem(key, id); }

    const total = Math.ceil(file.size / CHUNK);

    $('msg').textContent = '';
    $('up').style.display = 'block';
    $('btn').disabled = true;
    uploading = true;

    try {
        // Server se pucho kaun se chunks pehle se hain
        const r = await fetch(`${URLS.status}?id=${id}`, {headers: {Accept: 'application/json'}});
        const done = new Set((await r.json()).received);

        let doneBytes = 0;
        done.forEach(i => { if (i < total) doneBytes += Math.min(CHUNK, file.size - i * CHUNK); });

        const show = loaded => {
            const cur = doneBytes + loaded;
            const p = Math.min(100, Math.floor(cur / file.size * 100));
            $('upbar').style.width = p + '%';
            $('uptxt').textContent = `${p}% (${(cur/1048576).toFixed(0)} / ${(file.size/1048576).toFixed(0)} MB)`;
        };
        show(0);

        for (let i = 0; i < total; i++) {
            if (done.has(i)) continue;

            const blob = file.slice(i * CHUNK, Math.min(file.size, (i + 1) * CHUNK));
            await sendWithRetry(id, i, blob, show);

            doneBytes += blob.size;
            show(0);
        }

        $('uptxt').textContent = 'Jod rahe hain...';
        const res = await fetch(URLS.complete, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json'},
            body: JSON.stringify({id, total, title}),
        });

        if (!res.ok) {
            const j = await res.json().catch(() => ({}));
            throw new Error(j.message || ('Complete fail ' + res.status));
        }

        localStorage.removeItem(key);
        uploading = false;
        location.reload();
    } catch (err) {
        $('msg').textContent = err.message + ' — dobara Upload dabao, resume hoga.';
        $('btn').disabled = false;
        uploading = false;
    }
});

// ---- Processing progress (poll every 3s) ----
document.querySelectorAll('.row[data-active="1"]').forEach(row => {
    const id = row.dataset.id;
    const t = setInterval(async () => {
        try {
            const r = await fetch(`${URLS.base}/${id}/status`, {headers: {Accept: 'application/json'}});
            const d = await r.json();
            row.querySelector('.st').textContent = d.status;
            row.querySelector('.pc').textContent = d.status === 'processing' ? d.progress + '%' : '';
            const pb = row.querySelector('.pbar');
            if (pb) pb.style.width = d.progress + '%';
            if (d.status === 'ready' || d.status === 'failed') { clearInterval(t); location.reload(); }
        } catch (e) {}
    }, 3000);
});
</script>
</body>
</html>