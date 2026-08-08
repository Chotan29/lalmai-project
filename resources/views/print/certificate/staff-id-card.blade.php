<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Staff ID Cards</title>
    <style>
        /* The same card the students carry - 54 x 86 mm, front and back - so a college that
           already prints student cards can use the same blanks, the same printer settings and
           the same lanyards. What changes is what a member of staff needs on it: a designation
           in place of a group, a joining date in place of a session, and no parent's number.

           The accent colour is set once per card and every coloured part follows it. */
        :root {
            --accent: #1f5c8b;
            --college-red: #ed1c24;
            --college-green: #007a33;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { background: #9e9e9e; font-family: Arial, Helvetica, sans-serif; }

        .toolbar { text-align: center; padding: 12px; }
        .toolbar button {
            font-size: 15px; font-weight: bold; padding: 8px 26px; cursor: pointer;
            background: #2d6da3; color: #fff; border: 0; border-radius: 4px;
        }

        .sheet { display: flex; flex-wrap: wrap; justify-content: center; gap: 8mm; padding: 6mm; }

        .card {
            width: 54mm; height: 85.6mm; background: #fff; overflow: hidden;
            position: relative; display: flex; flex-direction: column;
            box-shadow: 0 1px 6px rgba(0,0,0,.45);
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }

        /* ---------- FRONT ---------- */
        .f-top { text-align: center; padding-top: 1.6mm; }
        .f-top img.monogram { width: 8mm; height: 8mm; object-fit: contain; }
        .f-govt { font-size: 5.7pt; font-weight: bold; color: #000; margin-top: .4mm; }
        .f-college { font-size: 12.5pt; font-weight: 900; color: var(--college-red); letter-spacing: -0.2px; margin-top: .1mm; }
        .f-addr { font-size: 7pt; font-weight: bold; color: var(--college-green); margin-top: .1mm; }
        .f-badge {
            display: inline-block; background: var(--accent); color: #fff; font-weight: 900;
            font-size: 7.6pt; padding: .6mm 3.6mm; border-radius: 3mm; margin-top: .8mm; letter-spacing: .3px;
        }
        .f-photo-wrap { text-align: center; margin-top: 1mm; }
        .f-photo {
            width: 17.5mm; height: 20mm; object-fit: cover;
            border: 0.55mm solid var(--accent); border-radius: 1.8mm; background: #eee;
        }
        /* The designation is what a visitor reads first on a member of staff's card, so it sits
           where the student card puts the roll number. */
        .f-desig { text-align: center; font-size: 10.5pt; font-weight: 900; color: var(--accent); margin-top: .7mm; padding: 0 2mm; line-height: 1.15; }
        .f-desig.f-desig-sm { font-size: 8.4pt; }
        .f-rows { padding: .6mm 3mm 0 3mm; }
        .f-mid { display: flex; align-items: flex-end; padding: 0 2.6mm 2.4mm 3mm; margin-top: auto; }
        .f-left { flex: 1; min-width: 0; }
        .f-row { display: flex; font-size: 7.4pt; line-height: 1.4; font-family: 'Arial Narrow', Arial, sans-serif; }
        .f-row .lb { width: 15mm; font-weight: bold; flex: none; white-space: nowrap; }
        .f-row .cl { width: 2.2mm; flex: none; font-weight: bold; }
        .f-row .vl { font-weight: bold; }
        .f-sign { text-align: center; display: inline-block; margin-top: 1.2mm; margin-left: 2mm; }
        .f-sign img { width: 12mm; height: 4.5mm; object-fit: contain; display: block; margin: 0 auto; }
        .f-sign .role { font-size: 6.6pt; font-weight: bold; border-top: 0.35mm solid #000; padding-top: .3mm; margin-top: .2mm; }
        .f-qr { flex: none; margin-left: 1mm; align-self: flex-end; }
        .f-qr img { width: 12mm; height: 12mm; display: block; }

        /* ---------- BACK ---------- */
        .b-top { text-align: center; padding-top: 2mm; }
        .b-top img.clogo { width: 11mm; height: 10.7mm; object-fit: contain; border-radius: 50%; }
        .b-contact { text-align: center; font-size: 7pt; font-weight: bold; line-height: 1.4; margin-top: 1mm; padding: 0 2mm; }
        .b-box {
            border: 0.5mm solid var(--accent); border-radius: 2.4mm;
            margin: 1.4mm 2.6mm 0 2.6mm; padding: 1mm 1.8mm 1.4mm 1.8mm;
        }
        .b-box .ttl { text-align: center; color: var(--accent); font-weight: 900; font-size: 8.6pt; margin-bottom: .6mm; }
        .b-row { display: flex; font-size: 6.9pt; line-height: 1.4; font-family: 'Arial Narrow', Arial, sans-serif; }
        .b-row .lb { width: 18.5mm; font-weight: bold; flex: none; white-space: nowrap; }
        .b-row .cl { width: 2.2mm; flex: none; font-weight: bold; }
        .b-row .vl { font-weight: bold; min-width: 0; overflow-wrap: anywhere; word-break: normal; }
        .b-found { text-align: center; color: var(--accent); font-weight: 900; font-size: 7.4pt; padding: .6mm 2mm 1.4mm 2mm; margin-top: auto; }
        .b-strip { height: 5mm; background: var(--accent); flex: none; }

        .pair { display: contents; }

        .print-modal {
            display: none; position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,.45); align-items: center; justify-content: center;
        }
        .print-modal.open { display: flex; }
        .print-modal-box {
            background: #fff; border-radius: 8px; padding: 22px 26px; text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,.35); min-width: 300px; font-family: Arial, sans-serif;
        }
        .print-modal-box h3 { font-size: 16px; margin-bottom: 16px; color: #222; }
        .print-modal-box button {
            display: block; width: 100%; margin: 8px 0; padding: 11px 16px; cursor: pointer;
            font-size: 14px; font-weight: bold; border: 0; border-radius: 5px; color: #fff;
        }
        .print-modal-box button.opt-main { background: #2d6da3; }
        .print-modal-box button.opt-demo { background: #007a33; }
        .print-modal-box button.cancel { background: #999; }

        @media print {
            /* Browsers drop background colour unless "Background graphics" is ticked, and it is
               off by default - which is how a demo sheet comes out black and white. */
            *, *::before, *::after {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .toolbar, .print-modal { display: none !important; }
            html, body { background: #fff; }
            .sheet { display: block; padding: 0; }
            .card { box-shadow: none; page-break-after: always; }

            /* DEMO (A4): front and back side by side, three people to a page */
            body.demo-print .sheet { padding: 0; }
            body.demo-print .pair {
                display: flex; justify-content: center; align-items: flex-start;
                gap: 4mm; margin-bottom: 4mm;
                page-break-inside: avoid; break-inside: avoid;
            }
            body.demo-print .card { page-break-after: auto; }
            body.demo-print .pair:nth-of-type(3n) { page-break-after: always; }
        }
    </style>
</head>
<body>
    <style id="pageStyle">@page { size: 54mm 86mm; margin: 0; }</style>
    <div class="toolbar"><button onclick="openPrintModal()">&#128424; Print ID Cards</button></div>

    <div class="print-modal" id="printModal">
        <div class="print-modal-box">
            <h3>Select Print Type</h3>
            <button class="opt-main" onclick="doPrint('main')">&#128424; Main Card Print (54 &times; 86 mm)</button>
            <button class="opt-demo" onclick="doPrint('demo')">&#128196; Demo Print (A4 &mdash; 3 cards / page)</button>
            <button class="cancel" onclick="closePrintModal()">Cancel</button>
        </div>
    </div>

    <div class="sheet">
    @if(isset($data['staff']) && $data['staff']->count() > 0)
        @foreach($data['staff'] as $staff)
            @php
                $name = strtoupper(trim(preg_replace('/\s+/', ' ', $staff->first_name.' '.$staff->middle_name.' '.$staff->last_name)));
                $designation = strtoupper(trim((string) $staff->designation_title));
                $father = strtoupper(trim((string) $staff->father_name));
                $mother = strtoupper(trim((string) $staff->mother_name));
                $addressParts = array_filter([trim((string) $staff->address), trim((string) $staff->state)]);
                $address = strtoupper(implode(', ', $addressParts));

                /* A colour per kind of post, so a card can be told apart across a room: teaching
                   staff in college blue, office in green, support in brown. First match wins. */
                $accentMap = [
                    'principal'     => '#7b1fa2',
                    'vice'          => '#4527a0',
                    'professor'     => '#1f5c8b',
                    'lecturer'      => '#1565c0',
                    'teacher'       => '#1565c0',
                    'demonstrator'  => '#00695c',
                    'librarian'     => '#00838f',
                    'accountant'    => '#007a33',
                    'clerk'         => '#007a33',
                    'office'        => '#007a33',
                    'computer'      => '#0277bd',
                    'lab'           => '#00695c',
                    'guard'         => '#5d4037',
                    'peon'          => '#5d4037',
                    'driver'        => '#5d4037',
                    'cleaner'       => '#5d4037',
                ];
                $accent = '#1f5c8b';
                foreach ($accentMap as $kw => $clr) {
                    if ($designation !== '' && stripos($designation, $kw) !== false) { $accent = $clr; break; }
                }

                $dob = $staff->date_of_birth ? \Carbon\Carbon::parse($staff->date_of_birth)->format('d-m-Y') : '';
                $joined = $staff->join_date ? \Carbon\Carbon::parse($staff->join_date)->format('d-m-Y') : '';
                $mobile = trim((string) $staff->mobile_1) ?: trim((string) $staff->mobile_2) ?: trim((string) $staff->home_phone);
                $photo = $staff->staff_image
                    ? asset('images/staff/'.$staff->staff_image)
                    : asset('assets/images/avatars/profile-pic.jpg');
            @endphp

            <div class="pair">
            {{-- FRONT --}}
            <div class="card" style="--accent: {{ $accent }};">
                <div class="f-top">
                    <img class="monogram" src="{{ asset('images/idcard/govt_monogram.png') }}" alt="">
                    <div class="f-govt">Government of the People's Republic of Bangladesh</div>
                    <div class="f-college">Lalmai Govt. College</div>
                    <div class="f-addr">Cumilla Sadar Dakshin, Cumilla</div>
                    <div class="f-badge">STAFF</div>
                </div>
                <div class="f-photo-wrap">
                    <img class="f-photo" src="{{ $photo }}" alt=""
                         onerror="this.onerror=null;this.src='{{ asset('assets/images/avatars/profile-pic.jpg') }}';">
                </div>
                @if($designation !== '')
                    <div class="f-desig {{ strlen($designation) > 18 ? 'f-desig-sm' : '' }}">{{ $designation }}</div>
                @endif
                <div class="f-rows">
                    <div class="f-row"><span class="lb">Name</span><span class="cl">:</span><span class="vl">{{ $name }}</span></div>
                    @if(trim((string) $staff->reg_no) !== '')
                        <div class="f-row"><span class="lb">ID No</span><span class="cl">:</span><span class="vl">{{ $staff->reg_no }}</span></div>
                    @endif
                </div>
                <div class="f-mid">
                    <div class="f-left">
                        @if($joined !== '')
                            <div class="f-row"><span class="lb">Joining Date</span><span class="cl">:</span><span class="vl">{{ $joined }}</span></div>
                        @endif
                        @if(trim((string) $staff->blood_group) !== '')
                            <div class="f-row"><span class="lb">Blood Group</span><span class="cl">:</span><span class="vl">{{ $staff->blood_group }}</span></div>
                        @endif
                        <div class="f-sign">
                            <img src="{{ asset('images/idcard/principal_sign.png') }}" alt="">
                            <div class="role">Principal</div>
                        </div>
                    </div>
                    @if($staff->qr_data_uri)
                        <div class="f-qr"><img src="{{ $staff->qr_data_uri }}" alt="QR"></div>
                    @endif
                </div>
            </div>

            {{-- BACK --}}
            <div class="card" style="--accent: {{ $accent }};">
                <div class="b-top">
                    <img class="clogo" src="{{ asset('images/idcard/college_logo.jpg') }}" alt="">
                </div>
                <div class="b-contact">
                    Cumilla Sadar Dakshin, Cumilla<br>
                    Web: lalmaigc.edu.bd<br>
                    E-mail: lalmaicollege1969@gmail.com<br>
                    lalmai_college@yahoo.com<br>
                    principallgc2026@gmail.com<br>
                    Principal Mob: 01309-105746
                </div>
                <div class="b-box">
                    <div class="ttl">Personal Details</div>
                    @if($dob !== '')
                        <div class="b-row"><span class="lb">Date of Birth</span><span class="cl">:</span><span class="vl">{{ $dob }}</span></div>
                    @endif
                    @if($father !== '')
                        <div class="b-row"><span class="lb">Father's Name</span><span class="cl">:</span><span class="vl">{{ $father }}</span></div>
                    @endif
                    @if($mother !== '')
                        <div class="b-row"><span class="lb">Mother's Name</span><span class="cl">:</span><span class="vl">{{ $mother }}</span></div>
                    @endif
                    @if($address !== '')
                        <div class="b-row"><span class="lb">Address</span><span class="cl">:</span><span class="vl">{{ $address }}</span></div>
                    @endif
                    @if($mobile !== '')
                        <div class="b-row"><span class="lb">Mobile</span><span class="cl">:</span><span class="vl">{{ $mobile }}</span></div>
                    @endif
                    @if(trim((string) $staff->email) !== '')
                        <div class="b-row"><span class="lb">E-mail</span><span class="cl">:</span><span class="vl">{{ $staff->email }}</span></div>
                    @endif
                    @if(trim((string) $staff->national_id_1) !== '')
                        <div class="b-row"><span class="lb">NID</span><span class="cl">:</span><span class="vl">{{ $staff->national_id_1 }}</span></div>
                    @endif
                </div>
                <div class="b-found">If it is found, please inform the College Office</div>
                <div class="b-strip"></div>
            </div>
            </div>{{-- /.pair --}}
        @endforeach
    @else
        <p style="background:#fff;padding:20px;">No staff selected.</p>
    @endif
    </div>

    <script>
    /* Auto-fit: a long designation or address can overflow a fixed-height card, so the detail
       rows are shrunk just enough to fit. Inline sizes are reset first each run, or repeated
       calls (load, then print) would compound and end up tiny. */
    (function () {
        function shrinkToFit(card) {
            var rows = card.querySelectorAll('.f-row, .b-row');
            if (!rows.length) return;

            rows.forEach(function (r) { r.style.fontSize = ''; r.style.lineHeight = ''; });

            var base = [];
            rows.forEach(function (r) { base.push(parseFloat(window.getComputedStyle(r).fontSize)); });

            var scale = 1, guard = 0;
            while (card.scrollHeight > card.clientHeight + 1 && scale > 0.60 && guard < 60) {
                scale -= 0.03;
                rows.forEach(function (r, i) {
                    r.style.fontSize = (base[i] * scale).toFixed(2) + 'px';
                    r.style.lineHeight = '1.28';
                });
                guard++;
            }
        }
        function fitAll() {
            document.querySelectorAll('.card').forEach(shrinkToFit);
        }
        window.__fitAll = fitAll;
        if (document.readyState === 'complete') { fitAll(); }
        else { window.addEventListener('load', fitAll); }
        window.addEventListener('beforeprint', fitAll);
    })();

    /* Print-type chooser: Main (54x86 per page) vs Demo (A4, 3 people per page) */
    function openPrintModal() { document.getElementById('printModal').classList.add('open'); }
    function closePrintModal() { document.getElementById('printModal').classList.remove('open'); }
    function doPrint(mode) {
        closePrintModal();
        var ps = document.getElementById('pageStyle');
        if (mode === 'demo') {
            document.body.classList.add('demo-print');
            ps.textContent = '@page { size: A4; margin: 6mm; }';
        } else {
            document.body.classList.remove('demo-print');
            ps.textContent = '@page { size: 54mm 86mm; margin: 0; }';
        }
        setTimeout(function () {
            if (window.__fitAll) window.__fitAll();
            window.print();
        }, 200);
    }
    window.addEventListener('afterprint', function () {
        document.body.classList.remove('demo-print');
    });
    document.addEventListener('DOMContentLoaded', function () {
        var m = document.getElementById('printModal');
        if (m) m.addEventListener('click', function (e) { if (e.target === this) closePrintModal(); });
    });
    </script>
</body>
</html>
