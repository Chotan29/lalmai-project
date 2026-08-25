<?php

/*
 * Attendance notifications.
 *
 * These two settings exist because of what the log showed on 19 August: the same eight
 * attendance rows being sent to the sms gateway every fifteen minutes, all day, for ever. The
 * sweep that looks for unsent notifications treated "failed" as "try again", and nothing ever
 * counted the attempts or asked how old the attendance was.
 *
 * While the gateway had no credit that cost nothing. The moment credit is added it would have
 * sent every one of those messages at once, and gone on sending them.
 */

return [

    /*
     * How many times to try telling a guardian before giving up.
     *
     * A message that has failed three times is not going to succeed on the fortieth - the number
     * is wrong, or the gateway is refusing, and both need a person rather than another attempt.
     * The row is marked abandoned so it stops being picked up and can still be found later.
     */
    'notify_max_attempts' => (int) env('ATTENDANCE_NOTIFY_MAX_ATTEMPTS', 3),

    /*
     * How far back to keep trying, in days.
     *
     * Telling a parent their child arrived at school last Tuesday helps nobody. Anything older
     * than this is left alone whatever its state.
     */
    'notify_max_age_days' => (int) env('ATTENDANCE_NOTIFY_MAX_AGE_DAYS', 2),

];
