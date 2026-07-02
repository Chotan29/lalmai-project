<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the paginator library to build
    | the simple pagination links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */
    'name'      =>  __('module.account'),
    'child'     =>    [
                        'fees' =>
                                            [
                                                'name'=>'Fee Collection',
                                                'child' => [
                                                                'detail'            =>  'Receive Detail',
                                                                'quick_receive'     =>  'Quick Receive',
                                                                'collection'           =>  'Collect Fees',
                                                                'balance'           =>  'Balance Fees Report',
                                                                'add'               =>  'Add Fees',
                                                                'online_payment'    =>  'Online Payments',
                                                                'head'              =>  'Fees Head',
                                                            ],
                                            ],
                        'fee_report' =>
                                        [
                                            'name'=>'Fee Report',
                                            'child' => [
                                                'cash_book'             =>  'Cash Book',
                                                'fee_collection'        =>  'Fee Collection',
                                                'online_payment'        =>  'Online Payments',
                                                'fee_collection_head'   =>  'Fee Collection Head',
                                                'fee_balance'           =>  'Balance / Dues',
                                            ],
                                        ],
                        'ledger_transaction' =>
                                            [
                                                'name'=>'Ledger & Transaction',
                                                'child' => [
                                                            'transaction'                       =>  'Transaction',
                                                            'multi'                 =>  'Multi Transaction',
                                                            'detail'                =>  'Transaction Detail',
                                                            'transfer'       =>  'Acc to Acc Transfer',
                                                            'transaction_head'                    =>  'Ledger/Account',
                                                            'account_groups'                    =>  'Account Groups',
                                                            'charts_of_account'                 =>  'Charts of Account',
                                                        ],
                                            ],
                        'banking' =>
                                        [
                                            'name'=>'Separate Banking',
                                            'child' => [
                                                            'manage_bank_account'       =>      'Manage Bank Accounts',
                                                            'add_new_bank'              =>      'Add New Bank',
                                                            'transaction_detail'        =>      'Transaction Detail',
                                                            'new_transaction'           =>      'New Transaction',
                                                        ],
                                        ],
                        'payroll' =>
                                        [
                                            'name'=>'Payroll',
                                            'child' => [
                                                            "paid_detail"       =>      "Paid Detail",
                                                            "payment"               =>      "Salary Pay",
                                                            "add"       =>      "Add Payroll",
                                                            "balance"           =>      "Balance Salary Report",
                                                            "head"              =>      "Salary Head",
                                                        ],
                                            ],

                        'account_report' =>
                                            [
                                                'name'=>'Account Report',
                                                'child' => [
                                                                'statement'      =>  'Statement of Ledger',
                                                                'balance'      =>  'Ledger Balance',
                                                                'coa'      =>  'Charts of Account',
                                                            ],

                                            ],
                    ],
    ];