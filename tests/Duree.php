<?php

require_once 'testing.php';
require_once 'model/Duree.php';

test_case('0 years 0 mons 3 days 4 hours 5 mins 6.0 secs', days: 3, hours: 4, mins: 5, secs: 6);
test_case('0 years 0 mons 3 days 4 hours 5 mins 6 secs', days: 3, hours: 4, mins: 5, secs: 6);
test_case('3 years 3 mons 700 days 133 hours 17 mins 36.789 secs', years: 3, mons: 3, days: 700, hours: 133, mins: 17, secs: 36.789);
test_case('0 years 0 mons 0 days 0 hours 0 mins 0.0 secs');

/**
 * Cas de test nominal.
 * @param string $output
 * @return void
 */
function test_case(
    string $output,
    int $years = 0,
    int $mons = 0,
    int $days = 0,
    int $hours = 0,
    int $mins = 0,
    float $secs = 0,
): void {
    $d = Duree::parse($output);
    assert_strictly_equal($d->years, $years);
    assert_strictly_equal($d->months, $mons);
    assert_strictly_equal($d->days, $days);
    assert_strictly_equal($d->hours, $hours);
    assert_strictly_equal($d->minutes, $mins);
    assert_strictly_equal($d->seconds, $secs);

    $roundtripped = Duree::parse($d);
    assert_equal($d, $roundtripped);
}

