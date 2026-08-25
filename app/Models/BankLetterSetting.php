<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The unchanging half of the bank transfer letter.
 *
 * Plain Eloquent rather than BaseModel: this is one row of settings with no status of its own and
 * nothing to audit beyond who last touched it.
 */
class BankLetterSetting extends Model
{
    protected $table = 'bank_letter_settings';

    protected $fillable = [
        'college_name', 'college_address', 'from_designation',
        'bank_name', 'branch_name', 'to_designation',
        'source_account_no', 'source_account_title',
        'principal_name', 'principal_designation',
        'contact_mobile', 'body_text', 'last_updated_by',
    ];

    /**
     * The one row, made if it is not there yet.
     *
     * A letter screen that fails because nobody has visited a settings page first is a screen that
     * gets reported as broken.
     */
    public static function current()
    {
        return static::first() ?: new static;
    }
}
