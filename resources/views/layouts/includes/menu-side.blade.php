<div id="sidebar" class="sidebar responsive ace-save-state navbar-collapse hidden-print">
    <script type="text/javascript">
        try{ace.settings.loadState('sidebar')}catch(e){}
    </script>

    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
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
    </div>
    <!-- /.sidebar-shortcuts -->

    <ul class="nav nav-list hidden-print">
        {{-- Dashboard --}}
        <li class="{!! request()->is('dashboard')?'active':'' !!}">
            <a href="{{ route('dashboard') }}" >
                <span class="menu-icon"><i class="fa fa-tachometer"></i></span>
                <span class="menu-text"> Dashboard </span>
            </a>
        </li>

        {{-- Web Portal --}}
        @ability('super-admin','web-cms')
            @if( isset($generalSetting) && isset($generalSetting->web_cms) && $generalSetting->web_cms ==1)
                <li class="{!! request()->is('web*')?'active open':'' !!} ">
                    <a href="{{route('web.admin.dashboard')}}">
                        <span class="menu-icon"><i class="fa fa-globe" aria-hidden="true"></i></span>
                        <span class="menu-text">  Web CMS</span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                </li>
            @endif
        @endability

        @ability('super-admin','front-office')
        @if( isset($generalSetting) && isset($generalSetting->front_desk) && $generalSetting->front_desk ==1)
            <li class="{!! request()->is('front*')?'active open':'' !!} ">
                <a href="#" class="dropdown-toggle">
                    <span class="menu-icon"><i class="fa fa-sign-out" aria-hidden="true"></i></span>
                    <span class="menu-text">Front Desk</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>
                <ul class="submenu">
                    <li class="{!! request()->is('front/postal-exchange*')?'active open':'' !!}">
                        <a href="{{ route('front.postal-exchange') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Postal Exchange
                        </a>
                    </li>

                    <li class="{!! request()->is('front/visitor*')?'active open':'' !!}}">
                        <a href="{{ route('front.visitor') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Visitor Log
                        </a>
                    </li>

                    {{-- <li class=">
                         <a href="#" class="dropdown-toggle">
                             <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                             Inquiry
                         </a>
                     </li>--}}
                </ul>
            </li>
        @endif
        @endability

        {{-- Staff & Student --}}
        @ability('super-admin','student-staff')
        @if( isset($generalSetting) && isset($generalSetting->student_staff) && $generalSetting->student_staff ==1)
            <li class="{!! request()->is('student*')||request()->is('staff*')?'active open':'' !!} ">
                <a href="#" class="dropdown-toggle">
                    <span class="menu-icon"><i class="fa fa-users" aria-hidden="true"></i></span>
                    <span class="menu-text"> Student&Staff</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li class="{!! request()->is('student*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Student
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('student')?'active':'' !!}">
                                <a href="{{ route('student') }}" accesskey="S">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Student Detail
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('student/registration')?'active':'' !!}">
                                <a href="{{ route('student.registration') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Registration
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('student/import')?'active':'' !!}">
                                <a href="{{ route('student.import') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Bulk Import
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('student/transfer')?'active':'' !!}">
                                <a href="{{ route('student.transfer') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Transfer Student
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('student/document')?'active':'' !!}">
                                <a href="{{ route('student.document') }}"  accesskey="U">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Document Upload
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('student/note')?'active':'' !!}">
                                <a href="{{ route('student.note') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"  accesskey="N"></i></span>
                                    Create Notes
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('report/student*')?'active':'' !!}">
                                <a href="{{ route('report.student') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Student Complete Records
                                </a>

                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>

                    <li class="{!! request()->is('guardian*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Guardian
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('guardian')?'active':'' !!}">
                                <a href="{{ route('guardian') }}" accesskey="S">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Guardian Detail
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('guardian/registration')?'active':'' !!}">
                                <a href="{{ route('guardian.registration') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Registration
                                </a>

                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>

                    <li class="{!! request()->is('staff*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Staff
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('staff')?'active':'' !!} ">
                                <a href="{{ route('staff') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Staff Detail
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('staff/add')?'active':'' !!} ">
                                <a href="{{ route('staff.add') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Registration
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('staff/import')?'active':'' !!}">
                                <a href="{{ route('staff.import') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Bulk Import
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('staff/document')?'active':'' !!}">
                                <a href="{{ route('staff.document') }}"  accesskey="U">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Document Upload
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('staff/note')?'active':'' !!}">
                                <a href="{{ route('staff.note') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"  accesskey="N"></i></span>
                                    Create Notes
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('staff/designation')?'active':'' !!}">
                                <a href="{{ route('staff.designation') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"  accesskey="N"></i></span>
                                    Designations
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('report/staff*')?'active':'' !!}">
                                <a href="{{ route('report.staff') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Staff Complete Records
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
        @ability('super-admin','account')
        @if( isset($generalSetting) && isset($generalSetting->account) && $generalSetting->account ==1)
            <li class="{!! request()->is('account/*')?'active open':'' !!} ">
                <a href="#" class="dropdown-toggle">
                    <span class="menu-icon"><i class="fa fa-calculator" aria-hidden="true"></i></span>
                    <span class="menu-text"> Account</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li class="{!! request()->is('account/fees/collection')?'active':'' !!}">
                                <a href="{{ route('account.fees.collection') }}" accesskey="R">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Collect Feesdsdsd account.dashboard
                                </a>

                                <b class="arrow"></b>
                            </li>
                    <li class="{!! request()->is('account/fees*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            <i class="fa fa-calculator"></i>  Fees Collection
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('account/fees')?'active':'' !!} ">
                                <a href="{{ route('account.fees') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Receive Detail
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/fees/quick-receive')?'active':'' !!} ">
                                <a href="{{ route('account.fees.quick-receive') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Quick Receive
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/fees/collection')?'active':'' !!}">
                                <a href="{{ route('account.fees.collection') }}" accesskey="R">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Collect Fees
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/fees/balance')?'active':'' !!} ">
                                <a href="{{ route('account.fees.balance') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Balance Fees Report
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/fees/master/add')?'active':'' !!}">
                                <a href="{{ route('account.fees.master.add') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Add Fees
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/fees/online-payment')?'active':'' !!}">
                                <a href="{{ route('account.fees.online-payment') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Online Payments
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/fees/head')?'active':'' !!} ">
                                <a href="{{ route('account.fees.head') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Fees Head
                                </a>
                                <b class="arrow"></b>
                            </li>

                        </ul>
                    </li>

                    <li class="divider"></li>

                    <li class="{!! request()->is('account.transaction*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            <i class="fa fa-newspaper-o"></i> Ledger & Transaction
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('account/transaction/add')?'active':'' !!}">
                                <a href="{{ route('account.transaction.add') }}">
                                    <span class="menu-icon"><i class="fa fa-plus"></i></span>
                                    Transaction
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/transaction/multi-add')?'active':'' !!}">
                                <a href="{{ route('account.transaction.multi-add') }}">
                                    <span class="menu-icon"><i class="fa fa-plus"></i></span>
                                    Multi Transaction
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/transaction')?'active':'' !!}">
                                <a href="{{ route('account.transaction') }}" accesskey="R">
                                    <span class="menu-icon"><i class="fa fa-list"></i></span>
                                    Transaction Detail
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/transfer')?'active':'' !!}">
                                <a href="{{ route('account.transfer') }}">
                                    <span class="menu-icon"><i class="fa fa-exchange"></i></span>
                                    Acc to Acc Transfer
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/transaction-head')?'active':'' !!}">
                                <a href="{{ route('account.transaction-head') }}">
                                    <span class="menu-icon"><i class="fa fa-newspaper-o"></i></span>
                                    Ledger/Account
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/transaction/account-group')?'active':'' !!}">
                                <a href="{{ route('account.transaction.account-group') }}">
                                    <span class="menu-icon"><i class="fa fa-newspaper-o"></i></span>
                                    Account Groups
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('account/transaction/account-group/chart-of-account')?'active':'' !!}">
                                <a href="{{ route('account.transaction.account-group.chart-of-account') }}">
                                    <span class="menu-icon"><i class="fa fa-newspaper-o"></i></span>
                                    Charts of Account
                                </a>
                                <b class="arrow"></b>
                            </li>

                        </ul>
                    </li>

                    <li class="{!! request()->is('account/bank*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            <i class="fa fa-bank"></i> Separate Banking
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('account/bank')?'active':'' !!}">
                                <a href="{{ route('account.bank') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Manage Bank Accounts
                                </a>
                            </li>
                            <li class="{!! request()->is('account/bank/add')?'active':'' !!}">
                                <a href="{{ route('account.bank.add') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Add New Bank
                                </a>
                            </li>

                            <li class="{!! request()->is('account/bank-transaction')?'active':'' !!}">
                                <a href="{{ route('account.bank-transaction') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Transaction Detail
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/bank-transaction/add')?'active':'' !!}">
                                <a href="{{ route('account.bank-transaction.add') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    New Transaction
                                </a>

                                <b class="arrow"></b>
                            </li>

                        </ul>
                    </li>

                    <li class="divider"></li>

                    <li class="{!! request()->is('account/payroll*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            <i class="fa fa-user-secret"></i>  Payroll
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('account/payroll')?'active':'' !!} ">
                                <a href="{{ route('account.payroll') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Paid Detail
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/salary/payment')?'active':'' !!}">
                                <a href="{{ route('account.salary.payment') }}" accesskey="R">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Salary Pay
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/payroll/master*')?'active':'' !!}">
                                <a href="{{ route('account.payroll.master.add') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Add Payroll
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/payroll/balance')?'active':'' !!} ">
                                <a href="{{ route('account.payroll.balance') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Balance Salary Report
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('account/payroll/head')?'active':'' !!} ">
                                <a href="{{ route('account.payroll.head') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Salary Head
                                </a>

                                <b class="arrow"></b>
                            </li>

                        </ul>
                    </li>
                    <li class="divider"></li>
                    <li class="{!! request()->is('account/report*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            <i class="fa fa-print"></i> Account Report
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('account/report/cash-book*')?'active':'' !!}">
                                <a href="{{ route('account.report.cash-book') }}">
                                    <span class="menu-icon"><i class="fa fa-rupee"></i></span>
                                    Cash Book
                                </a>
                            </li>

                            <li class="{!! request()->is('account/report/fee-collection')?'active':'' !!}">
                                <a href="{{ route('account.report.fee-collection') }}">
                                    <span class="menu-icon"><i class="fa fa-calculator"></i></span>
                                    Fee Collection
                                </a>
                            </li>

                            <li class="{!! request()->is('account/report/fee-online-payment')?'active':'' !!}">
                                <a href="{{ route('account.report.fee-online-payment') }}">
                                    <span class="menu-icon"><i class="fa fa-globe"></i></span>
                                    Online Payments
                                </a>
                            </li>

                            <li class="{!! request()->is('account/report/fee-collection-head*')?'active':'' !!}">
                                <a href="{{ route('account.report.fee-collection-head') }}">
                                    <span class="menu-icon"><i class="fa fa-calculator"></i></span>
                                    Fee Collection Head
                                </a>
                            </li>

                            <li class="{!! request()->is('account/report/balance-fee*')?'active':'' !!}">
                                <a href="{{ route('account.report.balance-fee') }}">
                                    <span class="menu-icon"><i class="fa fa-calculator"></i></span>
                                    Fee Balance
                                </a>
                            </li>

                            <li class="{!! request()->is('account/transaction-head/view*')?'active':'' !!}">
                                <a href="{{ route('account.transaction-head.view') }}">
                                    <span class="menu-icon"><i class="fa fa-newspaper-o"></i></span>
                                    Statement of Ledger
                                </a>
                            </li>
                            <li class="{!! request()->is('account/transaction-head/balance-statement*')?'active':'' !!}">
                                <a href="{{ route('account.transaction-head.balance-statement') }}" accesskey="B">
                                    <span class="menu-icon"><i class="fa fa-newspaper-o"></i></span>
                                    Ledger Balance
                                </a>
                            </li>

                            <li class="{!! request()->is('account/transaction/account-group/chart-of-account')?'active':'' !!}">
                                <a href="{{ route('account.transaction.account-group.chart-of-account') }}">
                                    <span class="menu-icon"><i class="fa fa-newspaper-o"></i></span>
                                    Charts of Account
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>

                </ul>
            </li>
        @endif
        @endability

        @ability('super-admin','inventory')
        @if( isset($generalSetting) && isset($generalSetting->inventory) && $generalSetting->inventory ==1)

            <li class="{!! request()->is('inventory*') /*|| request()->is('product*') || request()->is('customer*') || request()->is('vendor*')*/ ?'active open':'' !!}">
                <a href="#" class="dropdown-toggle">
                    <span class="menu-icon"><i class="fa fa-shopping-cart"></i></span>
                    <span class="menu-text">Inventory</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    <li class="{!! request()->is('inventory/assets*') || request()->is('inventory/sem-assets*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            <i class="fa fa-store"></i> Class Assets
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('inventory/assets')?'active':'' !!}">
                                <a href="{{ route('inventory.assets') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Assets
                                </a>
                            </li>
                            <li class="{!! request()->is('inventory/sem-assets')?'active':'' !!}">
                                <a href="{{ route('inventory.sem-assets') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Faculty/Sem Assets
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{!! request()->is('inventory/product*')?'active open':'' !!} ">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            <span class="menu-text">Product/Assets</span>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('inventory/product/registration*')?'active':'' !!}">
                                <a href="{{ route('inventory.product.registration') }}" accesskey="S">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    <i class="fa fa-plus"></i> Product
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('inventory/product')?'active':'' !!}">
                                <a href="{{ route('inventory.product') }}" accesskey="S">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    <i class="fa fa-list"></i> Product Detail
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('inventory/product/category*')?'active':'' !!}">
                                <a href="{{ route('inventory.product.category') }}" accesskey="S">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    <i class="fa fa-list-alt"></i> Category
                                </a>
                                <b class="arrow"></b>
                            </li>

                        </ul>
                    </li>

                    <li class="{!! request()->is('inventory/customer*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Customer
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('inventory/customer')?'active':'' !!}">
                                <a href="{{ route('inventory.customer') }}" accesskey="S">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Customer Detail
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('inventory/customer/registration')?'active':'' !!}">
                                <a href="{{ route('inventory.customer.registration') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Registration
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('inventory/customer/document')?'active':'' !!}">
                                <a href="{{ route('inventory.customer.document') }}"  accesskey="U">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Document Upload
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('inventory/customer/note')?'active':'' !!}">
                                <a href="{{ route('inventory.customer.note') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"  accesskey="N"></i></span>
                                    Create Notes
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>

                    <li class="{!! request()->is('inventory/vendor*')?'active open':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Vendor
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('inventory/vendor')?'active':'' !!}">
                                <a href="{{ route('inventory.vendor') }}" accesskey="S">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Vendor Detail
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('inventory/vendor/registration')?'active':'' !!}">
                                <a href="{{ route('inventory.vendor.registration') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Registration
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('inventory/vendor/document')?'active':'' !!}">
                                <a href="{{ route('inventory.vendor.document') }}"  accesskey="U">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Document Upload
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('inventory/vendor/note')?'active':'' !!}">
                                <a href="{{ route('inventory.vendor.note') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"  accesskey="N"></i></span>
                                    Create Notes
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>

                    {{-- <li class="{!! request()->is('vendor')?'active':'' !!}">
                         <a href="{{ route('vendor') }}" accesskey="S">
                             <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                             Vendor Detail
                         </a>

                         <b class="arrow"></b>
                     </li>

                     <li class="{!! request()->is('vendor/registration')?'active':'' !!}">
                         <a href="{{ route('vendor.registration') }}">
                             <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                             Registration
                         </a>

                         <b class="arrow"></b>
                     </li>

                     <li class="{!! request()->is('customer')?'active':'' !!}">
                         <a href="{{ route('customer') }}" accesskey="S">
                             <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                             Customer Detail
                         </a>

                         <b class="arrow"></b>
                     </li>

                     <li class="{!! request()->is('customer/registration')?'active':'' !!}">
                         <a href="{{ route('customer.registration') }}">
                             <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                             Registration
                         </a>

                         <b class="arrow"></b>
                     </li>--}}

                </ul>
            </li>
        @endif
        @endability

        {{-- Library --}}
        @ability('super-admin','library')
        @if( isset($generalSetting) && isset($generalSetting->library) && $generalSetting->library ==1)
            <li class="{!! request()->is('library*')?'active':'' !!}">
                <a href="#" class="dropdown-toggle">
                    <span class="menu-icon"><i class="fa fa-book" aria-hidden="true"></i></span>
                    <span class="menu-text">Library</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>

                <ul class="submenu">
                    <li class="{!! request()->is('library/book*')?'active':'' !!}">
                        <a href="{{ route('library.book') }}" accesskey="L" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Books
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('library/member*')?'active':'' !!}">
                                <a href="{{ route('library.book') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Book Detail
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('library/book/add*')?'active':'' !!}">
                                <a href="{{ route('library.book.add') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Add New
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('library/book/import*')?'active':'' !!}">
                                <a href="{{ route('library.book.import') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Bulk Import
                                </a>

                                <b class="arrow"></b>
                            </li>


                            <li class="{!! request()->is('library/book/category*')?'active':'' !!}">
                                <a href="{{ route('library.book.category') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Category
                                </a>

                                <b class="arrow"></b>
                            </li>

                        </ul>
                    </li>

                    <li class="{!! request()->is('library/member*') || request()->is('library/student*') || request()->is('library/staff*') ?'active':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Members
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('library/member*')?'active':'' !!}">
                                <a href="{{ route('library.member') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Membership
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('library/student*')?'active':'' !!}">
                                <a href="{{ route('library.student') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Student Member
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('library/staff*')?'active':'' !!}">
                                <a href="{{ route('library.staff') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Staff Member
                                </a>

                                <b class="arrow"></b>
                            </li>

                        </ul>
                    </li>

                    <li class="{!! request()->is('library/issue-history*')?'active':'' !!}">
                        <a href="{{ route('library.issue-history') }}">
                            <span class="menu-icon"><i class="fa fa-history"></i></span>
                            Issue History
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('library/return-over*')?'active':'' !!}">
                        <a href="{{ route('library.return-over') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Return Period Over
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('library/circulation*')?'active':'' !!} ">
                        <a href="{{ route('library.circulation') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Circulation Setting
                        </a>

                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
        @endif
        @endability

        {{-- Attendance --}}
        @ability('super-admin','attendance')
        @if( isset($generalSetting) && isset($generalSetting->attendance) && $generalSetting->attendance ==1)
            <li class="{!! request()->is('attendance*')?'active':'' !!}">
                <a href="#" class="dropdown-toggle">
                    <span class="menu-icon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                    <span class="menu-text"> Attendance</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    <li class=">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Student Attendance
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('attendance/student*')?'active':'' !!}">
                                <a href="{{ route('attendance.student') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Regular Attendance
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('attendance/subject*')?'active':'' !!}">
                                <a href="{{ route('attendance.subject') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Subject Wise Attendance
                                </a>

                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>

                    <li class="{!! request()->is('attendance/staff*')?'active':'' !!}">
                        <a href="{{ route('attendance.staff') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Staff Attendance
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('attendance/master*')?'active':'' !!}">
                        <a href="{{ route('attendance.master') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Monthly Calendar
                        </a>

                        <b class="arrow"></b>
                    </li>

                </ul>
            </li>
        @endif
        @endability

        {{-- Examination --}}
        @ability('super-admin','examination')
        @if( isset($generalSetting) && isset($generalSetting->exam) && $generalSetting->exam == 1)
            <li class="{!! request()->is('exam*') || request()->is('mcq*')?'active':'' !!}">
                <a href="#" class="dropdown-toggle">
                    <span class="menu-icon"><i class="fa fa-line-chart"  aria-hidden="true"></i></span>
                    <span class="menu-text"> Exam</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li class="{!! request()->is('mcq*')?'active':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            <span class="menu-text"> Online - MCQ Exam</span>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">
                            <li class=">
                                <a href="#" class="dropdown-toggle">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Question Bank
                                    <b class="arrow fa fa-angle-r"></b>
                                </a>
                                <b class="arrow"></b>
                                <ul class="submenu">
                                    <li class="{!! request()->is('mcq/question/index*')?'active':'' !!}">
                                        <a href="{{ route('mcq.question.index') }}">
                                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                            Question
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="{!! request()->is('mcq/question/question-group*')?'active':'' !!}">
                                        <a href="{{ route('mcq.question.question-group') }}">
                                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                            Group
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="{!! request()->is('mcq/question/question-level*')?'active':'' !!}">
                                        <a href="{{ route('mcq.question.question-level') }}">
                                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                            Level
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>

                            <li class="{!! request()->is('mcq/exam/exam-instruction*')?'active':'' !!} ">
                                <a href="{{ route('mcq.exam.exam-instruction') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Instruction
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('exam')?'active':'' !!}">
                                <a href="{{ route('exam') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Online Exam
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>

                    <li class="{!! request()->is('exam*')?'active':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            <span class="menu-text"> Offline - Manual Exam</span>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">
                            <li class="{!! request()->is('exam/schedule*')?'active':'' !!}">
                                <a href="{{ route('exam.schedule') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Schedule Exam
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('exam/mark-ledger')?'active':'' !!} ">
                                <a href="{{ route('exam.mark-ledger') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Mark Ledger
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('exam')?'active':'' !!}">
                                <a href="{{ route('exam') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Exams Head
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('exam/admit-card*')?'active':'' !!}">
                                <a href="{{ route('exam.admit-card') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Admit Card
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('exam/routine*')?'active':'' !!}">
                                <a href="{{ route('exam.routine') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Routine/Schedule
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('exam/mark-sheet*')?'active':'' !!}">
                                <a href="{{ route('exam.mark-sheet') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
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
        @ability('super-admin','certificate')
        @if( isset($generalSetting) && isset($generalSetting->certificate) && $generalSetting->certificate ==1)
            <li class="{!! request()->is('certificate*')?'active':'' !!}">
                <a href="#" class="dropdown-toggle">
                    <span class="menu-icon"><i class="fa fa-certificate"  aria-hidden="true"></i></span>
                    <span class="menu-text"> Certificate</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li class="{!! request()->is('certificate/issue')?'active':'' !!}">
                        <a href="{{ route('certificate.issue') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Issue Certificate
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('certificate/attendance*')?'active':'' !!}">
                        <a href="{{ route('certificate.attendance') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Attendance Certificate
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('certificate/transfer*')?'active':'' !!}">
                        <a href="{{ route('certificate.transfer') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Transfer Certificate
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('certificate/character*')?'active':'' !!}">
                        <a href="{{ route('certificate.character') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Character Certificate
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('certificate/bonafide*')?'active':'' !!}">
                        <a href="{{ route('certificate.bonafide') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Bonafide Certificate
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('certificate/course-completion*')?'active':'' !!}">
                        <a href="{{ route('certificate.course-completion') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Course Completion Cer.
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('certificate/issue-history*')?'active':'' !!}">
                        <a href="{{ route('certificate.issue-history') }}">
                            <span class="menu-icon"><i class="fa fa-history"></i></span>
                            Issue History
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('certificate/generate*')?'active':'' !!}">
                        <a href="{{ route('certificate.generate') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Custom Print
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li class="{!! request()->is('certificate/template*')?'active':'' !!}">
                        <a href="{{ route('certificate.template') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Certificate Template
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
        @endif
        @endability

        {{-- Hostel --}}
        @ability('super-admin','hostel')
        @if( isset($generalSetting) && isset($generalSetting->hostel) && $generalSetting->hostel ==1)
            <li class="{!! request()->is('hostel*')?'active':'' !!}">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon  fa fa-bed" aria-hidden="true"></i>
                    <span class="menu-text"> Hostels </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    <li class="{!! request()->is('hostel/resident*')?'active':'' !!}">
                        <a href="{{ route('hostel.resident') }}" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Resident
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('hostel/resident')?'active':'' !!}">
                                <a href="{{ route('hostel.resident') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Detail
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('hostel/resident/add')?'active':'' !!}">
                                <a href="{{ route('hostel.resident.add') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Registration
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('hostel/resident/history')?'active':'' !!}">
                                <a href="{{ route('hostel.resident.history') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Occupant History
                                </a>
                                <b class="arrow"></b>
                            </li>

                        </ul>
                    </li>
                    <li class="{!! request()->is('hostel') || request()->is('hostel/room-type')?'active':'' !!}">
                        <a href="{{ route('hostel') }}" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Hostel
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">

                            <li class="{!! request()->is('hostel*')?'active':'' !!}">
                                <a href="{{ route('hostel') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Hostel
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('hostel/room-type*')?'active':'' !!}">
                                <a href="{{ route('hostel.room-type') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Room Type
                                </a>

                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>

                    <li class="{!! request()->is('hostel/food*')?'active':'' !!}">
                        <a href="{{ route('hostel') }}" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Food & Meal
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('hostel/food*')?'active':'' !!}">
                                <a href="{{ route('hostel.food') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Meal Schedule
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('hostel/food/eating-time*')?'active':'' !!}">
                                <a href="{{ route('hostel.food.eating-time') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Eating Time
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('hostel/food/category*')?'active':'' !!}">
                                <a href="{{ route('hostel.food.category') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Food Category
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('hostel/food/item*')?'active':'' !!}">
                                <a href="{{ route('hostel.food.item') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
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
        @ability('super-admin','transport')
        @if( isset($generalSetting) && isset($generalSetting->transport) && $generalSetting->transport ==1)
            <li class="{!! request()->is('transport*')?'active':'' !!}">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon  fa fa-bus" aria-hidden="true"></i>
                    <span class="menu-text"> Transport </span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    <li class="{!! request()->is('transport/user*')?'active':'' !!}">
                        <a href="{{ route('transport.user') }}" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Traveller/User
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('transport/user')?'active':'' !!}">
                                <a href="{{ route('transport.user') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Detail
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('transport/user/add')?'active':'' !!}">
                                <a href="{{ route('transport.user.add') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Registration
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('transport/user/history')?'active':'' !!}">
                                <a href="{{ route('transport.user.history') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    User History
                                </a>
                                <b class="arrow"></b>
                            </li>

                        </ul>
                    </li>
                    <li class="{!! request()->is('transport/route*')?'active':'' !!}">
                        <a href="{{ route('transport.route') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Route
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li class="{!! request()->is('transport/vehicle*')?'active':'' !!}">
                        <a href="{{ route('transport.vehicle') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Vehicle
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                    </li>

                </ul>
            </li>
        @endif
        @endability

        {{-- More --}}
        {{-- @ability('super-admin','assignment,download,meeting')
             <li class=">
                 <a href="#" class="dropdown-toggle">
                     <i class="menu-icon  fa fa-th-list" aria-hidden="true"></i>
                     <span class="menu-text"> More </span>

                     <b class="arrow fa fa-angle-down"></b>
                 </a>
                 <b class="arrow"></b>
                 <ul class="submenu">
                     @ability('super-admin','assignment')
                         @if( isset($generalSetting) && isset($generalSetting->assignment) && $generalSetting->assignment ==1)
                             <li class="{!! request()->is('assignment')?'active':'' !!}">
                             <a href="{{ route('assignment') }}">
                                 <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                 Assignment
                             </a>
                             <b class="arrow"></b>
                         </li>
                         @endif
                     @endability

                     @ability('super-admin','download')
                         @if( isset($generalSetting) && isset($generalSetting->upload_download) && $generalSetting->upload_download ==1)
                             <li class="{!! request()->is('download*')?'active':'' !!}">
                             <a href="{{ route('download') }}">
                                 <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                 Upload & Download
                                 <b class="arrow fa fa-angle-r"></b>
                             </a>
                         </li>
                         @endif
                     @endability

                     @ability('super-admin','meeting')
                     @if( isset($generalSetting) && isset($generalSetting->meeting) && $generalSetting->meeting ==1)
                         <li class="{!! request()->is('meeting*')?'active':'' !!}">
                             <a href="{{ route('meeting') }}">
                                 <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                 Meeting - Remote Class
                                 <b class="arrow fa fa-angle-r"></b>
                             </a>
                         </li>
                     @endif
                     @endability
                 </ul>
             </li>
         @endability--}}

        @ability('super-admin','assignment')
        @if( isset($generalSetting) && isset($generalSetting->assignment) && $generalSetting->assignment ==1)
            <li class="{!! request()->is('assignment')?'active':'' !!}">
                <a href="{{ route('assignment') }}">
                    <span class="menu-icon"><i class="fa fa-tasks"></i></span>
                    Assignment
                </a>
                <b class="arrow"></b>
            </li>
        @endif
        @endability

        @ability('super-admin','download')
        @if( isset($generalSetting) && isset($generalSetting->upload_download) && $generalSetting->upload_download ==1)
            <li class="{!! request()->is('download*')?'active':'' !!}">
                <a href="{{ route('download') }}">
                    <span class="menu-icon"><i class="fa fa-download"></i></span>
                    Download
                    <b class="arrow fa fa-angle-r"></b>
                </a>
            </li>
        @endif
        @endability

        @ability('super-admin','meeting')
        @if( isset($generalSetting) && isset($generalSetting->meeting) && $generalSetting->meeting ==1)
            <li class="{!! request()->is('meeting*')?'active':'' !!}">
                <a href="{{ route('meeting') }}">
                    <span class="menu-icon"><i class="fa fa-video-camera"></i></span>
                    Meeting
                    <b class="arrow fa fa-angle-r"></b>
                </a>
            </li>
        @endif
        @endability


        {{-- Reports --}}
        @ability('super-admin','report')
        {{--<li class="{!! request()->is('report*')?'active':'' !!}">
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-bar-chart"  aria-hidden="true"></i></span>
                <span class="menu-text"> Report Links</span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="{!! request()->is('report/student*')?'active':'' !!}">
                    <a href="{{ route('report.student') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Student Detail
                    </a>

                    <b class="arrow"></b>
                </li>

                <li class="{!! request()->is('report/staff*')?'active':'' !!}">
                    <a href="{{ route('report.staff') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Staff Detail
                    </a>

                    <b class="arrow"></b>
                </li>
                @ability('super-admin','fees-index')
                <li class="{!! request()->is('account/fees')?'active':'' !!}">
                    <a href="{{ route('account.fees') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Fees Statement
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','fees-balance')
                <li class="{!! request()->is('account/fees/balance')?'active':'' !!}">
                    <a href="{{ route('account.fees.balance') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Balance Fees
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','payroll-balance')
                <li class="{!! request()->is('account/payroll/balance')?'active':'' !!}">
                    <a href="{{ route('account.payroll.balance') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Balance Salary
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','transaction-head-index')
                <li class="{!! request()->is('account/transaction-head')?'active':'' !!}">
                    <a href="{{ route('account.transaction-head') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Ledger
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','transaction-index')
                <li class="{!! request()->is('account/transaction')?'active':'' !!}">
                    <a href="{{ route('account.transaction') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Transactions
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','bank-index')
                <li class="{!! request()->is('account/bank')?'active':'' !!}">
                    <a href="{{ route('account.bank') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Bank Statement
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','library-issue-history')
                <li class="{!! request()->is('library/issue-history')?'active':'' !!}">
                    <a href="{{ route('library.issue-history') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Library Issue History
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','library-return-over')
                <li class="{!! request()->is('library/return-over')?'active':'' !!}">
                    <a href="{{ route('library.return-over') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Book Return Period Over
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','student-attendance-index')
                <li class="{!! request()->is('attendance/student')?'active':'' !!}">
                    <a href="{{ route('attendance.student') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Student Attendance
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','staff-attendance-index')
                <li class="{!! request()->is('attendance/staff')?'active':'' !!}">
                    <a href="{{ route('attendance.staff') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Staff Attendance
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','resident-history')
                <li class="{!! request()->is('hostel/resident/history')?'active':'' !!}">
                    <a href="{{ route('hostel.resident.history') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Hostel History
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability

                @ability('super-admin','transport-user-history')
                <li class="{!! request()->is('transport/user/history')?'active':'' !!}">
                    <a href="{{ route('transport.user.history') }}">
                        <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                        Transport History
                    </a>
                    <b class="arrow"></b>
                </li>
                @endability
            </ul>
        </li>--}}
        @endability

        {{-- Info Center --}}
        @ability('super-admin','alert-center')
        @if( isset($generalSetting) && isset($generalSetting->alert) && $generalSetting->alert ==1)
            <li class="{!! request()->is('info*')?'active':'' !!}">
                <a href="#" class="dropdown-toggle">
                    <span class="menu-icon"><i class="fa fa-bullhorn" aria-hidden="true"></i></span>
                    <span class="menu-text"> Alert </span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    <li class="{!! request()->is('info/notice*')?'active':'' !!}">
                        <a href="{{ route('info.notice') }}" accesskey="L">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            User Notice
                        </a>

                        <b class="arrow"></b>
                    </li>
                    <li class="{!! request()->is('info/smsemail*')?'active':'' !!}">
                        <a href="{{ route('info.smsemail') }}">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            SMS / E-mail
                        </a>

                        <b class="arrow"></b>
                    </li>

                </ul>
            </li>
        @endif
        @endability

        {{-- Academic --}}
        @ability('super-admin','academic')
        @if( isset($generalSetting) && isset($generalSetting->academic) && $generalSetting->academic ==1)

            <li class="{!! request()->is('info*')?'active':'' !!}">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon  fa fa-graduation-cap" aria-hidden="true"></i>
                    <span class="menu-text"> Academics </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li class="{!! request()->is('faculty*') || request()->is('semester*')?'active':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Academic Level
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('faculty*')?'active':'' !!}">
                                <a href="{{ route('faculty') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Faculty/Program/Class
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('semester*')?'active':'' !!}">
                                <a href="{{ route('semester') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Semester/Section
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('student-batch*')?'active':'' !!}">
                                <a href="{{ route('student-batch') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Batch
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>
                    <li class="{!! request()->is('grading*') || request()->is('subject*')?'active':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Grading/Subject
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('subject*')?'active':'' !!}">
                                <a href="{{ route('subject') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Course / Subject
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('grading*')?'active':'' !!}">
                                <a href="{{ route('grading') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Grading
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>

                    <li class="{!! request()->is('*status')?'active':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Status Setting
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('student-status*')?'active':'' !!}">
                                <a href="{{ route('student-status') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Student Status
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('attendance-status*')?'active':'' !!}">
                                <a href="{{ route('attendance-status') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Attendance Status
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('book-status*')?'active':'' !!}">
                                <a href="{{ route('book-status') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Books Status
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="{!! request()->is('bed-status*')?'active':'' !!}">
                                <a href="{{ route('bed-status') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Hostel Bed Status
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('customer-status*')?'active':'' !!}">
                                <a href="{{ route('customer-status') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Customer Status
                                </a>

                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>


                    <li class="{!! request()->is('year*') || request()->is('month*')?'active':'' !!}">
                        <a href="#" class="dropdown-toggle">
                            <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                            Year & Month
                            <b class="arrow fa fa-angle-r"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="{!! request()->is('year*')?'active':'' !!}">
                                <a href="{{ route('year') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Year
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('month*')?'active':'' !!}">
                                <a href="{{ route('month') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Month
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="{!! request()->is('day*')?'active':'' !!}">
                                <a href="{{ route('day') }}">
                                    <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                    Day
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
        @ability('super-admin','help')
            @if( isset($generalSetting) && isset($generalSetting->help) && $generalSetting->help ==1)
            <li class="{!! request()->is('help*')?'active':'' !!}">
                <a href="#" class="dropdown-toggle">
                        <i class="menu-icon  fa fa-question" aria-hidden="true"></i>
                        <span class="menu-text"> Help </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="{{route('license-info')}}" target="_self">
                                <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                License Info
                            </a>
                        </li>
                        <li class="">
                            <a href="http://unlimitededufirm.com/demo-detail" target="_blank">
                                <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                Test Demo
                            </a>
                        </li>
                        <li class="">
                            <a href="https://www.youtube.com/watch?v=2jgA9WY8IzQ&list=PLCtD_CGPAQJ2zSk5cDUkkfWGdtMGsF9n0" target="_blank">
                                <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                Video Tutorial
                            </a>
                        </li>
                        <li class="">
                            <a href="http://docs.unlimitededufirm.com" target="_blank">
                                <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                Documentation
                            </a>
                        </li>
                        <li class="">
                            <a href="https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988" target="_blank">
                                <span class="menu-icon"><i class="fa fa-caret-right"></i></span>
                                Buy New License
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        @endability
    </ul><!-- /.nav-list -->
    <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
        <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
    </div>
</div>

