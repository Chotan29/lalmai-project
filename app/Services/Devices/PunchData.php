<?php

namespace App\Services\Devices;

use Carbon\Carbon;

/**
 * One face recognised at a device, in a shape that has nothing to do with any manufacturer.
 *
 * This is the seam. A Tipsoi device sends personId, time in milliseconds and isPass; the next
 * brand will send something else under other names. Each driver turns its own json into one of
 * these, and everything downstream - the ingestor, the attendance row, the guardian message -
 * only ever sees this. That is what makes a second brand a day's work rather than a rewrite.
 */
class PunchData
{
    /** Device serial number, as the device reports it. */
    public $deviceKey;

    /** Whoever this is, in the id WE gave the device when enrolling - so, a student id. */
    public $personId;

    /** @var Carbon When the face was seen. */
    public $at;

    /** Did the device actually recognise them, or is this a stranger / failed match? */
    public $recognised;

    /** What the device called them, useful only for checking our own mapping. */
    public $name;

    /** The untouched payload, kept so a mistake in parsing can be traced to the source. */
    public $raw;

    /**
     * Which brand produced this, as keyed in config/devices.php.
     *
     * The ingestor stays brand-blind about behaviour, but a couple of stored values are still
     * brand-specific - attendances.source is an enum with a value per capture method - so it
     * needs to know which config block to read. It never branches on this.
     */
    public $vendor;

    public function __construct($deviceKey, $personId, Carbon $at, $recognised = true, $name = null, array $raw = [], $vendor = null)
    {
        $this->vendor     = $vendor;
        $this->deviceKey  = trim((string) $deviceKey);
        $this->personId   = trim((string) $personId);
        $this->at         = $at;
        $this->recognised = (bool) $recognised;
        $this->name       = $name;
        $this->raw        = $raw;
    }

    /**
     * A stranger, or a face the device could not match, is not somebody's attendance.
     *
     * The device reports these with personId STRANGERBABY - three of them were sitting in the
     * device when it was first read. They are worth keeping as a record but must never become an
     * attendance row, and certainly never a message to a guardian.
     */
    public function isIdentified()
    {
        if (!$this->recognised) { return false; }
        if ($this->personId === '') { return false; }
        if (strcasecmp($this->personId, 'STRANGERBABY') === 0) { return false; }
        if (strcasecmp($this->personId, 'IDCARD') === 0) { return false; }

        return true;
    }
}
