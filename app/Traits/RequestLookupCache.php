<?php

namespace App\Traits;

/**
 * Remember a row for the length of one request.
 *
 * The same shape of bug keeps turning up: a helper like getFeeHeadById is called from inside a
 * table row, so a page of 5,782 receipts asks the database 5,782 times which fee head each one
 * is - about a dozen distinct answers, fetched five thousand times. It was found in the
 * department and semester names, then in the exam schedules and subjects, and now in the fee
 * heads, and each time it was fixed by writing the same little store again.
 *
 * So it is written here once. A trait that needs it says `use RequestLookupCache` and calls
 * lookupOnce; the fix then lives inside the helper, not at the hundreds of places that call it.
 *
 * For one request only, on purpose. A renamed fee head is right on the very next page, and
 * nothing has to be told to clear anything. The flush is for a long-running worker that would
 * otherwise hold a row for hours.
 *
 * Keys carry their own prefix - 'fee_head:12' - because when one class uses several traits PHP
 * flattens them together and they share this one store.
 */
trait RequestLookupCache
{
    protected static $requestLookupCache = [];

    protected function lookupOnce($key, callable $load)
    {
        if (!array_key_exists($key, self::$requestLookupCache)) {
            self::$requestLookupCache[$key] = $load();
        }

        return self::$requestLookupCache[$key];
    }

    public static function flushRequestLookupCache()
    {
        self::$requestLookupCache = [];
    }
}
