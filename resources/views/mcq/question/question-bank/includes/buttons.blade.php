<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('mcq/question/index*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.question.index') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Detail</a>
        <a class="{!! request()->is('mcq/question/add*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.question.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add</a>
        <a class="{!! request()->is('mcq/question/import*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.question.import') }}"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Bulk Import</a>
        <a class="{!! request()->is('mcq/question/question-group*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.question.question-group') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Group</a>
        <a class="{!! request()->is('mcq/question/question-level*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.question.question-level') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Level</a>
    </div>
    <hr class="hr-6 ">
</div>
