{{-- Public result lookup. Roll alone is not enough on purpose: rolls run in sequence, so
     without the date of birth anyone could walk the whole class one number at a time. --}}
@php($gs = $data['generalSetting'] ?? null)
<!DOCTYPE html>
<html lang="en">
<head>
    @include('front.result.includes.head')
    <title>Result - {{ $gs->institute ?? 'Lalmai Govt. College' }}</title>
    @include('front.result.includes.style')
</head>
<body>
<div class="pr-wrap">
    <div class="pr-card">
        <div class="pr-head">
            @if($gs && $gs->logo && is_file(public_path('images/setting/general/'.$gs->logo)))
                <img src="{{ asset('images/setting/general/'.$gs->logo) }}" alt="Logo">
            @endif
            <h4>{{ $gs->institute ?? 'Lalmai Govt. College' }}</h4>
            <div class="sub">Examination Result</div>
        </div>

        @if(!empty($data['error']))
            <div class="pr-alert bad">{{ $data['error'] }}</div>
        @endif

        @if(count($data['exam_groups']) === 0)
            <div class="pr-alert info">
                No result has been published yet. Please check back after the college announces one.
            </div>
        @else
            <form method="POST" action="{{ route('public-result.find') }}" autocomplete="off">
                {{ csrf_field() }}

                <div class="mb-3">
                    <label class="pr-label" for="exam_group">Examination</label>
                    <select name="exam_group" id="exam_group" class="form-select" required>
                        <option value="">Select the examination</option>
                        @foreach($data['exam_groups'] as $key => $label)
                            <option value="{{ $key }}" {{ ($data['old_group'] ?? '') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="pr-label" for="reg_no">Roll / Registration Number</label>
                    <input type="text" name="reg_no" id="reg_no" class="form-control"
                           value="{{ $data['old_roll'] ?? '' }}" maxlength="30"
                           placeholder="e.g. 273001" required>
                </div>

                <div class="mb-3">
                    <label class="pr-label" for="date_of_birth">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" required>
                    <div class="form-text">Exactly as it is recorded on your registration form.</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 pr-btn">See Result</button>
            </form>

            <div class="pr-note">
                If your roll and date of birth are correct but nothing appears, please contact the
                college office - the result may not have been published for your class yet.
            </div>
        @endif
    </div>

    {{-- The department sheets the college has released, the way a merit list is put up on the
         notice board. Only released departments appear; nothing here is guessable from a URL. --}}
    @if(!empty($data['departments']))
        <div class="pr-sheets">
            <div class="pr-sheets-title">Published Tabulation Sheets</div>

            @foreach($data['departments'] as $department => $sheets)
                @foreach($sheets as $sheet)
                    <a class="pr-sheet" href="{{ $sheet['url'] }}">
                        <span class="pr-dept">{{ $department }}</span>
                        <span class="pr-sheet-main">
                            <span class="t">{{ $sheet['title'] }}</span>
                            <span class="m">{{ $sheet['year'] }} &middot; {{ $sheet['students'] }} students</span>
                        </span>
                        <span class="pr-sheet-go">View &rsaquo;</span>
                    </a>
                @endforeach
            @endforeach

            <div class="pr-note">
                Only the departments the college has published appear here. To see your own
                subject-wise marks, use the form above.
            </div>
        </div>
    @endif

    <div class="pr-foot">
        {{ $gs->institute ?? 'Lalmai Govt. College' }} &middot; Cumilla Sadar South
    </div>
</div>
</body>
</html>
