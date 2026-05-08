<style>
    blockquote {
        border-left: 7px solid #2bc4ee;
        padding-left: 20;
    }
</style>
<div id=":ru" class="a3s aXjCH m162811f50c84aefd"><u></u>
    <div style="margin:0px;padding:0px" bgcolor="#d8d6d6">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#d8d6d6">
            <tbody>
            <tr>
                <td height="60" align="center" valign="middle" style="font-family:'Open Sans',Arial,sans-serif;font-size:13px;line-height:20px;color:#313538">
                    {{--Watch the quick video to set up your first SMS integration.--}}
                </td>
            </tr>
            <tr>
                <td align="center" valign="top">
                    <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF" align="center" class="m_-2010080597923446268em_main_table" style="table-layout:fixed">
                        <tbody>
                        <tr>
                            <td align="center">
                                <table width="88%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                                    <tbody>
                                    <tr>
                                        <td height="35" style="height:35px">&nbsp;</td>
                                    </tr>
                                    {{--  @if(isset($generalSetting->logo))
                                      <tr>
                                            <td align="center">
                                                <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" width="216" style="display:block;font-family:Arial,sans-serif;font-size:18px;line-height:25px;font-weight:bold;color:#163a66;text-align:center;max-width:216px;width:100px;border-radius: 50%;" border="0" alt="{{ $data['subject'] }}" class="CToWUd">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="30" style="height:30px">&nbsp;</td>
                                        </tr>
                                    @endif--}}
                                    <tr>
                                        <td align="center" class="m_-2010080597923446268copy-main" style="font-family:'Open Sans',Arial,sans-serif;font-size:27px;line-height:31px;color:#313538; border-bottom:5px #2bc4ee solid; padding-bottom: 10px; ">
                                                        <span>
                                                            {{$data['subject']}}
                                                        </span>
                                            {{--<hr style="background: #2bc4ee;">--}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="30" style="height:30px">&nbsp;</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        {{--<tr>
                            <td align="center" valign="top" bgcolor="#2bc4ee" style="border-bottom:3px solid #2bc4ee">
                                <a id="m_-2010080597923446268ct0_1" href="#" target="_blank"><img src="" width="600" style="display:block;font-family:Arial,sans-serif;font-size:18px;line-height:25px;font-weight:bold;color:#163a66;text-align:center;max-width:600px;width:100%" border="0" alt="" class="CToWUd"></a>
                            </td>
                        </tr>--}}
                        <tr>
                            <td align="center">
                                <table width="88%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                                    <tbody>
                                    {{--<tr>
                                        <td height="35" style="height:35px">&nbsp;</td>
                                    </tr>--}}
                                    <tr>
                                        <td class="m_-2010080597923446268copy-main" style="font-family:'Open Sans',Arial,sans-serif;font-size:14px;line-height:21px;color:#313538">
                                            <table class="header-row" width="378" cellspacing="0" cellpadding="0" border="0" style="table-layout: fixed;"><tbody><tr><td class="header-row-td" width="378" style="font-family: Arial, sans-serif; font-weight: normal; line-height: 19px; color: #478fca; margin: 0px; font-size: 18px; padding-bottom: 10px; padding-top: 15px;" valign="top" align="left">Thank you for Subscribe!</td></tr></tbody></table>
                                            <div style="font-family: Arial, sans-serif; line-height: 20px; color: #444444; font-size: 13px;">
                                                <b style="color: #777777;">We are excited to you have join {{ $generalSetting->institute}} Newsletters</b>.
                                                Click on confirm button to confirm you subscription.
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="30" style="height:30px">&nbsp;</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @if($generalSetting->website)
                            <tr>
                                <td height="70" align="center" valign="middle" bgcolor="#ebebeb">
                                    <table width="150" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                                        <tbody>
                                        <tr>
                                            <td height="40" align="center" valign="middle" bgcolor="#2bc4ee" style="border-radius:20px">
                                                <a style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#ffffff;text-decoration:none;font-weight:bold" href="{{ route('web.subscribers.active', ['id' => $data['id']]) }}">Confirm</a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td align="center">
                                <table width="88%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
                                    <tbody>
                                    <tr>
                                        <td height="30" style="height:30px">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="font-family:'Open Sans',Arial,sans-serif;font-size:14px;line-height:26px;color:#313538">
                                            Yours,<br>
                                            <span style="color:#2bc4ee;font-weight:bold;font-size:18px;line-height:25px">{{ $generalSetting->institute }}</span>
                                            <br>
                                            @if($generalSetting->address)
                                                {{$generalSetting->address}}
                                            @endif
                                            <br>
                                            @if($generalSetting->phone)
                                                <span style="color:#2bc4ee">•</span>
                                                {{ $generalSetting->phone }}
                                            @endif

                                            <br>
                                            @if($generalSetting->email)
                                                <a style="color:#313538;text-decoration:none" href="mailto:{{ $generalSetting->email }}" target="_blank">{{$generalSetting->email}}</a>
                                            @endif
                                            @if($generalSetting->website)
                                                <span style="color:#2bc4ee">•</span>
                                                <a style="color:#313538;text-decoration:none" id="m_-2010080597923446268ct2_0" href="{{$generalSetting->website}}" target="_blank" data-saferedirecturl="#">
                                                    {{ $generalSetting->website }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="10" style="height:10px">&nbsp;</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%" align="center" bgcolor="#f5f5f5" style="font-family: Arial, sans-serif; line-height: 24px; color: #bbbbbb; font-size: 13px; font-weight: normal; text-align: center; padding: 9px; border-width: 1px 0px 0px; border-style: solid; border-color: #e3e3e3; background-color: #f5f5f5;" valign="top">
                                <a href="#" style="color: #428bca; text-decoration: none; background-color: transparent;">
                                    <img src="{{ asset('images/setting/general/'. $generalSetting->logo) }}" alt="{{ $generalSetting->site_title }}" ><br>
                                    {{ $generalSetting->site_title}}
                                </a>
                                {{--<p>{{ isset($generalSetting->address)?$generalSetting->address:'' }}</p>
                                <p>{{ isset($generalSetting->phone)?$generalSetting->phone:'' }}, {{ isset($generalSetting->email)?$generalSetting->email:'' }}</p>--}}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div></div>