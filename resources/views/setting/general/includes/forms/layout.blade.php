<style>
    .table{
        margin-top: 55px;
    }
    .table-bordered, td, th {
        border-radius: 0!important;
        vertical-align: top;
    }
    input[type=radio].ace+.lbl::before {
        border-radius: 100%;
        font-size: 100px;
        font-family: FontAwesome;
        text-shadow: 0 0 1px #32A3CE;
        line-height: 15px;
        height: 85px;
        min-width: 88px;
        margin-top: 54px;
        padding-top: 33px;
    }

</style>
<table class="text-center table-bordered">
    <tr>
        <td rowspan="2" width="50%">
            @include('setting.general.includes.forms.module')
        </td>
        <td style="margin-top: 50px;">
            <label>
                {!! Form::radio('nav_layout', 0, true, ['class' => 'ace']) !!}
                <span class="lbl"><h2>Navigation on TOP </h2> <img alt="Top Nav" src="{{ asset('assets/images/layout/nav_top.png') }}"  width="50%"  /></span>
            </label>
        </td>
    </tr>
    <tr>
        <td>
            <label>
                {!! Form::radio('nav_layout', 1, false, ['class' => 'ace']) !!}
                <span class="lbl"><h2>Navigation on SIDE </h2>  <img alt="Side Nav" src="{{ asset('assets/images/layout/nav_side.png') }}"  width="50%" /></span>
            </label>
        </td>
    </tr>
</table>

