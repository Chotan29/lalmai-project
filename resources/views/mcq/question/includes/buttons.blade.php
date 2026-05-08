<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('mcq/exam/index*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.exam.index') }}"><i class="fa fa-pencil-square" aria-hidden="true"></i>&nbsp;Question</a>
        <a class="{!! request()->is('mcq/question/index*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.question.index') }}"><i class="fa fa-pencil-square" aria-hidden="true"></i>&nbsp;Question</a>
        <a class="{!! request()->is('mcq/question/question-level')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.question.question-level') }}"><i class="fa fa-share-alt" aria-hidden="true"></i>&nbsp;Level</a>
    </div>
</div>
<hr class="hr-6">