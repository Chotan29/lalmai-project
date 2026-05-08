{{-- Minimal reusable load-failed partial --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Load Failed</title>
  <style>
    :root{--ink:#111827;--muted:#6b7280;--line:#e5e7eb;}
    body{font-family:system-ui, Segoe UI, Roboto, Arial, sans-serif; margin:40px; color:var(--ink)}
    .card{border:1px solid var(--line); border-radius:12px; padding:24px; max-width:720px}
    h1{font-size:20px; margin:0 0 8px}
    p{margin:0; color:var(--muted)}
  </style>
</head>
<body>
  <div class="card">
    <h1>Load Failed</h1>
    <p>{{ $message ?? 'Something went wrong while loading this section.' }}</p>
  </div>
</body>
</html>
