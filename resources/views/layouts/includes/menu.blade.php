<div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse ace-save-state hidden-print">
    <script type="text/javascript">
        try {
            ace.settings.loadState('sidebar')
        } catch (e) {}
    </script>

    {{-- <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
            <a class="btn btn-success" href="{{route('dashboard')}}" title="Dashboard">
                <i class="ace-icon fa fa-signal"></i>
            </a>

            <a class="btn btn-info" href="{{route('account.fees.quick-receive')}}">
                <i class="ace-icon fa fa-calculator"></i>
            </a>

            <a class="btn btn-warning" href="{{route('user')}}">
                <i class="ace-icon fa fa-users"></i>
            </a>

            <a class="btn btn-danger" href="{{route('setting.general')}}">
                <i class="ace-icon fa fa-cogs"></i>
            </a>


        </div>

        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
            <span class="btn btn-success"></span>

            <span class="btn btn-info"></span>

            <span class="btn btn-warning"></span>

            <span class="btn btn-danger"></span>
        </div>
    </div> --}}
    <!-- /.sidebar-shortcuts -->

    <ul class="nav nav-list hidden-print">
        {{-- Dashboard --}}
        <li class="{!! request()->is('dashboard') ? 'active' : '' !!}">
            <a href="{{ route('dashboard') }}">
                <span class="menu-icon"><i class="fa fa-tachometer"></i></span>
                <span class="menu-text"> {{ __('dashboard.name') }} </span>
            </a>
        </li>

        {{-- Web Portal --}}
        @ability('super-admin', 'web-cms')
            @if (isset($generalSetting) && $generalSetting->web_cms == 1)
                <li class="{!! request()->is('web*') ? 'active open' : '' !!}  hover">
                    <a href="{{ route('web.admin.dashboard') }}">
                        <i class="menu-icon-new fa fa-globe" aria-hidden="true"></i>
                        <span class="menu-text">{{ __('web_cms.name') }} </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                </li>
            @endif
        @endability

        @ability('super-admin', 'front-office')
            @if (isset($generalSetting) && $generalSetting->front_desk == 1)
                <li class="{!! request()->is('front*') ? 'active open' : '' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new  fa fa-comment" aria-hidden="true"></i>
                        <span class="menu-text">{{ __('front_desk.name') }}</span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="hover">
                            <a href="{{ route('front.postal-exchange') }}">
                                <i class="fa fa-caret-right"></i>
                                {{ __('front_desk.child.postal_exchange') }}
                            </a>
                        </li>

                        <li class="hover">
                            <a href="{{ route('front.visitor') }}">
                                <i class="fa fa-caret-right"></i>
                                {{ __('front_desk.child.visitor_log') }}
                            </a>
                        </li>

                        {{-- <li class="hover">
                             <a href="#" class="dropdown-toggle">
                                 <i class="fa fa-caret-right"></i>
                                 Inquiry
                             </a>
                         </li> --}}
                    </ul>
                </li>
            @endif
        @endability

        {{-- Staff & Student --}}
        @ability('super-admin', 'student-staff')
            @if (isset($generalSetting) && $generalSetting->student_staff == 1)
                <li class="{!! request()->is('student*') || request()->is('staff*') ? 'active open' : '' !!}  hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new fa fa-users" aria-hidden="true"></i>
                        <span class="menu-text">{{ __('student_staff.name') }}</span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="{!! request()->is('student*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">
                                <i class="fa fa-caret-right"></i>
                                {{ __('student_staff.child.student.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('student') ? 'active' : '' !!} hover">
                                    <a href="{{ route('student') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.detail') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('student/registration') ? 'active' : '' !!} hover">
                                    <a href="{{ route('student.registration') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.registration') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('student/import') ? 'active' : '' !!} hover">
                                    <a href="{{ route('student.import') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.bulk_import') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('student/transfer') ? 'active' : '' !!} hover">
                                    <a href="{{ route('student.transfer') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.child.student.child.transfer') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('student/document') ? 'active' : '' !!} hover">
                                    <a href="{{ route('student.document') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.document_upload') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('student/note') ? 'active' : '' !!} hover">
                                    <a href="{{ route('student.note') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.notes') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('report/student*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('report.student') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.complete_records') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('guardian*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">
                                <i class="fa fa-caret-right"></i>
                                {{ __('student_staff.child.guardian.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('guardian') ? 'active' : '' !!} hover">
                                    <a href="{{ route('guardian') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.detail') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('guardian/registration') ? 'active' : '' !!} hover">
                                    <a href="{{ route('guardian.registration') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.registration') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('staff*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">
                                <i class="fa fa-caret-right"></i>
                                {{ __('student_staff.child.staff.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('staff') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('staff') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.detail') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('staff/add') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('staff.add') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.registration') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('staff/import') ? 'active' : '' !!} hover">
                                    <a href="{{ route('staff.import') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.bulk_import') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('staff/document') ? 'active' : '' !!} hover">
                                    <a href="{{ route('staff.document') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.document_upload') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('staff/note') ? 'active' : '' !!} hover">
                                    <a href="{{ route('staff.note') }}">
                                        <i class="menu-icon-new fa fa-caret-right"></i>
                                        {{ __('student_staff.common.notes') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('staff/designation') ? 'active' : '' !!} hover">
                                    <a href="{{ route('staff.designation') }}">
                                        <i class="menu-icon-new fa fa-caret-right"></i>
                                        {{ __('student_staff.child.staff.child.designation') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('report/staff*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('report.staff') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('student_staff.common.complete_records') }}

                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            @endif
        @endability

        {{-- Account --}}
        @ability('super-admin', 'account')
            @if (isset($generalSetting) && $generalSetting->account == 1)
                <li class="{!! request()->is('account/*') ? 'active open' : '' !!}  hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new fa fa-calculator" aria-hidden="true"></i>
                        <span class="menu-text"> {{ __('account.name') }}</span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="{!! request()->is('account/dashboard') ? 'active' : '' !!}  hover">
                            <a href="{{ route('account.dashboard') }}">
                                <i class="fa fa-tachometer"></i>
                                {{ __('dashboard.account') }}
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('account/fees*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                <i class="fa fa-calculator"></i> {{ __('account.child.fees.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('fees/dashboard') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('fees.dashboard') }}">
                                        <i class="fa fa-tachometer"></i>
                                        {{ __('dashboard.fees') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('account/fees') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('account.fees') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.fees.child.detail') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/fees/quick-receive') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('account.fees.quick-receive') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.fees.child.quick_receive') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/fees/collection') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.fees.collection') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.fees.child.collection') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/fees/balance') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('account.fees.balance') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.fees.child.balance') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/fees/master/add') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.fees.master.add') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.fees.child.add') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/fees/online-payment') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.fees.online-payment') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.fees.child.online_payment') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/fees/head') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('account.fees.head') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.fees.child.head') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                            </ul>
                        </li>

                        <li class="divider"></li>
                        <li class="{!! request()->is('account/report*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                <i class="fa fa-print"></i> {{ __('account.child.fee_report.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('account/report/cash-book*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.report.cash-book') }}">
                                        <i class="menu-icon-new fa fa-rupee"></i>
                                        {{ __('account.child.fee_report.child.cash_book') }}
                                    </a>
                                </li>

                                <li class="{!! request()->is('account/report/fee-collection') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.report.fee-collection') }}">
                                        <i class="menu-icon-new fa fa-calculator"></i>
                                        {{ __('account.child.fee_report.child.fee_collection') }}
                                    </a>
                                </li>

                                <li class="{!! request()->is('account/report/fee-online-payment') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.report.fee-online-payment') }}">
                                        <i class="menu-icon-new fa fa-globe"></i>
                                        {{ __('account.child.fee_report.child.online_payment') }}
                                    </a>
                                </li>

                                <li class="{!! request()->is('account/report/fee-collection-head*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.report.fee-collection-head') }}">
                                        <i class="menu-icon-new fa fa-calculator"></i>
                                        {{ __('account.child.fee_report.child.fee_collection_head') }}
                                    </a>
                                </li>

                                <li class="{!! request()->is('account/report/balance-fee*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.report.balance-fee') }}">
                                        <i class="menu-icon-new fa fa-calculator"></i>
                                        {{ __('account.child.fee_report.child.fee_balance') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="divider"></li>

                        <li class="{!! request()->is('account.transaction*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                <i class="fa fa-newspaper-o"></i> {{ __('account.child.ledger_transaction.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('account/transaction/add') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.transaction.add') }}">
                                        <i class="menu-icon-new fa fa-plus"></i>
                                        {{ __('account.child.ledger_transaction.child.transaction') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/transaction/multi-add') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.transaction.multi-add') }}">
                                        <i class="menu-icon-new fa fa-plus"></i>
                                        {{ __('account.child.ledger_transaction.child.multi') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/transaction') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.transaction') }}">
                                        <i class="menu-icon-new fa fa-list"></i>
                                        {{ __('account.child.ledger_transaction.child.detail') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/transfer') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.transfer') }}">
                                        <i class="menu-icon-new fa fa-exchange"></i>
                                        {{ __('account.child.ledger_transaction.child.transfer') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/transaction-head') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.transaction-head') }}">
                                        <i class="menu-icon-new fa fa-newspaper-o"></i>
                                        {{ __('account.child.ledger_transaction.child.transaction_head') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/transaction/account-group') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.transaction.account-group') }}">
                                        <i class="menu-icon-new fa fa-newspaper-o"></i>
                                        {{ __('account.child.ledger_transaction.child.account_groups') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('account/transaction/account-group/chart-of-account') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.transaction.account-group.chart-of-account') }}">
                                        <i class="menu-icon-new fa fa-newspaper-o"></i>
                                        {{ __('account.child.ledger_transaction.child.charts_of_account') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                            </ul>
                        </li>

                        <li class="{!! request()->is('account/bank*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                <i class="fa fa-bank"></i> {{ __('account.child.banking.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('account/bank') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.bank') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.banking.child.manage_bank_account') }}
                                    </a>
                                </li>
                                <li class="{!! request()->is('account/bank/add') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.bank.add') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.banking.child.add_new_bank') }}
                                    </a>
                                </li>

                                <li class="{!! request()->is('account/bank-transaction') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.bank-transaction') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.banking.child.transaction_detail') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/bank-transaction/add') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.bank-transaction.add') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.banking.child.new_transaction') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                            </ul>
                        </li>

                        <li class="divider"></li>

                        <li class="{!! request()->is('account/payroll*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                <i class="fa fa-user-secret"></i> {{ __('account.child.payroll.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('account/payroll') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('account.payroll') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.payroll.child.paid_detail') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/salary/payment') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.salary.payment') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.payroll.child.payment') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/payroll/master*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.payroll.master.add') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.payroll.child.add') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/payroll/balance') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('account.payroll.balance') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.payroll.child.balance') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('account/payroll/head') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('account.payroll.head') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('account.child.payroll.child.head') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                            </ul>
                        </li>
                        <li class="divider"></li>
                        <li class="{!! request()->is('account/report*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                <i class="fa fa-print"></i> {{ __('account.child.account_report.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('account/transaction-head/view*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.transaction-head.view') }}">
                                        <i class="menu-icon-new fa fa-newspaper-o"></i>
                                        {{ __('account.child.account_report.child.statement') }}
                                    </a>
                                </li>
                                <li class="{!! request()->is('account/transaction-head/balance-statement*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.transaction-head.balance-statement') }}">
                                        <i class="menu-icon-new fa fa-newspaper-o"></i>
                                        {{ __('account.child.account_report.child.balance') }}
                                    </a>
                                </li>

                                <li class="{!! request()->is('account/transaction/account-group/chart-of-account') ? 'active' : '' !!} hover">
                                    <a href="{{ route('account.transaction.account-group.chart-of-account') }}">
                                        <i class="menu-icon-new fa fa-newspaper-o"></i>
                                        {{ __('account.child.account_report.child.coa') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </li>
            @endif
        @endability

        @ability('super-admin', 'inventory')
            @if (isset($generalSetting) && $generalSetting->inventory == 1)
                <li class="{!! request()->is('inventory*') ? 'active open' : '' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new fa fa-shopping-cart"></i>
                        {{ __('inventory.name') }}
                        <b class="arrow"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="{!! request()->is('inventory/assets*') || request()->is('inventory/sem-assets*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                <i class="fa fa-store"></i> Class Assets
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('inventory/assets') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.assets') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Assets
                                    </a>
                                </li>
                                <li class="{!! request()->is('inventory/sem-assets') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.sem-assets') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Faculty/Sem Assets
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('inventory/product*') ? 'active open' : '' !!}  hover">
                            <a href="#" class="dropdown-toggle">

                                <span class="menu-text">Product/Assets</span>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('inventory/product/registration*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.product.registration') }}">
                                        <i class="fa fa-caret-right"></i>
                                        <i class="fa fa-plus"></i> Product
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('inventory/product') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.product') }}">
                                        <i class="fa fa-caret-right"></i>
                                        <i class="fa fa-list"></i> Product Detail
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('inventory/product/category*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.product.category') }}">
                                        <i class="fa fa-caret-right"></i>
                                        <i class="fa fa-list-alt"></i> Category
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                            </ul>
                        </li>

                        <li class="{!! request()->is('inventory/customer*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                Customer
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('inventory/customer') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.customer') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Customer Detail
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('inventory/customer/registration') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.customer.registration') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Registration
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('inventory/customer/document') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.customer.document') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Document Upload
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('inventory/customer/note') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.customer.note') }}">
                                        <i class="menu-icon-new fa fa-caret-right"></i>
                                        Create Notes
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('inventory/vendor*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                Vendor
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('inventory/vendor') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.vendor') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Vendor Detail
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('inventory/vendor/registration') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.vendor.registration') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Registration
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('inventory/vendor/document') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.vendor.document') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Document Upload
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('inventory/vendor/note') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.vendor.note') }}">
                                        <i class="menu-icon-new fa fa-caret-right"></i>
                                        Create Notes
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('inventory/purchase*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                Purchase
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('inventory/purchase') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.purchase') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Purchase Details
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('inventory/purchase/registration') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.purchase.registration') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Purchase Now
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('inventory/vendor/document') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.vendor.document') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Purchase Return
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('inventory/sales*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                Sales
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('inventory/vendor') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.vendor') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Sales Details
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('inventory/vendor/registration') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.vendor.registration') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Sales Now
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('inventory/vendor/document') ? 'active' : '' !!} hover">
                                    <a href="{{ route('inventory.vendor.document') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Sales Return
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        {{-- <li class="{!! request()->is('vendor')?'active':'' !!} hover">
                         <a href="{{ route('vendor') }}" >
                             <i class="fa fa-caret-right"></i>
                             Vendor Detail
                         </a>

                         <b class="arrow"></b>
                     </li>

                     <li class="{!! request()->is('vendor/registration')?'active':'' !!} hover">
                         <a href="{{ route('vendor.registration') }}">
                             <i class="fa fa-caret-right"></i>
                             Registration
                         </a>

                         <b class="arrow"></b>
                     </li>

                     <li class="{!! request()->is('customer')?'active':'' !!} hover">
                         <a href="{{ route('customer') }}" >
                             <i class="fa fa-caret-right"></i>
                             Customer Detail
                         </a>

                         <b class="arrow"></b>
                     </li>

                     <li class="{!! request()->is('customer/registration')?'active':'' !!} hover">
                         <a href="{{ route('customer.registration') }}">
                             <i class="fa fa-caret-right"></i>
                             Registration
                         </a>

                         <b class="arrow"></b>
                     </li> --}}

                    </ul>
                </li>
            @endif
        @endability

        {{-- Library --}}
        @ability('super-admin', 'library')
            @if (isset($generalSetting) && $generalSetting->library == 1)
                <li class="{!! request()->is('library*') ? 'active' : '' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new fa fa-book" aria-hidden="true"></i>
                        <span class="menu-text">
                            {{ __('library.name') }}
                        </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="{!! request()->is('library/book*') ? 'active' : '' !!} hover">
                            <a href="{{ route('library.book') }}" class="dropdown-toggle">

                                Books
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('library/member*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('library.book') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Book Detail
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('library/book/add*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('library.book.add') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Add New
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('library/book/import*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('library.book.import') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Bulk Import
                                    </a>

                                    <b class="arrow"></b>
                                </li>


                                <li class="{!! request()->is('library/book/category*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('library.book.category') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Category
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                            </ul>
                        </li>

                        <li class="{!! request()->is('library/member*') || request()->is('library/student*') || request()->is('library/staff*')
                            ? 'active'
                            : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                Members
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('library/member*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('library.member') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Membership
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('library/student*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('library.student') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Student Member
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('library/staff*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('library.staff') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Staff Member
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                            </ul>
                        </li>

                        <li class="{!! request()->is('library/request*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                Book Request
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('library/request-student*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('library.student-request') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Student Request
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('library/request-staff*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('library.staff-request') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Staff Request
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                            </ul>
                        </li>

                        <li class="{!! request()->is('library/issue-history*') ? 'active' : '' !!} hover">
                            <a href="{{ route('library.issue-history') }}">
                                <i class="menu-icon-new fa fa-history"></i>
                                Issue History
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('library/return-over*') ? 'active' : '' !!} hover">
                            <a href="{{ route('library.return-over') }}">
                                <i class="fa fa-caret-right"></i>
                                Return Period Over
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('library/circulation*') ? 'active' : '' !!}  hover">
                            <a href="{{ route('library.circulation') }}">
                                <i class="fa fa-caret-right"></i>
                                Circulation Setting
                            </a>

                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
            @endif
        @endability

        {{-- Attendance --}}
        @ability('super-admin', 'attendance')
            @if (isset($generalSetting) && $generalSetting->attendance == 1)
                <li class="{!! request()->is('attendance*') ? 'active' : '' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new fa fa-calendar" aria-hidden="true"></i>
                        <span class="menu-text">
                            {{ __('attendance.name') }}
                        </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="{!! request()->is('attendance/dashboard*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">
                            <i class="fa fa-caret-right"></i>
                                <span class="menu-text"> Attendance Dashboard</span>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">
                                <li class="{!! request()->is('attendance/dashboard/students*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.dashboard.students.index') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Students Dashboard
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('attendance/dashboard/staff*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.dashboard.staff.index') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Staff Dashboard
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>
                        <li class="{!! request()->is('attendance/live*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">
                            <i class="fa fa-caret-right"></i>
                                <span class="menu-text"> Live Attendance</span>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">
                                <li class="{!! request()->is('attendance/live/students*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.live.students') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Students Attendance
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('attendance/live/staff*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.live.staff') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Staff Attendance
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('attendance/scan*') ? 'active' : '' !!} hover">
                            <a href="{{ route('attendance.scan') }}">
                                <i class="fa fa-caret-right"></i>
                                Attendance Scanner
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="{!! request()->is('attendance/tipsoi*') ? 'active' : '' !!} hover">
                            <a href="{{ route('attendance.tipsoi.dashboard') }}">
                                <i class="fa fa-caret-right"></i>
                                Attendance Device
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('attendance/reports/students/monthly*' || 'attendance/reports/staff/monthly*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">
                                <i class="fa fa-caret-right"></i>
                                <span class="menu-text"> Monthly Report</span>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">
                                <li class="{!! request()->is('attendance/reports/students/monthly*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.reports.students.monthly') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Students Attendance
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('attendance/reports/staff/monthly*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.reports.staff.monthly') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Staff Attendance
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>
                        <li class="{!! request()->is('attendance/reports/individual*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">
                                <i class="fa fa-caret-right"></i>
                                <span class="menu-text"> Card Print</span>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">                              
                               
                                <li class="{!! request()->is('attendance/reports/individual/students*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.reports.individual.students') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Students Attendance
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                 <li class="{!! request()->is('attendance/reports/individual/staff*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.reports.individual.staff') }}">
                                        <i class="fa fa-caret-right"></i>
                                         Staff Attendance
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        {{-- <li class="hover">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-microchip"></i>
                                TIPSOI Management
                                <b class="arrow fa fa-angle-down"></b>
                            </a>
                            <b class="arrow"></b>

                            <ul class="submenu">
                                <!-- Dashboard -->
                                <li
                                    class="{{ request()->routeIs('attendance.tipsoi.dashboard') ? 'active' : '' }} hover">
                                    <a href="{{ route('attendance.tipsoi.dashboard') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Dashboard
                                    </a>
                                </li>

                                <!-- Users -->
                                <li class="hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-caret-right"></i>
                                        User Management
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.users.index') ? 'active' : '' }}">
                                            <a href="{{ route('attendance.tipsoi.users.index') }}">
                                                All Users
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.users.store') ? 'active' : '' }}">
                                            <a href="{{ route('attendance.tipsoi.users.store') }}">
                                                Add New User
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.users.photo.upload') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="showPhotoUploadModal()">
                                                Upload Photo
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Devices -->
                                <li class="hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-caret-right"></i>
                                        Device Management
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.devices.index') ? 'active' : '' }}">
                                            <a href="{{ route('attendance.tipsoi.devices.index') }}">
                                                All Devices
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.devices.sync') ? 'active' : '' }}">
                                            <a href="{{ route('attendance.tipsoi.devices.sync') }}">
                                                Sync Devices
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Attendance Logs -->
                                <li class="{{ request()->routeIs('attendance.tipsoi.logs.*') ? 'active' : '' }} hover">
                                    <a href="{{ route('attendance.tipsoi.logs.index') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Attendance Logs
                                    </a>
                                </li>

                                <!-- Access Control -->
                                <li class="hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-caret-right"></i>
                                        Access Control
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.allocations.*') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="showAllocateModal()">
                                                Allocate to Device
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.revocations.*') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="confirmRevokeAll()">
                                                Revoke Access
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.revocations.device') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="showRevokeDeviceModal()">
                                                Revoke From Device
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Synchronization -->
                                <li class="hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-caret-right"></i>
                                        Synchronization
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.sync.all') ? 'active' : '' }}">
                                            <a href="{{ route('attendance.tipsoi.sync.all') }}">
                                                Sync All Data
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.sync.single') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="showSyncSingleModal()">
                                                Sync Single Person
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.sync.status') ? 'active' : '' }}">
                                            <a href="{{ route('attendance.tipsoi.sync.status') }}">
                                                Sync Status
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Bulk Operations -->
                                <li class="hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-caret-right"></i>
                                        Bulk Operations
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.allocations.bulk') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="showBulkAllocateModal()">
                                                Bulk Allocation
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.revocations.bulk') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="showBulkRevokeModal()">
                                                Bulk Revocation
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Student Integration -->
                                <li class="hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-caret-right"></i>
                                        Student Integration
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.students.register') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="showStudentRegisterModal()">
                                                Register Student
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.students.revoke') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="showStudentRevokeModal()">
                                                Revoke Student
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.students.bulk.register') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="showBulkStudentRegisterModal()">
                                                Bulk Register
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('attendance.tipsoi.students.bulk.revoke') ? 'active' : '' }}">
                                            <a href="javascript:void(0);" onclick="showBulkStudentRevokeModal()">
                                                Bulk Revoke
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li> --}}



                        {{-- 
                        <li class="hover">
                            <a href="#" class="dropdown-toggle">

                                Student Attendance
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('attendance/student*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.student') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Regular Attendance
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('attendance/subject*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.subject') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Subject Wise Attendance
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('attendance/staff*') ? 'active' : '' !!} hover">
                            <a href="{{ route('attendance.staff') }}">
                                <i class="fa fa-caret-right"></i>
                                Staff Attendance
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('attendance/report*') ? 'active open' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">
                                Attendance Report
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                {{-- <li class="{!! request()->is('attendance/report/student*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.report.student') }}">
                                        <i class="fa fa-calendar"></i>
                                        Student Report
                                    </a>
                                </li>

                                <li class="{!! request()->is('attendance/report/staff') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance.report.staff') }}">
                                        <i class="fa fa-calendar"></i>
                                        Staff Report
                                    </a>
                                </li> 
                            </ul>
                        </li> 

                        <li class="{!! request()->is('attendance/master*') ? 'active' : '' !!} hover">
                            <a href="{{ route('attendance.master') }}">
                                <i class="fa fa-caret-right"></i>
                                Monthly Calendar
                            </a>

                            <b class="arrow"></b>
                        </li>
--}}
                    </ul>
                </li>
            @endif
        @endability

        {{-- Examination --}}
        @ability('super-admin', 'examination')
            @if (isset($generalSetting) && $generalSetting->exam == 1)
                <li class="{!! request()->is('exam*') || request()->is('mcq*') ? 'active' : '' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new fa fa-line-chart" aria-hidden="true"></i>
                        <span class="menu-text">
                            {{ __('exam.name') }}
                        </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="{!! request()->is('mcq*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                <span class="menu-text"> Online - MCQ Exam</span>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">
                                <li class="hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-caret-right"></i>
                                        Question Bank
                                        <b class="arrow"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li class="{!! request()->is('mcq/question/index*') ? 'active' : '' !!} hover">
                                            <a href="{{ route('mcq.question.index') }}">
                                                <i class="fa fa-caret-right"></i>
                                                Question
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                        <li class="{!! request()->is('mcq/question/question-group*') ? 'active' : '' !!} hover">
                                            <a href="{{ route('mcq.question.question-group') }}">
                                                <i class="fa fa-caret-right"></i>
                                                Group
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                        <li class="{!! request()->is('mcq/question/question-level*') ? 'active' : '' !!} hover">
                                            <a href="{{ route('mcq.question.question-level') }}">
                                                <i class="fa fa-caret-right"></i>
                                                Level
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                    </ul>
                                </li>

                                <li class="{!! request()->is('mcq/exam/exam-instruction*') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('mcq.exam.exam-instruction') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Instruction
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('mcq/exam') ? 'active' : '' !!} hover">
                                    <a href="{{ route('mcq.exam.index') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Online Exam
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('exam*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                <span class="menu-text"> Offline - Manual Exam</span>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">
                                <li class="{!! request()->is('exam/dashboard*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('exam.dashboard') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Exam Dashboard
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('exam/schedule*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('exam.schedule') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Schedule Exam
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('exam/mark-ledger') ? 'active' : '' !!}  hover">
                                    <a href="{{ route('exam.mark-ledger') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Mark Ledger
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('exam') ? 'active' : '' !!} hover">
                                    <a href="{{ route('exam') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Exams Head
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('exam/admit-card*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('exam.admit-card') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Admit Card
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('exam/routine*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('exam.routine') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Routine/Schedule
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('exam/mark-sheet*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('exam.mark-sheet') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Grade/Mark/Ledger Sheet
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>


                    </ul>
                </li>
            @endif
        @endability

        {{-- Certificate --}}
        @ability('super-admin', 'certificate')
            @if (isset($generalSetting) && $generalSetting->certificate == 1)
                <li class="{!! request()->is('certificate*') ? 'active' : '' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new fa fa-certificate" aria-hidden="true"></i>
                        <span class="menu-text">
                            {{ __('certificate.name') }}
                        </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="{!! request()->is('certificate/issue') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.issue') }}">
                                <i class="fa fa-caret-right"></i>
                                Issue Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/attendance*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.attendance') }}">
                                <i class="fa fa-caret-right"></i>
                                Attendance Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/transfer*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.transfer') }}">
                                <i class="fa fa-caret-right"></i>
                                Transfer Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/character*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.character') }}">
                                <i class="fa fa-caret-right"></i>
                                Character Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/bonafide*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.bonafide') }}">
                                <i class="fa fa-caret-right"></i>
                                Bonafide Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/course-completion*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.course-completion') }}">
                                <i class="fa fa-caret-right"></i>
                                Course Completion Cer.
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/nirgam-utara*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.nirgam-utara') }}">
                                <i class="fa fa-caret-right"></i>
                                Nirgam Utara
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/provisional*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.provisional') }}">
                                <i class="fa fa-caret-right"></i>
                                Provisional Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/testimonial*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.testimonial') }}">
                                <i class="fa fa-caret-right"></i>
                                Testimonial Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/moi*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.moi') }}">
                                <i class="fa fa-caret-right"></i>
                                MOI Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/transcript*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.transcript') }}">
                                <i class="fa fa-caret-right"></i>
                                Transcript Certificate
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/issue-history*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.issue-history') }}">
                                <i class="menu-icon-new fa fa-history"></i>
                                Issue History
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('certificate/generate*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.generate') }}">
                                <i class="fa fa-caret-right"></i>
                                Custom Print
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="{!! request()->is('certificate/template*') ? 'active' : '' !!} hover">
                            <a href="{{ route('certificate.template') }}">
                                <i class="fa fa-caret-right"></i>
                                Certificate Template
                            </a>
                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
            @endif
        @endability

        {{-- Hostel --}}
        @ability('super-admin', 'hostel')
            @if (isset($generalSetting) && $generalSetting->hostel == 1)
                <li class="{!! request()->is('hostel*') ? 'active' : '' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new  fa fa-bed" aria-hidden="true"></i>
                        <span class="menu-text"> {{ __('hostel.name') }} </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="{!! request()->is('hostel/resident*') ? 'active' : '' !!} hover">
                            <a href="{{ route('hostel.resident') }}" class="dropdown-toggle">

                                Resident
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('hostel/resident') ? 'active' : '' !!} hover">
                                    <a href="{{ route('hostel.resident') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Detail
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('hostel/resident/add') ? 'active' : '' !!} hover">
                                    <a href="{{ route('hostel.resident.add') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Registration
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('hostel/resident/history') ? 'active' : '' !!} hover">
                                    <a href="{{ route('hostel.resident.history') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Occupant History
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                            </ul>
                        </li>
                        <li class="{!! request()->is('hostel') || request()->is('hostel/room-type') ? 'active' : '' !!} hover">
                            <a href="{{ route('hostel') }}" class="dropdown-toggle">

                                Hostel
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">

                                <li class="{!! request()->is('hostel*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('hostel') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Hostel
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('hostel/room-type*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('hostel.room-type') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Room Type
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('hostel/food*') ? 'active' : '' !!} hover">
                            <a href="{{ route('hostel') }}" class="dropdown-toggle">

                                Food & Meal
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('hostel/food*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('hostel.food') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Meal Schedule
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('hostel/food/eating-time*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('hostel.food.eating-time') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Eating Time
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('hostel/food/category*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('hostel.food.category') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Food Category
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('hostel/food/item*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('hostel.food.item') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Food Item
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            @endif
        @endability

        {{-- Transport --}}
        @ability('super-admin', 'transport')
            @if (isset($generalSetting) && $generalSetting->transport == 1)
                <li class="{!! request()->is('transport*') ? 'active' : '' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new  fa fa-bus" aria-hidden="true"></i>
                        <span class="menu-text"> {{ __('transport.name') }} </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="{!! request()->is('transport/user*') ? 'active' : '' !!} hover">
                            <a href="{{ route('transport.user') }}" class="dropdown-toggle">

                                Traveller/User
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('transport/user') ? 'active' : '' !!} hover">
                                    <a href="{{ route('transport.user') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Detail
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('transport/user/add') ? 'active' : '' !!} hover">
                                    <a href="{{ route('transport.user.add') }}">
                                        <i class="fa fa-caret-right"></i>
                                        Registration
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('transport/user/history') ? 'active' : '' !!} hover">
                                    <a href="{{ route('transport.user.history') }}">
                                        <i class="fa fa-caret-right"></i>
                                        User History
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                            </ul>
                        </li>
                        <li class="{!! request()->is('transport/route*') ? 'active' : '' !!} hover">
                            <a href="{{ route('transport.route') }}">
                                <i class="fa fa-caret-right"></i>
                                Route
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="{!! request()->is('transport/vehicle*') ? 'active' : '' !!} hover">
                            <a href="{{ route('transport.vehicle') }}">
                                <i class="fa fa-caret-right"></i>
                                Vehicle
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            @endif
        @endability

        {{-- More --}}
        {{-- @ability('super-admin','assignment,download,meeting')
             <li class="hover">
                 <a href="#" class="dropdown-toggle">
                     <i class="menu-icon-new  fa fa-th-list" aria-hidden="true"></i>
                     <span class="menu-text"> More </span>

                     <b class="arrow fa fa-angle-down"></b>
                 </a>
                 <b class="arrow"></b>
                 <ul class="submenu">
                     @ability('super-admin','assignment')
                         @if (isset($generalSetting) && $generalSetting->assignment == 1)
                             <li class="{!! request()->is('assignment')?'active':'' !!} hover">
                             <a href="{{ route('assignment') }}">
                                 <i class="fa fa-caret-right"></i>
                                 Assignment
                             </a>
                             <b class="arrow"></b>
                         </li>
                         @endif
                     @endability

                     @ability('super-admin','download')
                         @if (isset($generalSetting) && $generalSetting->upload_download == 1)
                             <li class="{!! request()->is('download*')?'active':'' !!} hover">
                             <a href="{{ route('download') }}">
                                 <i class="fa fa-caret-right"></i>
                                 Upload & Download
                                 <b class="arrow"></b>
                             </a>
                         </li>
                         @endif
                     @endability

                     @ability('super-admin','meeting')
                     @if (isset($generalSetting) && $generalSetting->meeting == 1)
                         <li class="{!! request()->is('meeting*')?'active':'' !!} hover">
                             <a href="{{ route('meeting') }}">
                                 <i class="fa fa-caret-right"></i>
                                 Meeting - Remote Class
                                 <b class="arrow"></b>
                             </a>
                         </li>
                     @endif
                     @endability
                 </ul>
             </li>
         @endability --}}

        @ability('super-admin', 'assignment')
            @if (isset($generalSetting) && $generalSetting->assignment == 1)
                <li class="{!! request()->is('assignment') ? 'active' : '' !!} hover">
                    <a href="{{ route('assignment') }}">
                        <i class="menu-icon-new fa fa-tasks"></i>
                        {{ __('assignment.name') }}
                    </a>
                    <b class="arrow"></b>
                </li>
            @endif
        @endability

        @ability('super-admin', 'application')
            @if (isset($generalSetting) && $generalSetting->application == 1)
                <li class="{!! request()->is('application*') ? 'active' : '' !!} hover">
                    <a href="{{ route('application.index') }}">
                        <i class="menu-icon-new fa fa-file"></i>
                        {{ __('application.name') }}
                    </a>
                    <b class="arrow"></b>
                </li>
            @endif
        @endability

        @ability('super-admin', 'download')
            @if (isset($generalSetting) && $generalSetting->upload_download == 1)
                <li class="{!! request()->is('download*') ? 'active' : '' !!} hover">
                    <a href="{{ route('download') }}">
                        <i class="menu-icon-new fa fa-download"></i>
                        {{ __('upload_download.name') }}
                        <b class="arrow"></b>
                    </a>
                </li>
            @endif
        @endability

        @ability('super-admin', 'meeting')
            @if (isset($generalSetting) && $generalSetting->meeting == 1)
                <li class="{!! request()->is('meeting*') ? 'active' : '' !!} hover">
                    <a href="{{ route('meeting') }}">
                        <i class="menu-icon-new fa fa-video-camera"></i>
                        {{ __('meeting.name') }}
                        <b class="arrow"></b>
                    </a>
                </li>
            @endif
        @endability


        {{-- Reports --}}
        @ability('super-admin', 'report')
            {{-- <li class="{!! request()->is('report*')?'active':'' !!} hover">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon-new fa fa-bar-chart"  aria-hidden="true"></i>
                <span class="menu-text"> Report Links</span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="{!! request()->is('report/student*')?'active':'' !!} hover">
                    <a href="{{ route('report.student') }}">
                        <i class="fa fa-caret-right"></i>
                        Student Detail
                    </a>

                    <b class="arrow"></b>
                </li>

                <li class="{!! request()->is('report/staff*')?'active':'' !!} hover">
                    <a href="{{ route('report.staff') }}">
                        <i class="fa fa-caret-right"></i>
                        Staff Detail
                    </a>

                    <b class="arrow"></b>
                </li>
                @ability('super-admin','fees-index')
                <li class="{!! request()->is('account/fees')?'active':'' !!} hover">
                    <a href="{{ route('account.fees') }}">
                        <i class="fa fa-caret-right"></i>
                        Fees Statement
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','fees-balance')
                <li class="{!! request()->is('account/fees/balance')?'active':'' !!} hover">
                    <a href="{{ route('account.fees.balance') }}">
                        <i class="fa fa-caret-right"></i>
                        Balance Fees
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','payroll-balance')
                <li class="{!! request()->is('account/payroll/balance')?'active':'' !!} hover">
                    <a href="{{ route('account.payroll.balance') }}">
                        <i class="fa fa-caret-right"></i>
                        Balance Salary
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','transaction-head-index')
                <li class="{!! request()->is('account/transaction-head')?'active':'' !!} hover">
                    <a href="{{ route('account.transaction-head') }}">
                        <i class="fa fa-caret-right"></i>
                        Ledger
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','transaction-index')
                <li class="{!! request()->is('account/transaction')?'active':'' !!} hover">
                    <a href="{{ route('account.transaction') }}">
                        <i class="fa fa-caret-right"></i>
                        Transactions
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','bank-index')
                <li class="{!! request()->is('account/bank')?'active':'' !!} hover">
                    <a href="{{ route('account.bank') }}">
                        <i class="fa fa-caret-right"></i>
                        Bank Statement
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','library-issue-history')
                <li class="{!! request()->is('library/issue-history')?'active':'' !!} hover">
                    <a href="{{ route('library.issue-history') }}">
                        <i class="fa fa-caret-right"></i>
                        Library Issue History
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','library-return-over')
                <li class="{!! request()->is('library/return-over')?'active':'' !!} hover">
                    <a href="{{ route('library.return-over') }}">
                        <i class="fa fa-caret-right"></i>
                        Book Return Period Over
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','student-attendance-index')
                <li class="{!! request()->is('attendance/student')?'active':'' !!} hover">
                    <a href="{{ route('attendance.student') }}">
                        <i class="fa fa-caret-right"></i>
                        Student Attendance
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','staff-attendance-index')
                <li class="{!! request()->is('attendance/staff')?'active':'' !!} hover">
                    <a href="{{ route('attendance.staff') }}">
                        <i class="fa fa-caret-right"></i>
                        Staff Attendance
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','resident-history')
                <li class="{!! request()->is('hostel/resident/history')?'active':'' !!} hover">
                    <a href="{{ route('hostel.resident.history') }}">
                        <i class="fa fa-caret-right"></i>
                        Hostel History
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','transport-user-history')
                <li class="{!! request()->is('transport/user/history')?'active':'' !!} hover">
                    <a href="{{ route('transport.user.history') }}">
                        <i class="fa fa-caret-right"></i>
                        Transport History
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability
            </ul>
        </li> --}}
        @endability

        {{-- Info Center --}}
        @ability('super-admin', 'alert-center')
            @if (isset($generalSetting) && $generalSetting->alert == 1)
                <li class="{!! request()->is('info*') ? 'active' : '' !!} hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new fa fa-bullhorn" aria-hidden="true"></i>
                        <span class="menu-text">
                            {{ __('alert.name') }}
                        </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="{!! request()->is('info/notice*') ? 'active' : '' !!} hover">
                            <a href="{{ route('info.notice') }}">
                                <i class="fa fa-caret-right"></i>
                                User Notice
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="{!! request()->is('info/smsemail*') ? 'active' : '' !!} hover">
                            <a href="{{ route('info.smsemail') }}">
                                <i class="fa fa-caret-right"></i>
                                SMS / E-mail
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            @endif
        @endability

        {{-- Academic --}}
        @ability('super-admin', 'academic')
            @if (isset($generalSetting) && $generalSetting->academic == 1)
                <li class="hover">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon-new  fa fa-graduation-cap" aria-hidden="true"></i>
                        <span class="menu-text">
                            {{ __('academic.name') }}
                        </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="{!! request()->is('academic/dashboard') ? 'active' : '' !!}  hover">
                            <a href="{{ route('academic.dashboard') }}">
                                <i class="fa fa-tachometer"></i>
                                {{ __('dashboard.academic') }}
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="{!! request()->is('faculty*') || request()->is('semester*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                {{ __('academic.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('department-head*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('department-head') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.academic_level.child.department_head') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('department*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('department') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.academic_level.child.department') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>



                                <li class="{!! request()->is('faculty*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('faculty') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.academic_level.child.faculty') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('semester*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('semester') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.academic_level.child.semester') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('student-batch*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('student-batch') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.academic_level.child.batch') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('routine*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('routine.index') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.academic_level.child.routine') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>
                        <li class="{!! request()->is('grading*') || request()->is('subject*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                {{ __('academic.child.grading_subject.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('subject*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('subject') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.grading_subject.child.subject') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('grading*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('grading') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.grading_subject.child.grading') }}
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('year*') || request()->is('month*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                {{ __('academic.child.year_month_day.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('year*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('year') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.year_month_day.child.year') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('month*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('month') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.year_month_day.child.month') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('day*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('day') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.year_month_day.child.day') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('*status') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                {{ __('academic.child.status_setting.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('student-status*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('student-status') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.status_setting.child.student_status') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('attendance-status*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('attendance-status') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.status_setting.child.attendance_status') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('book-status*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('book-status') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.status_setting.child.book_status') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('bed-status*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('bed-status') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.status_setting.child.hostel_bed_status') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('customer-status*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('customer-status') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.status_setting.child.customer_status') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="{!! request()->is('dynamic*') ? 'active' : '' !!} hover">
                            <a href="#" class="dropdown-toggle">

                                {{ __('academic.child.dynamic_gallery.name') }}
                                <b class="arrow"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                                <li class="{!! request()->is('dynamic/placement*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('dynamic.placement') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.dynamic_gallery.child.placement') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('dynamic/scholarship*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('dynamic.scholarship') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.dynamic_gallery.child.scholarship') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('dynamic/annexure*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('dynamic.annexure') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.dynamic_gallery.child.annexure') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="{!! request()->is('dynamic/academic-info-level*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('dynamic.academic-info-level') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.dynamic_gallery.child.academic_info_level') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('dynamic/degree*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('dynamic.degree') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.dynamic_gallery.child.degree') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('dynamic/state*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('dynamic.state') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.dynamic_gallery.child.state') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="{!! request()->is('dynamic/application-type*') ? 'active' : '' !!} hover">
                                    <a href="{{ route('dynamic.application-type') }}">
                                        <i class="fa fa-caret-right"></i>
                                        {{ __('academic.child.dynamic_gallery.child.application_type') }}
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            @endif
        @endability



        {{-- Help --}}
        @ability('super-admin', 'admin')
            @if (isset($generalSetting) && $generalSetting->help == 1)
                <li class="hover">
                    <a href="#" target="_blank" class="dropdown-toggle">
                        <i class="menu-icon-new  fa fa-question" aria-hidden="true"></i>
                        <span class="menu-text">
                            {{ __('help.name') }}
                        </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="hover">
                            <a href="{{ route('license-info') }}" target="_self">
                                <i class="fa fa-caret-right"></i>
                                License Info
                            </a>
                        </li>
                        <li class="hover">
                            <a href="http://unlimitededufirm.com/demo-detail" target="_blank">
                                <i class="fa fa-caret-right"></i>
                                Test Demo
                            </a>
                        </li>
                        <li class="hover">
                            <a href="https://www.youtube.com/watch?v=2jgA9WY8IzQ&list=PLCtD_CGPAQJ2zSk5cDUkkfWGdtMGsF9n0"
                                target="_blank">
                                <i class="fa fa-caret-right"></i>
                                Video Tutorial
                            </a>
                        </li>
                        <li class="hover">
                            <a href="http://docs.unlimitededufirm.com" target="_blank">
                                <i class="fa fa-caret-right"></i>
                                Documentation
                            </a>
                        </li>
                        <li class="hover">
                            <a href="https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988"
                                target="_blank">
                                <i class="fa fa-caret-right"></i>
                                Buy New License
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        @endability
    </ul><!-- /.nav-list -->
</div>
