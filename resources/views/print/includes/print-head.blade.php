@if($data['certificate_template']->print_institute_head == 1)
    <div class="institute-detail">
        @include('print.includes.institution-detail')
    </div>
@else
    <div class="institute-detail">
        <div class="hidden-print">
            @include('print.includes.institution-detail')
        </div>
    </div>
@endif