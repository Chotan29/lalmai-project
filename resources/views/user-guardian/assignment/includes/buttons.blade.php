<div class="clearfix hidden-print " >
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('user-guardian/students/assignment')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('user-guardian.students.assignment',['id'=>Crypt::encryptString($data['student_id'])]) }}"><i class="fa fa-tasks" aria-hidden="true"></i>&nbsp;Assignment Detail</a>
    </div>
</div>
<div class="form-horizontal filter-form">
                            @include($view_path.'.issue-history.includes.search_form')