@if(isset($subjects))
    @php
        $maxAllowedSubjects = min((int) $numOfSubject, 7);

        $optionalSubjects = $subjects->filter(function ($subject) {
            return strtolower(trim($subject->subject_type ?? '')) === 'optional';
        })->values();

        $compulsorySubjects = $subjects->reject(function ($subject) {
            return strtolower(trim($subject->subject_type ?? '')) === 'optional';
        })->values();
    @endphp

    <div class="subject-selection-header">
        <div class="subject-selection-title">Select Subjects</div>
        <div class="subject-selection-limit">Maximum {{$maxAllowedSubjects}} subjects (Compulsory up to 6, Optional up to 1).</div>
    </div>

    <div class="subject-structure-note">
        <span><strong>Left:</strong> Compulsory subjects</span>
        <span><strong>Right:</strong> Optional subject</span>
        <span><strong>Total:</strong> 7 subjects maximum</span>
    </div>

    <input type="hidden" name="max_subjects_count" value="{{$maxAllowedSubjects}}">

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
                            <label class="subject-option-row">
                                {!! Form::checkbox('subject[]', $subject->subject_id, false, ['class' => 'ace', 'data-subject-type' => 'compulsory']) !!}
                                <span class="lbl">{{ $subject->subject_title }}</span>
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
                    <h4>Optional Subjects</h4>
                    <span class="subject-group-tag subject-group-tag--optional">RIGHT</span>
                    <span class="subject-count">{{$optionalSubjects->count()}}</span>
                </div>

                <div class="subject-group-card__body">
                    @if($optionalSubjects->count())
                        @foreach($optionalSubjects as $subject)
                            <label class="subject-option-row">
                                {!! Form::checkbox('subject[]', $subject->subject_id, false, ['class' => 'ace', 'data-subject-type' => 'optional']) !!}
                                <span class="lbl">{{ $subject->subject_title }}</span>
                            </label>
                        @endforeach
                    @else
                        <p class="subject-empty-state">No optional subjects found for this semester.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif