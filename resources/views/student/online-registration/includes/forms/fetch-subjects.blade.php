@if(isset($subjects))
    @php
        $selectedSubjectIds = collect($selectedSubjectIds ?? [])->map(function ($id) {
            return (int) $id;
        })->all();

        /*Which of the student's subjects is the 4th (optional) one. Comes from
          student_subject.subject_role; empty for a brand new form.*/
        $selectedOptionalIds = collect($selectedOptionalIds ?? [])->map(function ($id) {
            return (int) $id;
        })->all();

        /*Admin-configured per-semester limits (passed from controller; safe fallbacks)*/
        $maxCompulsory = isset($maxCompulsory) ? (int) $maxCompulsory : 6;
        $maxOptional = isset($maxOptional) ? (int) $maxOptional : 1;
        $maxAllowedSubjects = isset($totalMax) ? (int) $totalMax : ($maxCompulsory + $maxOptional);

        $isOptionalType = function ($subject) {
            return strtolower(trim((string) ($subject->subject_type ?? $subject->sub_type ?? ''))) === 'optional';
        };

        /*A subject can be offered BOTH ways: compulsory for most students and the 4th
          subject for others (Higher Mathematics, Biology ...). Such a subject is flagged
          allow_as_optional on the semester mapping and appears in both columns.*/
        $allowsOptional = function ($subject) {
            return (int) ($subject->allow_as_optional ?? 0) === 1;
        };

        $optionalSubjects = $subjects->filter(function ($subject) use ($isOptionalType, $allowsOptional) {
            return $isOptionalType($subject) || $allowsOptional($subject);
        })->values();

        $compulsorySubjects = $subjects->reject($isOptionalType)->values();
    @endphp

    <div class="subject-selection-header">
        <div class="subject-selection-title">Select Subjects</div>
        <div class="subject-selection-limit">Maximum {{$maxAllowedSubjects}} subjects (Compulsory up to {{$maxCompulsory}}, Optional up to {{$maxOptional}}).</div>
    </div>

    <div class="subject-structure-note">
        <span><strong>Left:</strong> Compulsory subjects</span>
        <span><strong>Right:</strong> Optional (4th) subject</span>
        <span><strong>Total:</strong> {{$maxAllowedSubjects}} subjects maximum</span>
    </div>

    <input type="hidden" name="max_subjects_count" value="{{$maxAllowedSubjects}}">
    <input type="hidden" name="max_compulsory_count" value="{{$maxCompulsory}}">
    <input type="hidden" name="max_optional_count" value="{{$maxOptional}}">
    {{-- Filled on submit with the ids ticked in the Optional column, so the server knows
         which subject is this student's 4th subject even when the same subject is also
         available as a compulsory one. --}}
    <input type="hidden" name="optional_subject_ids" id="optional_subject_ids" value="{{ implode(',', $selectedOptionalIds) }}">

    <div class="row subject-selection-grid">
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="subject-group-card subject-group-card--compulsory">
                <div class="subject-group-card__head">
                    <h4>Compulsory Subjects</h4>
                    <span class="subject-group-tag">LEFT</span>
                    <span class="subject-count">{{$compulsorySubjects->count()}}</span>
                </div>

                <div class="subject-group-card__body">
                    @if($compulsorySubjects->count())
                        @foreach($compulsorySubjects as $subject)
                            @php
                                $subjectId = (int) ($subject->subject_id ?? $subject->id ?? 0);
                                $subjectTitle = $subject->subject_title ?? $subject->title ?? 'Unknown Subject';
                                $checkedHere = in_array($subjectId, $selectedSubjectIds, true)
                                    && !in_array($subjectId, $selectedOptionalIds, true);
                            @endphp
                            <label class="subject-option-row">
                                {!! Form::checkbox('subject[]', $subjectId, $checkedHere, ['class' => 'ace', 'data-subject-type' => 'compulsory', 'data-subject-id' => $subjectId]) !!}
                                <span class="lbl">{{ $subjectTitle }}</span>
                            </label>
                        @endforeach
                    @else
                        <p class="subject-empty-state">No compulsory subjects found for this semester.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="subject-group-card subject-group-card--optional">
                <div class="subject-group-card__head">
                    <h4>Optional (4th) Subject</h4>
                    <span class="subject-group-tag subject-group-tag--optional">RIGHT</span>
                    <span class="subject-count">{{$optionalSubjects->count()}}</span>
                </div>

                <div class="subject-group-card__body">
                    @if($optionalSubjects->count())
                        @foreach($optionalSubjects as $subject)
                            @php
                                $subjectId = (int) ($subject->subject_id ?? $subject->id ?? 0);
                                $subjectTitle = $subject->subject_title ?? $subject->title ?? 'Unknown Subject';
                                $bothWays = !$isOptionalType($subject) && $allowsOptional($subject);
                                $checkedHere = $bothWays
                                    ? in_array($subjectId, $selectedOptionalIds, true)
                                    : in_array($subjectId, $selectedSubjectIds, true);
                            @endphp
                            <label class="subject-option-row">
                                {!! Form::checkbox('subject[]', $subjectId, $checkedHere, ['class' => 'ace', 'data-subject-type' => 'optional', 'data-subject-id' => $subjectId]) !!}
                                <span class="lbl">{{ $subjectTitle }}@if($bothWays) <small class="text-muted">(as 4th subject)</small>@endif</span>
                            </label>
                        @endforeach
                    @else
                        <p class="subject-empty-state">No optional subjects found for this semester.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var wrapper = document.getElementById('subjects_wrapper') || document;

            /*The same subject can appear in both columns. Ticking it on one side unticks
              the other, so a student can never be both compulsory and 4th in one paper.*/
            wrapper.addEventListener('change', function (e) {
                var el = e.target;
                if (!el || el.name !== 'subject[]' || !el.checked) { return; }

                var id = el.getAttribute('data-subject-id');
                var side = el.getAttribute('data-subject-type');
                if (!id) { return; }

                var twins = wrapper.querySelectorAll('input[name="subject[]"][data-subject-id="' + id + '"]');
                for (var i = 0; i < twins.length; i++) {
                    if (twins[i] !== el && twins[i].getAttribute('data-subject-type') !== side) {
                        twins[i].checked = false;
                    }
                }
                syncOptionalIds();
            }, true);

            function syncOptionalIds() {
                var field = document.getElementById('optional_subject_ids');
                if (!field) { return; }

                var ids = [];
                var boxes = wrapper.querySelectorAll('input[name="subject[]"][data-subject-type="optional"]');
                for (var i = 0; i < boxes.length; i++) {
                    if (boxes[i].checked) { ids.push(boxes[i].value); }
                }
                field.value = ids.join(',');
            }

            syncOptionalIds();

            var form = (document.getElementById('optional_subject_ids') || {}).form;
            if (form) { form.addEventListener('submit', syncOptionalIds, true); }
        })();
    </script>
@endif
