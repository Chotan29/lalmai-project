<?php

namespace App\Models;

/**
 * The bank account a fee head's money is transferred into.
 *
 * A head may have one, or one per department, or two chosen by the student's religion. Which of
 * those applies is decided by the rows themselves rather than by anything the caller has to know.
 */
class FeeHeadBankAccount extends BaseModel
{
    protected $table = 'fee_head_bank_accounts';

    protected $fillable = [
        'fee_head_id', 'serial', 'faculty_id', 'account_name', 'account_no',
        'match_religion', 'is_default', 'note',
        'created_by', 'last_updated_by', 'status',
    ];

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class, 'fee_head_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /** The religions this account takes, as a list. */
    public function religions()
    {
        $raw = trim((string) $this->match_religion);
        if ($raw === '') { return []; }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    public function takesReligion($religion)
    {
        $wanted = $this->religions();
        if (!$wanted) { return false; }

        $religion = trim((string) $religion);
        foreach ($wanted as $r) {
            if (strcasecmp($r, $religion) === 0) { return true; }
        }

        return false;
    }

    /**
     * Choose the account for one head and one student.
     *
     * Narrowest match first: the student's own department beats an account that serves everybody,
     * and a religion rule beats no rule. Whatever is left over goes to the default, so a student
     * with no religion recorded still has their money land somewhere and the letter still adds up.
     *
     * @param  \Illuminate\Support\Collection $accounts  rows for this head, already loaded
     */
    public static function choose($accounts, $facultyId, $religion)
    {
        /*
         * BaseModel turns status into the words "active" and "in-active" on the way out, so
         * (int) $a->status is 0 for every live row - and this filter emptied the pool completely,
         * which made every head look as though it had no bank account at all. The stored value is
         * read instead of the accessor's translation of it.
         */
        $rows = $accounts->filter(function ($a) {
            $raw = $a->getAttributes()['status'] ?? 1;
            return (int) $raw === 1;
        });

        /*
         * Department accounts, if this head has any for this department.
         *
         * Filtered rather than ->where(): this is a loaded collection, not a query, and a
         * collection has no whereNull - asking for one throws rather than returning nothing, which
         * is at least honest but stops the letter dead.
         */
        $mine = $rows->filter(function ($a) use ($facultyId) {
            return $facultyId && (int) $a->faculty_id === (int) $facultyId;
        });

        $pool = $mine->count()
            ? $mine
            : $rows->filter(function ($a) { return $a->faculty_id === null; });

        if (!$pool->count()) { $pool = $rows; }

        foreach ($pool as $account) {
            if ($account->takesReligion($religion)) { return $account; }
        }

        $default = $pool->filter(function ($a) { return (bool) $a->is_default; })->first();
        if ($default) { return $default; }

        /* No rules at all - the ordinary case, one account for the head. */
        $plain = $pool->filter(function ($a) { return $a->religions() === []; })->first();

        return $plain ?: $pool->first();
    }
}
