 @if(isset($subjects))
    <div class="row">
        <div class="col-md-12 padding-5">
            <div class="label label-warning arrowed-in arrowed-right arrowed">
                Check Only {{$numOfSubject}} Box :
            </div>
            <input type="hidden" name="max_subjects_count" value="{{$numOfSubject}}">
            <hr class="hr-8">
            @foreach($subjects as $subject)
                <div class="col-md-4">
                    <label>
                        {!! Form::checkbox('subject[]', $subject->subject_id, false, ['class' => 'ace']) !!}
                        <span class="lbl"> {{ $subject->subject_title}} </span>
                    </label>
                    <hr class="hr-2">
                </div>
            @endforeach
        </div>
    </div>
@endif