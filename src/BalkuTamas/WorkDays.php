<?php

namespace BalkuTamas;

/**
 * Class WorkDays
 *
 * @author  Balku Tamas
 * @link    http://btamas.hu Author website
 *
 * @package BalkuTamas
 */
class WorkDays
{
    const VERSION = '1.0';

    /**
     * Singleton reference to singleton instance.
     *
     * @var self
     */
    private static $instance = null;

    /**
     * Current date.
     *
     * @var \DateTime
     */
    protected $currentDate = null;

    /**
     * Week days, you might want to skip.
     *
     * @var array
     */
    protected $skipDays = [];

    /**
     * Days of the year, you might want to skip.
     *
     * @var array
     */
    protected $skipDates = [];

    /**
     * The list of errors.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Gets the instance via lazy initialization (created on first usage)
     *
     * @return self
     */
    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new self();

            // Default date
            static::$instance->currentDate = new \DateTime();
        }

        return static::$instance;
    }

    /**
     * Is not allowed to call from outside: private!
     */
    private function __construct()
    {
        // no action
    }

    /**
     * Prevent the instance from being cloned.
     *
     * @return void
     */
    private function __clone()
    {
        // no action
    }

    /**
     * Prevent from being unserialized.
     *
     * @return void
     */
    private function __wakeup()
    {
        // no action
    }

    /**
     * Retrieve all error messages.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Everything back to default.
     *
     * @return $this
     */
    public function reset()
    {
        $this->errors      = [];
        $this->skipDays    = [];
        $this->skipDates   = [];
        $this->currentDate = new \DateTime();

        return $this;
    }

    /**
     * Overwrite the default current date.
     *
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setCurrentDate(\DateTime $date)
    {
        $this->currentDate = $date;

        return $this;
    }

    /**
     * Retrieve current date.
     *
     * @return \DateTime
     */
    public function getCurrentDate()
    {
        return $this->currentDate;
    }

    /**
     * Week days, you might want to skip.
     *
     * @param array $days Only english days!
     *
     * @return $this
     */
    public function setSkipDays(array $days = ['Saturday', 'Sunday'])
    {
        $days = array_map('strtolower', $days);
        try {
            $this->checkDays($days);

            $this->skipDays = array_unique(array_merge($this->skipDays, $days));
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        return $this;
    }

    /**
     * Skipped days.
     *
     * @return array
     */
    public function getSkipDays()
    {
        return $this->skipDays;
    }

    /**
     * Days of the year, you might want to skip.
     *
     * @param array $dates
     *
     * @return $this
     */
    public function setSkipDates(array $dates)
    {
        $tmp = [];
        foreach ($dates as $date) {
            try {
                $dateTime = new \DateTime($date);
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }

            $tmp[] = $dateTime->format('Ymd');
        }

        if (!empty($tmp)) {
            $this->skipDates = array_unique(array_merge($this->skipDates, $tmp));
        }

        return $this;
    }

    /**
     * Skipped dates.
     *
     * @return array
     */
    public function getSkipDates()
    {
        $dates = [];
        foreach ($this->skipDates as $date) {
            // Human readable format
            $dates[] = preg_replace('/(\d{4})(\d{2})(\d{2})/', '\1-\2-\3', $date);
        }
        return $dates;
    }

    /**
     * Setting Holidays.
     *
     * @param array $days
     *
     * @return $this
     */
    public function setHolidays(array $days)
    {
        $this->setSkipDates($days);

        return $this;
    }

    /**
     * Next business day.
     *
     * @param int $offset
     *
     * @return \DateTime
     * @throws \Exception
     */
    public function getNextWorkDay($offset = 0)
    {
        if (!empty($this->errors)) {
            trigger_error('One or more errors during the data collection.', E_USER_WARNING);
        }

        if (empty($this->skipDays) && empty($this->skipDates)) {
            throw new \Exception('You must enter at least one day or date!');
        }

        $skipOffset = true;
        $i          = 1;
        do {
            $nextDate = clone $this->currentDate;
            $nextDate->add(new \DateInterval("P{$i}D"));
            $day  = strtolower($nextDate->format('l'));
            $date = $nextDate->format('Ymd');

            if ($skipOffset === false) {
                $offset--;
            }

            ++$i;
        } while (($skipOffset = (in_array($date, $this->skipDates) || in_array($day, $this->skipDays))) || $offset > 0);

        return $nextDate;
    }

    /**
     * Check the days of the week.
     *
     * @param array $days
     *
     * @return bool
     * @throws \Exception
     */
    protected function checkDays(array $days)
    {
        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as &$day) {
            if (!in_array($day, $validDays)) {
                throw new \Exception('Invalid day name. (' . $day . ')');
            }
        }

        return true;
    }
}
