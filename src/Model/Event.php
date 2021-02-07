<?php
/** The Event class is a part of the Model of IMT2571 Assignment #1.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/
 *      The tutorial code used as basis.
 */

/** The Event is the class holding data related to one event.
 */
class Event
{
    /** String table for error cause messages
      */
    public const MSG_TABLE = ['Event id expected to be a valid number. ',
                            'Event title is mandatory. ',
                            'Event date is mandatory. ',
                            'Date must be of format YYYY-MM-DD. ',
                            'Date must be an existing one. '
                        ];

    /** String table index for invalid id message */
    public const MSGID_INVALID_ID = 0;

        /** String table index for empty title message */
    public const MSGID_NO_TITLE = 1;

        /** String table index for empty date message */
    public const MSGID_NO_DATE = 2;

        /** String table index for invalid date format message */
    public const MSGID_INCORRECT_DATE_FORMAT = 3;

        /** String table index for invalid date value message */
    public const MSGID_INVALID_DATE = 4;

    /**
      * @var int The database id of the event; -1 for events not yet stored.
      */
    public $id;

    /**
      * @var string The title of the event.
      */
    public $title;

    /**
      * @var int The date of the event.
      */
    public $date;

    /**
      * @var int The description of the event.
      */
    public $description;

    /** Constructor
     * @param string $title Event title
     * @param string $date Event date
     * @param string $description Event description
     * @param integer $id Event id (optional); event id's should be created by
     *                the model when the event is stored for the first time.
     */
    public function __construct($title, $date, $description, $id = -1)
    {
        $this->id = $id;
        $this->title = $title;
        $this->date = trim($date);
        $this->description = $description;
    }

    /** Helper function verifying that ids are numbers.
     * @param integer $id The event id to check.
     * @throws InvalidArgumentException if $id is not a number.
     * @static
     */
    public static function verifyId($id)
    {
        if (!is_numeric($id) || $id < 1) {
            throw new InvalidArgumentException
                      (self::MSG_TABLE[self::MSGID_INVALID_ID]);
        }
    }

    /** Helper function verifying that the passed string represents a validating
     * date of the format "YYYY-MM-DD" in all digits.
     * @param String $date The date string to check.
     * @throws InvalidArgumentException if the string does not represent a valid
     *         date.
     * @static
     */
    private function verifyDate()
    {
        if (!preg_match('/^(?P<year>\d{4})-(?P<month>\d{2})-(?P<day>\d{2})$/',
            $this->date, $dateParts)) {
            throw new InvalidArgumentException
                      (self::MSG_TABLE[self::MSGID_INCORRECT_DATE_FORMAT]);
        }
        if (!checkdate($dateParts['month'], $dateParts['day'],
                       $dateParts['year'])) {
            throw new InvalidArgumentException(
                          self::MSG_TABLE[self::MSGID_INVALID_DATE]);
        }
    }

    /** Helper function verifying that event data is valid - i.e., that $id is
     *  a valid number (only when $ignoreId is false) and that title and date
     *  are non-empty strings and that dates are valid.
     *  @param Event $event The event record to check.
     *  @param boolean $ignoreId The id of event is only checked if $ignoreId is
     *  false.
     *  @throws InvalidArgumentException if event data is invalid.
     */
    public function verify($ignoreId = false)
    {
        $isOk = true;
        $msg = '';
        if (!$ignoreId && !is_numeric($this->id)) {
            $msg .= self::MSG_TABLE[self::MSGID_INVALID_ID];
            $isOk = false;
       }
        if ($this->title == null || $this->title == '') {
            if (!$isOk)
            {
                $msg .= '<br />';
            }
            $msg .= self::MSG_TABLE[self::MSGID_NO_TITLE];
            $isOk = false;
        }
        if ($this->date == null || $this->date == '') {
            if (!$isOk)
            {
                $msg .= '<br />';
            }
            $msg .= self::MSG_TABLE[self::MSGID_NO_DATE];
            $isOk = false;
        } else {
            // Check that given date is valid
            try {
                $this->verifyDate();
            } catch (InvalidArgumentException $e) {
                $msg .= $e->getMessage();
                $isOk = false;
            }
        }
        if (!$isOk) {
            throw new InvalidArgumentException($msg);
        }
    }
}
