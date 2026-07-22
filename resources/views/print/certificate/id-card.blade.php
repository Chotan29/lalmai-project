<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student ID Cards</title>
    <style>
        :root {
            /* Change --accent once to recolor every card (badge, roll, borders, strip) */
            --accent: #e01e26;
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
        .f-roll { text-align: center; font-size: 11.5pt; font-weight: 900; color: var(--accent); margin-top: .7mm; white-space: nowrap; }
        .f-roll.f-roll-sm { font-size: 9pt; }
        /* Name/Session full width; DOB+Blood+sign left, QR right (like the sample) */
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
        .b-expiry { text-align: center; color: #000; font-weight: 900; font-size: 7.4pt; margin-top: auto; padding-top: 1mm; }
        .b-found { text-align: center; color: var(--accent); font-weight: 900; font-size: 7.4pt; padding: .6mm 2mm 1.4mm 2mm; }
        .b-strip { height: 5mm; background: var(--accent); flex: none; }

        @media print {
            .toolbar { display: none; }
            html, body { background: #fff; }
            .sheet { display: block; padding: 0; }
            .card { box-shadow: none; page-break-after: always; }
            @page { size: 54mm 86mm; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar"><button onclick="window.print()">&#128424; Print ID Cards</button></div>

    <div class="sheet">
    @if($data['student'] && $data['student']->count() > 0)
        @foreach($data['student'] as $student)
            @php
                $name = strtoupper(trim(preg_replace('/\s+/', ' ', $student->first_name.' '.$student->middle_name.' '.$student->last_name)));
                $father = strtoupper(trim(preg_replace('/\s+/', ' ', trim($student->father_first_name.' '.$student->father_middle_name.' '.$student->father_last_name))));
                $mother = strtoupper(trim(preg_replace('/\s+/', ' ', trim($student->mother_first_name.' '.$student->mother_middle_name.' '.$student->mother_last_name))));
                $addressParts = array_filter([trim((string) $student->address), trim((string) $student->state)]);
                $address = strtoupper(implode(', ', $addressParts));
                $group = strtoupper(trim((string) $student->faculty_name));
                /*display names per the approved sample*/
                if ($group === 'BUSINESS') { $group = 'BUSINESS STUDIES'; }

                /*Per-department accent color (badge, roll, borders, strip).
                  First matching keyword wins; default red.*/
                $accentMap = [
                    'science' => '#e01e26',
                    'humanities' => '#1565c0',
                    'business' => '#007a33',
                    'accounting' => '#00695c',
                    'management' => '#4527a0',
                    'english' => '#ad1457',
                    'marketing' => '#e65100',
                    'bbs' => '#5d4037',
                    'bss' => '#e65100',
                    'b.a' => '#7b1fa2',
                ];
                $accent = '#e01e26';
                foreach ($accentMap as $kw => $clr) {
                    if ($group !== '' && stripos($group, $kw) !== false) { $accent = $clr; break; }
                }
                $session = trim((string) $student->batch_title);
                /*HSC students (Class Eleven/Twelve) get the HSC prefix like the sample*/
                if ($session !== '' && stripos((string) $student->semester_name, 'class') !== false) {
                    $session = 'HSC '.$session;
                }
                $dob = $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d-m-Y') : '';
                /*Expiry: 31 December of (last batch year + 1), e.g. 2025-2026 -> 2027*/
                $expiry = '';
                if (preg_match_all('/(20\d{2})/', (string) $student->batch_title, $ym) && count($ym[1]) > 0) {
                    $expiry = '31 December '.((int) max($ym[1]) + 1);
                }
                $mobile = trim((string) $student->mobile_1) ?: trim((string) $student->home_phone);
                $photo = $student->student_image
                    ? asset('images/studentProfile/'.$student->student_image)
                    : asset('assets/images/avatars/profile-pic.jpg');
            @endphp

            {{-- FRONT --}}
            <div class="card" style="--accent: {{ $accent }};">
                <div class="f-top">
                    <img class="monogram" src="{{ asset('images/idcard/govt_monogram.png') }}" alt="">
                    <div class="f-govt">Government of the People's Republic of Bangladesh</div>
                    <div class="f-college">Lalmai Govt. College</div>
                    <div class="f-addr">Cumilla Sadar South, Cumilla</div>
                    @if($group !== '')
                        @if(strlen($group) <= 12)
                            <div class="f-badge">GROUP: {{ $group }}</div>
                        @else
                            <div class="f-badge" style="font-size: 6.4pt; padding: .7mm 2.5mm;">{{ $group }}</div>
                        @endif
                    @endif
                </div>
                <div class="f-photo-wrap">
                    <img class="f-photo" src="{{ $photo }}" alt=""
                         onerror="this.onerror=null;this.src='{{ asset('assets/images/avatars/profile-pic.jpg') }}';">
                </div>
                <div class="f-roll {{ strlen((string) $student->reg_no) > 11 ? 'f-roll-sm' : '' }}">Roll No : {{ $student->reg_no }}</div>
                <div class="f-rows">
                    <div class="f-row"><span class="lb">Name</span><span class="cl">:</span><span class="vl">{{ $name }}</span></div>
                    @if($session !== '')
                        <div class="f-row"><span class="lb">Session</span><span class="cl">:</span><span class="vl">{{ $session }}</span></div>
                    @endif
                </div>
                <div class="f-mid">
                    <div class="f-left">
                        @if($dob !== '')
                            <div class="f-row"><span class="lb">Date of Birth</span><span class="cl">:</span><span class="vl">{{ $dob }}</span></div>
                        @endif
                        @if(trim((string) $student->blood_group) !== '')
                            <div class="f-row"><span class="lb">Blood Group</span><span class="cl">:</span><span class="vl">{{ $student->blood_group }}</span></div>
                        @endif
                        <div class="f-sign">
                            <img src="{{ asset('images/idcard/principal_sign.png') }}" alt="">
                            <div class="role">Principal</div>
                        </div>
                    </div>
                    @if($student->qr_data_uri)
                        <div class="f-qr"><img src="{{ $student->qr_data_uri }}" alt="QR"></div>
                    @endif
                </div>
            </div>

            {{-- BACK --}}
            <div class="card" style="--accent: {{ $accent }};">
                <div class="b-top">
                    <img class="clogo" src="{{ asset('images/idcard/college_logo.jpg') }}" alt="">
                </div>
                <div class="b-contact">
                    Cumilla Sadar South, Cumilla<br>
                    Web: lalmaigc.edu.bd<br>
                    E-mail: lalmaicollege1969@gmail.com<br>
                    lalmai_college@yahoo.com<br>
                    principallgc2026@gmail.com<br>
                    Mob: 01309-105746
                </div>
                <div class="b-box">
                    <div class="ttl">Personal Details</div>
                    @if($father !== '')
                        <div class="b-row"><span class="lb">Father's Name</span><span class="cl">:</span><span class="vl">{{ $father }}</span></div>
                    @endif
                    @if($mother !== '')
                        <div class="b-row"><span class="lb">Mother's Name</span><span class="cl">:</span><span class="vl">{{ $mother }}</span></div>
                    @endif
                    @if($address !== '')
                        <div class="b-row"><span class="lb">Permanent Address</span><span class="cl">:</span><span class="vl">{{ $address }}</span></div>
                    @endif
                    @if($mobile !== '')
                        <div class="b-row"><span class="lb">Mobile No</span><span class="cl">:</span><span class="vl">{{ $mobile }}</span></div>
                    @endif
                    @if(trim((string) $student->email) !== '')
                        <div class="b-row"><span class="lb">E-mail</span><span class="cl">:</span><span class="vl">{{ $student->email }}</span></div>
                    @endif
                </div>
                @if($expiry !== '')
                    <div class="b-expiry">Expiry Date : {{ $expiry }}</div>
                @endif
                <div class="b-found">If it is found, please inform the College Office</div>
                <div class="b-strip"></div>
            </div>
        @endforeach
    @else
        <p style="background:#fff;padding:20px;">No student selected.</p>
    @endif
    </div>

    <script>
    /* Auto-fit: if a card's content (e.g. a long address) overflows its fixed
       height, shrink the detail-row font-size just enough to fit. Resets inline
       styles first each run so repeated calls (load + print) never compound. */
    (function () {
        function shrinkToFit(card) {
            var rows = card.querySelectorAll('.f-row, .b-row');
            if (!rows.length) return;

            // reset any previous auto-fit so we always measure from the design size
            rows.forEach(function (r) { r.style.fontSize = ''; r.style.lineHeight = ''; });

            var base = [];
            rows.forEach(function (r) { base.push(parseFloat(window.getComputedStyle(r).fontSize)); });

            var scale = 1, guard = 0;
            // step down to at most 60% of the original size
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
        if (document.readyState === 'complete') { fitAll(); }
        else { window.addEventListener('load', fitAll); }
        window.addEventListener('beforeprint', fitAll);
    })();
    </script>
</body>
</html>
