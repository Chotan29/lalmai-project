<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('mcq/exam/index*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.exam.index') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Detail</a>
        <a class="{!! request()->is('mcq/exam/add*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('mcq.exam.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add</a>
    </div>
    <hr class="hr-6 ">
</div>
