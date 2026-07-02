@if(isset($data['annexures']))
    <div class="row">
        <div class="col-md-12 padding-5">
            <div class="label label-warning arrowed-in arrowed-right arrowed">
                Details of Annexure & photo copy :
            </div>
            <hr class="hr-8">
        @foreach($data['annexures'] as $annexure)
            <div class="col-md-6">
                <label>
                    {!! Form::checkbox('annexure[]', $annexure->id, false, ['class' => 'ace']) !!}
                    <span class="lbl"> {{ $annexure->title}} </span>
                </label>
                <hr class="hr-2">
            </div>
        @endforeach
        </div>

    </div>
@endif
@if($data['registration_setting']->rules_status == '1')
    <div class="row">
        <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="#registraton-rule" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                        <i class="ace-icon fa fa-arrow-down"></i> Registration Rules
                    </a>
                </div>

                <div class="panel-collapse collapse" id="registraton-rule" style="height: 0px;">
                    <div class="panel-body">
                        {!! $data['registration_setting']->rules !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@if($data['registration_setting']->agreement_status == '1')
    <div class="row">
        <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="#registraton-agreement" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                        <i class="ace-icon fa fa-arrow-down"></i> Registration Agreement
                    </a>
                </div>

                <div class="panel-collapse collapse" id="registraton-agreement" style="height: 0px;">
                    <div class="panel-body">
                        {!! $data['registration_setting']->agreement !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

