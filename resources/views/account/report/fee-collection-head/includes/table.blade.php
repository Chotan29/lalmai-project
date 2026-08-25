@if(isset($data['fee_group_tag']) && $data['fee_group_tag'] =='fee_group')
    @include($view_path.'.includes.fee-group-table-data')
@elseif(isset($data['fee_head_tag']) && $data['fee_head_tag'] =='fee_head')
    @include($view_path.'.includes.daily-fee-head-table-data')
{{-- isset, like the two branches above it. The controller only sets tag when a filter combination
     it recognises came in; anything else - a stray parameter, a bookmarked url missing its fee
     head - left it unset and this line brought the whole page down with "Undefined index: tag"
     instead of simply showing the empty report. --}}
@elseif(isset($data['tag']) && ($data['tag'] =='daily' || $data['tag'] =='weekly' || $data['tag'] =='monthly' || $data['tag'] =='yearly'))
    @include($view_path.'.includes.daily-table-data')

@elseif(isset($data['print_head']))
    @include($view_path.'.includes.tabe-data')
@else
@endif