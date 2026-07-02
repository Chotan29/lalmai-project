@extends('layouts.master')

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; }

.admit-toolbar {
    max-width: 210mm;
    margin: 14px auto 10px;
    text-align: right;
}

.admit-page-wrap {
    width: 210mm;
    height: 297mm;
    margin: 0 auto 20px;
    background: #fff;
    font-family: 'Poppins', sans-serif;
    color: #1a2b3c;
    position: relative;
    border: 2px solid #0f3e6a;
    padding: 3px;
    display: flex;
    flex-direction: column;
}
.admit-page-inner {
    width: 100%;
    flex: 1;
    border: 1px solid #3a6a9b;
    padding: 0;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.admit-page-inner > * { position: relative; z-index: 1; flex-shrink: 0; }
.admit-wm {
    position: absolute;
    top: 55%; left: 50%;
    transform: translate(-50%, -50%);
    width: 45%;
    opacity: 0.08;
    pointer-events: none;
    z-index: 0;
    display: block;
}

.admit-top-strip { background: #0f3e6a; height: 7px; }

.admit-title-band {
    background: #f0f5fb;
    border-bottom: 1px solid #cddaeb;
    padding: 5px 14mm;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.atb-left  { font-size: 10.5px; color: #2d5580; font-weight: 600; letter-spacing: .3px; }
.atb-right { font-size: 10.5px; color: #2d5580; font-weight: 600; letter-spacing: .3px; text-align: right; }
.atb-center {
    font-size: 11px; font-weight: 800; letter-spacing: 3px;
    text-transform: uppercase; color: #0f3e6a; text-align: center;
    padding: 2px 16px;
    border-left: 1px solid #cddaeb;
    border-right: 1px solid #cddaeb;
}

.admit-header {
    display: grid;
    grid-template-columns: 26mm 1fr 30mm;
    gap: 8px;
    align-items: center;
    padding: 12px 14mm;
    border-bottom: 1.5px solid #d0dcea;
}
.admit-logo-wrap { display: flex; align-items: center; justify-content: center; }
.admit-logo { width: 100%; max-height: 25mm; object-fit: contain; }

.admit-headline { text-align: center; line-height: 1.3; }
.ahl-republic { font-size: 10px; color: #2d5580; letter-spacing: .2px; margin-bottom: 1px; }
.ahl-college  { font-size: 18px; font-weight: 800; color: #0f3e6a; letter-spacing: .4px; font-family: 'Merriweather', serif; margin: 2px 0; white-space: nowrap; }
.ahl-web      { font-size: 10px; color: #4a7aaa; margin-bottom: 5px; }
.ahl-admit    {
    display: inline-block;
    font-size: 14px; font-weight: 800; letter-spacing: 3px;
    text-transform: uppercase; color: #fff;
    background: #0f3e6a;
    padding: 3px 18px; border-radius: 2px; margin: 3px 0;
}
.ahl-exam { font-size: 12px; font-weight: 700; color: #1d3550; margin-top: 3px; }
.ahl-exam-sub { font-size: 10.5px; font-weight: 400; color: #4a6a8a; }

.admit-photo-wrap { display: flex; flex-direction: column; align-items: center; gap: 3px; }
.admit-photo-box {
    width: 28mm; height: 33mm;
    border: 1.5px solid #3a6a9b; border-radius: 3px;
    overflow: hidden; display: flex; align-items: center; justify-content: center;
    background: #f4f8fc;
}
.admit-photo-box img { width: 100%; height: 100%; object-fit: cover; }
.admit-photo-label { font-size: 9px; color: #5a7a9a; font-weight: 600; letter-spacing: .5px; text-transform: uppercase; }

.admit-info {
    margin: 10px 14mm 0;
    border: 1px solid #cddaeb;
    border-radius: 4px;
    overflow: hidden;
}
.admit-info-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-bottom: 1px solid #e0eaf5;
}
.admit-info-row:last-child { border-bottom: none; }
.admit-info-cell {
    display: grid;
    grid-template-columns: 110px 1fr;
    align-items: center;
    padding: 7px 10px;
    gap: 4px;
}
.admit-info-cell:first-child { border-right: 1px solid #e0eaf5; }
.info-label { font-weight: 700; color: #0f3e6a; font-size: 11px; white-space: nowrap; }
.info-value { font-weight: 500; color: #1a2b3c; font-size: 12px; }
.info-value.caps { text-transform: uppercase; font-weight: 600; }
.admit-info-row:nth-child(odd) .admit-info-cell { background: #f8fbff; }

.admit-subjects {
    margin: 10px 14mm 0;
    border: 1px solid #cddaeb;
    border-radius: 4px;
    overflow: hidden;
}
.subject-head-bar {
    background: #0f3e6a; color: #fff;
    text-align: center; font-size: 12px; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase; padding: 5px 10px;
}
.subject-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    padding: 6px 8px 4px;
    gap: 2px 16px;
}
.subj-item {
    display: flex; align-items: baseline; gap: 6px;
    padding: 3px 4px; border-radius: 3px; font-size: 11.5px;
}
.subj-item:nth-child(odd) { background: #f8fbff; }
.subj-code {
    min-width: 48px; font-weight: 800; color: #0f3e6a;
    font-size: 12px; text-align: right; flex-shrink: 0;
}
.subj-name { font-weight: 500; color: #1a2b3c; text-transform: uppercase; font-size: 11px; }
.optional-bar {
    border-top: 1px dashed #cddaeb; background: #f0f5fb;
    padding: 5px 12px; font-size: 11px;
    display: flex; align-items: center; gap: 6px;
}
.opt-label { font-weight: 800; color: #0f3e6a; white-space: nowrap; }
.opt-value  { font-weight: 600; color: #2d5580; text-transform: uppercase; }

.admit-sig-row {
    display: flex; justify-content: space-between; align-items: flex-end;
    padding: 0 14mm; margin-top: 20px; margin-bottom: 4px;
}
.sig-box { text-align: center; min-width: 130px; }
.sig-line {
    border-top: 1px solid #1a3550; padding-top: 5px; margin-top: 32px;
    font-size: 11px; font-weight: 700; color: #0f3e6a; letter-spacing: .5px;
}
.sig-barcode { text-align: center; }
.sig-barcode canvas, .sig-barcode img { display: block; margin: 0 auto; width: 68px !important; height: 68px !important; }
.sig-barcode-label {
    font-size: 9px; font-weight: 700; color: #1a2b3c;
    letter-spacing: 1px; margin-top: 3px;
}

.niyom-wrap {
    margin: 10px 14mm 8mm;
    border: 1.5px solid #1a4f7a; border-radius: 4px; overflow: hidden;
    font-family: 'SolaimanLipi','Kalpurush','Hind Siliguri','Noto Sans Bengali',Arial,sans-serif;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.niyom-head {
    background: #0f3e6a; color: #fff;
    text-align: center; font-size: 13px; font-weight: 700;
    padding: 7px 12px; letter-spacing: .3px;
    flex-shrink: 0;
}
.niyom-body { padding: 10px 14px; flex: 1; }
.niyom-list { list-style: none; margin: 0; padding: 0; }
.niyom-list li {
    font-size: 11.5px; line-height: 1.75;
    color: #1a2b3c; margin-bottom: 1px;
    display: flex; gap: 5px;
}
.niyom-num { font-weight: 800; min-width: 26px; color: #0f3e6a; flex-shrink: 0; }

.admit-bottom-strip {
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 5px; background: #0f3e6a;
}

@page {
    size: A4;
    margin: 0;
}

@media print {
    body { margin: 0; padding: 0; }

    .navbar-fixed-top, .navbar-fixed-bottom,
    .main-sidebar, .sidebar, .nav-list,
    .breadcrumbs, .page-header,
    .hidden-print, .admit-toolbar,
    .ace-settings-container, .btn-scroll-up,
    footer.main-footer { display: none !important; }

    .main-container, .main-content, .main-content-inner, .page-content {
        margin: 0 !important; padding: 0 !important;
        border: 0 !important; width: 100% !important;
        min-height: auto !important; float: none !important;
        background: transparent !important;
    }

    .admit-page-wrap {
        width: 100%; height: 297mm;
        margin: 0; border: 2px solid #0f3e6a;
        page-break-after: always;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .admit-page-inner {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .admit-top-strip,
    .admit-bottom-strip {
        background: #0f3e6a !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .ahl-admit {
        background: #0f3e6a !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .subject-head-bar {
        background: #0f3e6a !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .niyom-head {
        background: #0f3e6a !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .admit-info-row:nth-child(odd) .admit-info-cell {
        background: #f8fbff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .subj-item:nth-child(odd) {
        background: #f8fbff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .admit-photo-box {
        background: #f4f8fc !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .admit-wm { display: block !important; opacity: 0.08 !important; z-index: 0 !important; }
    .niyom-wrap { page-break-inside: avoid; }
}
</style>
@endsection

@section('content')

@if($data['student'] && $data['student']->count() > 0)

    <div class="admit-toolbar hidden-print">
        <button class="btn btn-primary" onclick="window.print(); return false;">
            <i class="fa fa-print"></i> Print Admit Card(s)
        </button>
    </div>

    @foreach($data['student'] as $student)

    @php
        $admitSubjects    = $student->admit_subjects ?? collect();
        $optionalSubjects = $admitSubjects->filter(function ($s) {
            return strtolower(trim((string) ($s->subject_type ?? ''))) === 'optional';
        })->values();
        $regularSubjects  = $admitSubjects->reject(function ($s) {
            return strtolower(trim((string) ($s->subject_type ?? ''))) === 'optional';
        })->values();

        $hasLogo  = isset($generalSetting->logo) && $generalSetting->logo;
        $logoPath = $hasLogo ? asset('images/setting/general/' . $generalSetting->logo) : '';
        $logoSrc  = $hasLogo ? asset('images' . DIRECTORY_SEPARATOR . 'setting' . DIRECTORY_SEPARATOR . 'general' . DIRECTORY_SEPARATOR . $generalSetting->logo) : '';
        $photoSrc = $student->student_image ? asset('images' . DIRECTORY_SEPARATOR . 'studentProfile' . DIRECTORY_SEPARATOR . $student->student_image) : '';
        $fullName = strtoupper(trim($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name));
        $dob      = $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : 'N/A';
    @endphp

    <div class="admit-page-wrap">
        @if($hasLogo)
        <img class="admit-wm" src="{{ $logoPath }}" alt="">
        @endif
        <div class="admit-page-inner">

            <div class="admit-top-strip"></div>



            <div class="admit-header">

                <div class="admit-logo-wrap">
                    @if($hasLogo)
                        <img class="admit-logo" src="{{ $logoSrc }}" alt="Logo">
                    @endif
                </div>

                <div class="admit-headline">
                    <div class="ahl-republic">The People's Republic of Bangladesh</div>
                    <div class="ahl-college">Lalmai Government College, Cumilla</div>
                    <div class="ahl-web">Web: https://lalmaigc.edu.bd</div>
                    <div><span class="ahl-admit">ADMIT CARD</span></div>
                    <div class="ahl-exam">
                        HSC Examination ({{ $data['exam_name'] ?? 'Eleventh Final Exam' }}) -2026
                    </div>
                </div>

                <div class="admit-photo-wrap">
                    <div class="admit-photo-box">
                        @if($photoSrc)
                            <img src="{{ $photoSrc }}" alt="Photo">
                        @endif
                    </div>
                </div>

            </div>

            <div class="admit-info">
                <div class="admit-info-row">
                    <div class="admit-info-cell">
                        <span class="info-label">Group:</span>
                        <span class="info-value">{{ ViewHelper::getFacultyTitle($student->faculty) ?: 'N/A' }}</span>
                    </div>
                    <div class="admit-info-cell">
                        <span class="info-label">Roll No:</span>
                        <span class="info-value">{{ $student->reg_no ?: 'N/A' }}</span>
                    </div>
                </div>
                <div class="admit-info-row">
                    <div class="admit-info-cell">
                        <span class="info-label">Name:</span>
                        <span class="info-value caps">{{ $fullName }}</span>
                    </div>
                    <div class="admit-info-cell">
                        <span class="info-label">Board Reg:</span>
                        <span class="info-value">{{ $student->university_reg ?: 'N/A' }}</span>
                    </div>
                </div>
                <div class="admit-info-row">
                    <div class="admit-info-cell">
                        <span class="info-label">Date of Birth:</span>
                        <span class="info-value">{{ $dob }}</span>
                    </div>
                    <div class="admit-info-cell">
                        <span class="info-label">Session:</span>
                        <span class="info-value">{{ ViewHelper::getStudentBatchById($student->batch) ?: 'N/A' }}</span>
                    </div>
                </div>
                <div class="admit-info-row">
                    <div class="admit-info-cell">
                        <span class="info-label">Gender:</span>
                        <span class="info-value">{{ $student->gender ?: 'N/A' }}</span>
                    </div>
                    <div class="admit-info-cell">
                        <span class="info-label">Subject Type:</span>
                        <span class="info-value">Compulsory &amp; Optional</span>
                    </div>
                </div>
            </div>

            <div class="admit-subjects">
                <div class="subject-head-bar">Code &amp; Subject</div>

                @if($regularSubjects->count() > 0)
                    <div class="subject-grid">
                        @foreach($regularSubjects as $subj)
                            <div class="subj-item">
                                <span class="subj-code">{{ $subj->code ?: '-' }}</span>
                                <span class="subj-name">{{ $subj->title }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="padding:8px 12px;font-size:12px;color:#6a8aaa">No compulsory subjects found.</div>
                @endif

                @if($optionalSubjects->count() > 0)
                    <div class="subject-grid">
                        @foreach($optionalSubjects as $subj)
                            <div class="subj-item">
                                <span class="subj-code">{{ preg_replace('/^O-/i', '', $subj->code ?: '-') }}</span>
                                <span class="subj-name">{{ trim(preg_replace('/\(optional\)/i', '', $subj->title)) }} (Optional)</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="admit-sig-row">
                <div class="sig-box">
                    <div class="sig-line">Examinee's Signature</div>
                </div>
                <div class="sig-barcode">
                    <div class="admit-qr" data-roll="Roll: {{ $student->reg_no }}&#10;Name: {{ $fullName }}&#10;Session: {{ ViewHelper::getStudentBatchById($student->batch) ?: 'N/A' }}&#10;College: Lalmai Govt. College, Cumilla"></div>
                </div>
                <div class="sig-box">
                    <div class="sig-line">Principal</div>
                </div>
            </div>

            <div class="niyom-wrap">
                <div class="niyom-head">পরীক্ষার্থীর অবশ্যই পালনীয় নিয়মাবলী</div>
                <div class="niyom-body">
                    <ul class="niyom-list">
                        <li><span class="niyom-num">০১।</span> কলেজ প্রদত্ত পরীক্ষার রুটিন অনুযায়ী নির্ধারিত তারিখ ও সময়ে পরীক্ষার হলে যথাযথভাবে উপস্থিত হতে হবে ;</li>
                        <li><span class="niyom-num">০২।</span> পরীক্ষার দিন পরীক্ষাকেন্দ্রে পরীক্ষা শুরু হওয়ার কমপক্ষে ৩০ (ত্রিশ) মিনিট পূর্বে পরীক্ষার্থীকে নিজ আসন গ্রহণ করতে হবে ;</li>
                        <li><span class="niyom-num">০৩।</span> পরীক্ষা শুরু হওয়ার ৩০ (ত্রিশ) মিনিট পরে কোনো পরীক্ষার্থীকে পরীক্ষাকক্ষে প্রবেশ করতে দেওয়া হবে না এবং পরীক্ষার কমপক্ষে ১ (এক) ঘণ্টা পূর্বে কোনো পরীক্ষার্থী পরীক্ষাকক্ষ ত্যাগ করতে পারবে না ;</li>
                        <li><span class="niyom-num">০৪।</span> পরীক্ষার হলে প্রবেশের সময় কোনো প্রকার <strong>ইলেকট্রনিক ডিভাইস (মোবাইল ফোন ও অন্যান্য ডিভাইস)</strong> সঙ্গে আনা সম্পূর্ণ নিষিদ্ধ ;</li>
                        <li><span class="niyom-num">০৫।</span> পরীক্ষার হলে কোনো ব্যাগ, বই, নোট বা অননুমোদিত কোনো কাগজপত্র সঙ্গে আনা সম্পূর্ণ নিষিদ্ধ ;</li>
                        <li><span class="niyom-num">০৬।</span> পরীক্ষার্থীরা অন্য কোনো পরীক্ষার্থীর সাথে কথা বলতে পারবে না। কোনো বই বা ইলেকট্রনিক ডিভাইস ব্যবহার করলে পরীক্ষা বাতিল বলে গণ্য হবে ;</li>
                        <li><span class="niyom-num">০৭।</span> পরীক্ষার্থীকে উত্তরপত্র পাওয়ার সাথে সাথে রোল নম্বর, রেজিস্ট্রেশন নম্বরসহ সকল তথ্য সঠিকভাবে পূরণ করতে হবে ;</li>
                        <li><span class="niyom-num">০৮।</span> উত্তরপত্রে উত্তর লেখার পর কোনো অংশ কাটাকাটি বা ঘষামাজা করা যাবে না ;</li>
                        <li><span class="niyom-num">০৯।</span> উত্তরপত্র সম্পূর্ণ লেখার পর পরীক্ষার হল ত্যাগের পূর্বে অবশ্যই পরীক্ষকের নিকট জমা দিতে হবে ;</li>
                        <li><span class="niyom-num">১০।</span> <strong>প্রবেশপত্র ও কলেজের নির্ধারিত ইউনিফর্ম</strong> ছাড়া কোনো পরীক্ষার্থীকে পরীক্ষার হলে প্রবেশ করতে দেওয়া হবে না ;</li>
                        <li><span class="niyom-num">১১।</span> উত্তরপত্রের যেকোনো জায়গায় পরীক্ষার্থীর নাম, ঠিকানা বা যেকোনো অপ্রাসঙ্গিক তথ্য লেখা যাবে না ;</li>
                        <li><span class="niyom-num">১২।</span> পরীক্ষার হলে অসদাচরণ করলে কলেজের শৃঙ্খলা বিধি অনুযায়ী শাস্তিমূলক ব্যবস্থা গ্রহণ করা হবে।</li>
                    </ul>
                </div>
            </div>

            <div class="admit-bottom-strip"></div>
        </div>
    </div>

    @endforeach

@endif

@endsection

@section('js')
@include('includes.scripts.print_script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.admit-qr').forEach(function (el) {
        var val = el.getAttribute('data-roll');
        if (val) {
            new QRCode(el, {
                text: val,
                width: 68,
                height: 68,
                colorDark: '#0f3e6a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    });
});
</script>
@endsection
