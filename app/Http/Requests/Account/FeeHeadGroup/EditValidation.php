<?php

namespace App\Http\Requests\Account\FeeHeadGroup;

use Illuminate\Foundation\Http\FormRequest;

class EditValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /* The name stays unique, but not against the row being edited - the id comes off the
           route, already decrypted by the controller before it reaches here. */
        $id = (int) $this->get('row_id');

        return [
            'title'                 => 'required | max:150 | unique:fee_head_groups,title,'.$id,
            'total_amount'          => 'required | numeric | min:1',
            'fee_head_id'           => 'required | array | min:1',
            'fee_head_id.*'         => 'required | exists:fee_heads,id',
            'amount'                => 'required | array | min:1',
            'amount.*'              => 'required | numeric | min:0',
        ];
    }

    public function messages()
    {
        return [
            'title.required'         => 'Main Fee Head Name Required',
            'title.unique'           => 'Please Enter Unique Main Fee Head Name.',
            'total_amount.required'  => 'Total Amount Required',
            'total_amount.min'       => 'Total Amount Must Be Greater Than 0.',
            'fee_head_id.required'   => 'Please, Add At Least One Sub Head.',
            'fee_head_id.*.required' => 'Please, Select Sub Head.',
            'fee_head_id.*.exists'   => 'Invalid Sub Head Selected.',
            'amount.*.required'      => 'Sub Head Amount Required',
            'amount.*.numeric'       => 'Sub Head Amount Must Be A Number.',
            'amount.*.min'           => 'Sub Head Amount Can Not Be Negative.',
        ];
    }
}
