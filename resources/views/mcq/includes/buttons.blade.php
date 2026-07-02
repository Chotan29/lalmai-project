<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('mcq/exam*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.exam.index') }}"><i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;Exam</a>
        <a class="{!! request()->is('mcq/exam/exam-instruction*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.exam.exam-instruction') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Instruction</a>
        <a class="{!! request()->is('mcq/question*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.question.index') }}"><i class="fa fa-question" aria-hidden="true"></i>&nbsp;Question</a>
        <a class="{!! request()->is('mcq/question/question-group*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.question.question-group') }}"><i class="fa fa-question" aria-hidden="true"></i>&nbsp;Group</a>
        <a class="{!! request()->is('mcq/question/question-level*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.question.question-level') }}"><i class="fa fa-question" aria-hidden="true"></i>&nbsp;Level</a>
    </div>
</div>
<hr class="hr-6">