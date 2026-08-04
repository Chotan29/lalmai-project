{{-- Shared chrome for the public result pages. Kept apart from the admin theme on purpose:
     these pages are opened by students and parents from lalmaigc.edu.bd, often on a phone,
     and must not drag in the whole back-office stylesheet. --}}
<style>
    body { background:#f4f7fb; font-family: Arial, Helvetica, sans-serif; color:#1f2e44; }
    .pr-wrap { max-width: 640px; margin: 36px auto; padding: 0 15px; }
    .pr-wide { max-width: 1400px; }
    .pr-card { background:#fff; border:1px solid #e3e8ef; border-radius:12px; padding:26px; }
    .pr-head { text-align:center; margin-bottom:22px; }
    .pr-head img { max-height:64px; margin-bottom:10px; }
    .pr-head h4 { font-weight:700; margin:0; color:#0f5132; }
    .pr-head .sub { color:#6c757d; font-size:13.5px; margin-top:6px; }
    .pr-note { background:#f8fafc; border:1px solid #e6ebf2; border-radius:8px; padding:12px 14px;
               font-size:12.5px; color:#5c6470; margin-top:18px; }
    .pr-alert { border-radius:8px; padding:12px 14px; font-size:14px; margin-bottom:16px; }
    .pr-alert.bad { background:#fdecea; border:1px solid #f3c2bd; color:#8f2018; }
    .pr-alert.info { background:#eef4fd; border:1px solid #cddffa; color:#1f5aa6; }
    .pr-label { font-weight:600; font-size:13.5px; margin-bottom:4px; display:block; }
    .pr-btn { background:#0f5132; border-color:#0f5132; }
    .pr-btn:hover { background:#0a3a24; border-color:#0a3a24; }
    .pr-foot { text-align:center; color:#8a94a3; font-size:12px; margin-top:18px; }
    .pr-student { display:flex; justify-content:space-between; flex-wrap:wrap; gap:8px;
                  border-bottom:1px solid #e6ebf2; padding-bottom:12px; margin-bottom:14px; }
    .pr-student .nm { font-weight:700; font-size:17px; }
    .pr-student .rl { color:#6c757d; font-size:13px; }
    .pr-gpa { font-weight:700; padding:6px 16px; border-radius:6px; font-size:15px; }
    .pr-gpa.is-pass { background:#e7f3ec; color:#0f5132; }
    .pr-gpa.is-fail { background:#fdecea; color:#c0392b; }
    .pr-sheets { margin-top:18px; }
    .pr-sheets-title { font-size:13px; color:#6c757d; margin:0 0 8px 2px; }
    .pr-sheet { display:flex; align-items:center; gap:12px; background:#fff; border:1px solid #e3e8ef;
                border-radius:8px; padding:12px 14px; margin-bottom:8px; text-decoration:none;
                color:#1f2e44; }
    .pr-sheet:hover { border-color:#0f5132; }
    .pr-dept { background:#e7f3ec; color:#0f5132; font-size:12px; padding:4px 12px; border-radius:6px;
               white-space:nowrap; }
    .pr-sheet-main { flex:1; min-width:0; }
    .pr-sheet-main .t { display:block; font-size:14px; }
    .pr-sheet-main .m { display:block; font-size:12px; color:#6c757d; }
    .pr-sheet-go { font-size:13px; color:#0f5132; white-space:nowrap; }

    @media print { .pr-noprint { display:none !important; } body { background:#fff; }
                   .pr-card { border:none; } }
</style>
