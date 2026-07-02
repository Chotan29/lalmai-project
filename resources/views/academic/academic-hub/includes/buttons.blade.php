<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('department-head*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('department-head') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.academic_level.child.department_head')}}</a>
        <a class="{!! request()->is('department*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('department') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.academic_level.child.department')}}</a>
        <a class="{!! request()->is('faculty')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('faculty') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.academic_level.child.faculty')}}</a>
        <a class="{!! request()->is('semester*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('semester') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.academic_level.child.semester')}}</a>
        <a class="{!! request()->is('student-batch*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('student-batch') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.academic_level.child.batch')}}</a>
        <a class="{!! request()->is('subject*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('subject') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.grading_subject.child.subject')}}</a>
        <a class="{!! request()->is('grading*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('grading') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.grading_subject.child.grading')}}</a>
    </div>
</div>
<hr class="hr-6">