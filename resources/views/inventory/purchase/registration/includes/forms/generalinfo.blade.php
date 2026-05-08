<div class="form-group">
    @if (!isset($data['row']))
        <label class="col-sm-1 control-label">Vendor</label>
        <div class="col-sm-5">
            {!! Form::select('vendor_id', $data['vendor'], null, ['class' => 'chosen-select form-control', "required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'vendor_id'])
        </div>
    @else
        <label class="col-sm-2 control-label">Vendor</label>
        <div class="col-sm-4">
            {!! Form::select('vendor_id', $data['vendor'], null, ['class' => 'chosen-select form-control', "required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'vendor_id'])
        </div>
    @endif

    {!! Form::label('purchase_date', 'Date', ['class' => 'col-sm-1 control-label',]) !!}
    <div class="col-sm-2">
        {!! Form::text('purchase_date', null, ["class" => "form-control date-picker border-form input-mask-date","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'purchase_date'])
    </div>

        {!! Form::label('invoice_no', 'InvoiceNo', ['class' => 'col-sm-1 control-label',]) !!}
        <div class="col-sm-2">
            {!! Form::text('invoice_no', null, ["class" => "form-control border-form","required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'invoice_no'])
        </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover" id="purchaseTable">
        <thead>
        <tr>
            <th class="text-center" width="20%">Item Information<i class="text-danger">*</i></th>
            <th class="text-center">Stock/Qnt</th>
            <th class="text-center">Qnty <i class="text-danger">*</i></th>
            <th class="text-center">Rate<i class="text-danger">*</i></th>
            <th class="text-center">Total</th>
            <th class="text-center">Action</th>
        </tr>
        </thead>
        <tbody id="addPurchaseItem">
            <tr>
                <td class="span3 supplier">
                    <input type="text" name="product_name" required="" class="form-control product_name productSelection" onkeypress="product_pur_or_list(1);" placeholder="Product Name" id="product_name_1" tabindex="5" aria-required="true">

                    <input type="hidden" class="autocomplete_hidden_value product_id_1" name="product_id[]" id="SchoolHiddenId">

                    <input type="hidden" class="sl" value="1">
                </td>

                <td class="wt">
                    <input type="text" id="available_quantity_1" class="form-control text-right stock_ctn_1" placeholder="0.00" readonly="">
                </td>

                <td class="text-right">
                    <input type="text" name="product_quantity[]" id="cartoon_1" required="" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_store(1);" onchange="calculate_store(1);" placeholder="0.00" value="" tabindex="6" aria-required="true">
                </td>
                <td class="test">
                    <input type="text" name="product_rate[]" required="" onkeyup="calculate_store(1);" onchange="calculate_store(1);" id="product_rate_1" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" aria-required="true">
                </td>


                <td class="text-right">
                    <input class="form-control total_price text-right" type="text" name="total_price[]" id="total_price_1" value="0.00" readonly="readonly">
                </td>
                <td>



                    <button class="btn btn-danger text-right red" type="button" value="Delete" onclick="deleteRow(this)" tabindex="8"><i class="fa fa-close"></i></button>
                </td>
            </tr>
        </tbody>
        <tfoot>
        <tr>

            <td class="text-right" colspan="4"><b>Total:</b></td>
            <td class="text-right">
                <input type="text" id="Total" class="text-right form-control" name="total" value="0.00" readonly="readonly">
            </td>
            <td> <button type="button" id="add_invoice_item" class="btn btn-info" name="add-invoice-item" onclick="addPurchaseOrderField1('addPurchaseItem')" tabindex="13"><i class="fa fa-plus"></i></button>

                <input type="hidden" name="baseUrl" class="baseUrl" value="https://saleserpnew.bdtask.com/saleserp_v9.8_demo/"></td>
        </tr>
        <tr>

            <td class="text-right" colspan="4"><b>Discount:</b></td>
            <td class="text-right">
                <input type="text" id="discount" class="text-right form-control discount" onkeyup="calculate_store(1)" name="discount" placeholder="0.00" value="">
            </td>
            <td>

            </td>
        </tr>

        <tr>
            <td class="text-right" colspan="4"><b>Grand Total:</b></td>
            <td class="text-right">
                <input type="text" id="grandTotal" class="text-right form-control" name="grand_total_price" value="0.00" readonly="readonly">
            </td>
            <td> </td>
        </tr>
        <tr>
            <td class="text-right" colspan="4"><b>Paid Amount:</b></td>
            <td class="text-right">
                <input type="text" id="paidAmount" class="text-right form-control" onkeyup="invoice_paidamount()" name="paid_amount" value="">
            </td>
            <td> </td>
        </tr>
        <tr>
            <td colspan="2" class="text-right">
                <input type="button" id="full_paid_tab" class="btn btn-warning" value="Full Paid" tabindex="16" onclick="full_paid()">
            </td>
            <td class="text-right" colspan="2"><b>Due Amount:</b></td>
            <td class="text-right">
                <input type="text" id="dueAmmount" class="text-right form-control" name="due_amount" value="0.00" readonly="readonly">
            </td>
            <td></td>
        </tr>
        </tfoot>
    </table>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">PaymentMethod</label>
    <div class="col-sm-4">
        {!! Form::select('payment_method', $data['paymentMethod'], null, ['class' => 'form-control', 'onChange' => 'loadPaymentLedger(this);', "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'payment_method'])
    </div>

    <div class="col-sm-4">
        {!! Form::select('payment_ledger', ['class' => 'form-control paymentledger', 'onChange' => 'loadPaymentLedger(this);', "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'payment_ledger'])
    </div>

</div>

<div class="form-group">
    {!! Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', null, ["class" => "form-control border-form", "rows"=>"1"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'description'])
    </div>
</div>