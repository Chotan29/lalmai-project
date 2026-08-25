<?php

namespace App\Traits;

/**
 * Reading the fee filter, and matching money against it.
 *
 * Its own trait rather than part of AccountingScope because the bank letter needs it too, and the
 * letter is a plain service - pulling the whole accounting scope in for two methods would drag a
 * dozen models and a request cache along with it.
 *
 * The point of it existing at all: this match used to be copied into nine separate queries - the
 * head table, the department table, both of their refund queries, the date span, the programme
 * list, both queries behind the letter, and the general fee filter. Nine copies of a rule about
 * where money belongs is eight chances for one of them to be left behind, and a report that
 * quietly answers a different question than the sheet beside it.
 */
trait FeeGroupFilter
{
    /**
     * Every Main Fee Head the filter picked.
     *
     * The filter holds a comma separated list - "GROUP:1,GROUP:2" - rather than an array, so a
     * link written when only one fee could be chosen ("?fee_heads=GROUP:1") still reads correctly
     * as a list of one. Every bookmark, print button and download link in the office keeps working
     * without being rewritten.
     *
     * Anything in the list that is not a Main Fee Head is dropped here. Mixing a fee with a plain
     * head is refused higher up, where there is a screen to say so on.
     *
     * @return int[]  in the order given, without duplicates
     */
    public function feeGroupIdsFromFilter($value)
    {
        /* A form posting fee_heads[] hands us an array; a URL hands us the comma list. */
        $parts = is_array($value) ? $value : explode(',', (string) $value);

        $ids = [];
        foreach ($parts as $part) {
            $part = trim((string) $part);

            if (strpos($part, 'GROUP:') !== 0) {
                continue;
            }

            $id = (int) substr($part, 6);
            if ($id > 0 && !in_array($id, $ids, true)) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /** True when the filter names something that is not a Main Fee Head. */
    public function feeFilterHasPlainHead($value)
    {
        $parts = is_array($value) ? $value : explode(',', (string) $value);

        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part !== '' && $part !== '0' && strpos($part, 'GROUP:') !== 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whatever was passed for the fee, as a list of Main Fee Head ids.
     *
     * Callers pass a single id, a "GROUP:1" string, a comma list, or an array of any of those.
     * Accepting all of them is what let the existing callers keep their single-id calls unchanged
     * while the same methods learned to take several.
     *
     * @return int[]
     */
    public function asGroupIds($groupId)
    {
        if (is_array($groupId)) {
            $out = [];
            foreach ($groupId as $one) {
                foreach ($this->asGroupIds($one) as $id) {
                    if (!in_array($id, $out, true)) { $out[] = $id; }
                }
            }
            return $out;
        }

        /* A bare number is an id already; anything else is a filter string to be read. */
        if (is_int($groupId) || ctype_digit((string) $groupId)) {
            return ((int) $groupId) > 0 ? [(int) $groupId] : [];
        }

        return $this->feeGroupIdsFromFilter($groupId);
    }

    /**
     * Narrow a fee_collections query (joined as `fm`) to one or more Main Fee Heads.
     *
     * @param  int[] $groupIds
     */
    public function applyFeeGroups($query, array $groupIds)
    {
        if (!$groupIds) {
            /* Asked for no fee at all, answer with no money rather than with all of it. */
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($outer) use ($groupIds) {
            foreach ($groupIds as $groupId) {
                $outer->orWhere(function ($q) use ($groupId) {
                    /* A recurring run tags the period on the end ("GROUP-3-2026-08") while a
                       one-off does not, so both shapes have to match - and matching the prefix
                       alone would let GROUP-1 swallow GROUP-10. */
                    $q->where('fm.billing_period_key', 'GROUP-' . $groupId)
                      ->orWhere('fm.billing_period_key', 'like', 'GROUP-' . $groupId . '-%');
                });
            }
        });
    }
}
