<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require dirname(__FILE__) . '/../src/BalkuTamas/WorkDays.php';

$date = new DateTime();
if (isset($_POST['date'])) {
    try {
        $overwrite = new DateTime($_POST['date']);
        $date      = $overwrite;
    } catch (Exception $e) {
        // remains the default
    }
}

$wd = \BalkuTamas\WorkDays::instance()
    ->setCurrentDate($date);

$offset = 0;
if (isset($_POST['offset']) && $_POST['offset'] > 0) {
    $offset = (int)$_POST['offset'];
}

if (isset($_POST['days']) && is_array($_POST['days'])) {
    $wd->setSkipDays($_POST['days']);
} else {
    $wd->setSkipDays();
}

if (!empty($_POST['dates'])) {
    $wd->setSkipDates((array)explode(',', $_POST['dates']));
} else {
    $wd->setSkipDates(['2014-09-23', '2014-12-24']);
}

?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WorkDays</title>
    <style>
        #wrap {
            width: 500px;
            margin: 0 auto
        }

        .text-center {
            text-align: center
        }

        label {
            display: block
        }

        input[type="number"] {
            width: 40px
        }
    </style>
</head>
<body>
<div id="wrap">
    <form method="post">
        <h1 class="text-center">WorkDays</h1>
        <label>
            <em>Calculate from date (YYYY-MM-DD):</em>
            <input type="date" name="date" value="<?php echo $wd->getCurrentDate()->format('Y-m-d') ?>">
        </label>
        <label>
            <em>Offset from date:</em>
            <input type="number" name="offset" value="<?php echo (int)$offset ?>" min="0">
        </label>
        <?php $skippedDates = $wd->getSkipDates(); ?>
        <label>
            <em>Skip dates (comma separated):</em>
            <input type="input" name="dates" value="<?php echo implode(',', $skippedDates) ?>">
        </label>
        <em>Skip days:</em>
        <?php
        $skippedDays = $wd->getSkipDays();
        $exampleDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($exampleDays as $day) {
            ?>
            <label>
                <input type="checkbox" name="days[]" value="<?php echo $day ?>"
                       <?php if (in_array($day, $skippedDays)) { ?>checked<?php } ?>> <?php echo ucfirst($day) ?>
            </label>
        <?php } ?>
        <button type="submit">CALC!</button>
    </form>
    <p>
        <em>
            Skipped days: <?php echo $skippedDays ? implode(', ', $skippedDays) : 'no' ?><br>
            Skipped dates: <?php echo $skippedDates ? implode(', ', $skippedDates) : 'no' ?>
        </em>
    </p>

    <p class="text-center">
        Next workday:
        <strong><?php echo $wd->getNextWorkDay((int)$offset)->format('Y.m.d (l)') ?></strong>
    </p>
</div>
</body>
</html>
