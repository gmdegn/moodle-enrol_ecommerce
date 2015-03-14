<?php
//  This file is part of Moodle - http:// moodle.org/
//
//  Moodle is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  Moodle is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Moodle.  If not, see <http:// www.gnu.org/licenses/>.

/**
 * elightenment elightenment enrolment plugin.
 *
 * This plugin allows you to set up a course shop and shopping cart
 *
 * @package    enrol_elightenment
 * @copyright  2015 Gary McKnight
 * @license    http:// www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('../../course/lib.php');
require_once('../../lib/filelib.php');

global $DB, $OUTPUT, $PAGE, $USER;

// user must be logged in for purchasing classes to work
require_login();

// set up moodle page
$PAGE->set_url($CFG->wwwroot.'/enrol/elightenment/cart.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('cartTitle', 'enrol_elightenment'));
$PAGE->set_heading(get_string('cartTitle', 'enrol_elightenment'));
$PAGE->set_cacheable(false);

// check to see if the Cart has bee created yet. If not, create it; otherwise
// push the course ID into the array. Then check to make sure the course hasn't been
// added twice by mistake. If it has, ignore any duplicate values.
$getid = optional_param('id', null, PARAM_INT);
$cartstring = $DB->get_record('enrol_elightenment_cart', array('uid' => $USER->id));
$cartstringarray = json_decode(base64_decode($cartstring->cartvalues));
if (! $cartstringarray) {
    $cartstringarray = array();
}
// This will sort the array so that classes are in ascending order by their ID values.
asort($cartstringarray);

$authrec = $DB->get_record('enrol_elightenment', array(), 'authkey');
$authkey = $authrec->authkey;

$courses = get_courses();
$total = 0;
$idarray = array();
$namearray = array();
$amtarray = array();

// If 'remove' was clicked, this will find the key of the id value and unset it.
// Then it will redo the SESSION array so that it doesn't have any blank spaces.
$getremove = optional_param('remove', null, PARAM_INT);
if (! empty($getremove)) {
    $key = array_search($getremove, $cartstringarray);
    unset($cartstringarray[$key]);
    $cartstringarray = array_values($cartstringarray);
    $dataobject = new stdClass();
    $dataobject->id = $cartstring->id;
    $dataobject->cartvalues = base64_encode(json_encode($cartstringarray));
    $DB->update_record('enrol_elightenment_cart', $dataobject);
}

echo $OUTPUT->header();

echo '
<style>
.content {
    display: inline-block;
    vertical-align: top;
    text-align: center;
    padding .5%;
    width: 100%;
    height: 100%;
    margin: 0 1% 0 0;
    overflow: hidden;
}
.coursebox {
    word-wrap: break-word;
    padding: 1%;
    padding-top: 0;
    display: block;
    height: auto;
    overflow: hidden;
    transition: all 2s ease-in-out;
}
#totals {
    text-align: right;
    padding: .5%;
    padding-top: 0;
    margin-top: 0;
    display: block-inline;
    float: right;
}
#region-main {
        border-style: hidden;
}
.title {
    padding-top: 1%;
}
.buttons {
    text-align: right;
    display: block-inline;
    float: right;
    margin-bottom: -1%;
}
strong {
    font-size: 150%;
}
b:not(#ignore) {
    font-size: 120%;
    float: right;
}
</style>';

echo '<div class="content">';

if (empty($cartstringarray)) {
    echo '<div class="coursebox"><strong><br>Your Cart Is Empty!<hr></strong></div>';
    $press = 'disabled';
} else {
    $press = '';
}

foreach ($cartstringarray as $courseid) {
    foreach ($courses as $search) {
        if ($search->id == $courseid) {
            $found = $search;
        }
    }
    // Find the cost of the course from the moodle database.
    $evars = $DB->get_record('enrol', array('courseid' => $found->id, 'enrol' => 'elightenment'));

    echo '
        <div class="coursebox">
            <div class="title">
                <strong>'.$found->fullname.'</strong>
                <b>$'.$evars->cost.' </b>
                </div>
                <div class="buttons">
                <form method="POST" action="#">
                    <input type="hidden" name="remove" value="'.$found->id.'">
                    <input type="submit" value="'.get_string('removeCourse', 'enrol_elightenment').'">
                </form>
            </div>
        </div>';

    $total += $evars->cost;

    array_push($idarray, $found->id);
    array_push($namearray, $found->fullname);
    array_push($amtarray, $evars->cost);
}
$names = base64_encode(json_encode($namearray));
$ids = base64_encode(json_encode($idarray));
$amts = base64_encode(json_encode($amtarray));

echo '<br>
    <div class="navbar-inner" id="totals">
        <b id="ignore">Total: </b>$'.$total.'
        <br>
        <form method="POST" action="http://elightenmentlearning.com/payment/paypal.php">
            <input type="hidden" name="pID" value="'.$ids.'">
            <input type="hidden" name="pName" value="'.$names.'">
            <input type="hidden" name="amt" value="'.$amts.'">
            <input type="hidden" name="siteURL" value="'.$CFG->wwwroot.'">
            <input type="hidden" name="authkey" value="'.$authkey.'">
            <input type="hidden" name="uID" value="'.$USER->id.'">
            <input type="submit" value="'.get_string('purchase', 'enrol_elightenment').'" '.$press.'>
        </form>
    </div>
    </div>';

echo $OUTPUT->footer();
