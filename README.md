# WorkDays

At first, sorry for my english!

The WorkDays class are calculate the next workdays. It's available to ignore specific days (monday, sunday, etc) or dates (2014-09-01, 2015-01-01, etc.) and the class are process with these data.

## Server Requirements

* PHP version 5.4 or higher.

## Installation

Simple drop `WorkDays.php` in any project and call `require 'WorkDays.php';` in your
project. You can then access the `WorkDays` class.

### Example

    $wd = WorkDays::instance()
        ->setSkipDays()
        ->setSkipDates(['2014-09-23', '2014-12-24']);
    echo "Next workday: " . $wd->getNextWorkDay()->format('Y-m-d'); // Next workday: 2014-09-24

## Resources

* [Documentation](https://github.com/BalkuTamas/WorkDays)
