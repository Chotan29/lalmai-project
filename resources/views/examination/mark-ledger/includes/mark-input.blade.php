{{-- One mark box (theory / MCQ / practical).

     $field   - form field name, e.g. obtain_mark_practical[]
     $value   - current value (null for a fresh row)
     $limit   - full mark for this component; 0 means the subject has no such component
     $label   - human name used in the tooltip, e.g. "practical"
     $locked  - row is owned by another teacher

     IMPORTANT: never render these as `disabled`. The controller pairs the mark arrays with
     students_id[] BY POSITION, and a disabled input is not submitted at all - that would
     shift every following mark onto the wrong student. `readonly` still submits. --}}

@php
    $limit = (float) ($limit ?? 0);
    $locked = $locked ?? false;
    $noComponent = $limit <= 0;

    $attributes = ['class' => 'form-control border-form', 'min' => '0', 'step' => 'any'];

    if ($noComponent) {
        /* No max="0" here: the browser would answer "Value must be less than or equal to 0"
           and silently refuse to submit the whole form. Say what is actually wrong instead. */
        $attributes['readonly'] = 'readonly';
        $attributes['placeholder'] = '—';
        $attributes['style'] = 'background:#f2f2f2; color:#999;';
        $attributes['title'] = 'This subject has no '.$label.' marks, so nothing can be entered here. '
            .'To take '.$label.' marks, set the '.$label.' full mark for this subject in Academic → Subject.';
    } else {
        $attributes['max'] = $limit;
        $attributes['title'] = 'Full '.$label.' mark: '.($limit + 0);

        if ($locked) {
            $attributes['readonly'] = 'readonly';
            $attributes['style'] = 'background:#eee;';
        }
    }
@endphp

{!! Form::number($field, $value, $attributes) !!}
