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
 * Elightenment ecommerce enrolment plugin.
 *
 * This plugin allows you to set up a course shop and shopping cart
 *
 * @package    enrol_ecommerce
 * @copyright  2015 Gary McKnight
 * @license    http:// www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('../../course/lib.php');
require_once('../../lib/filelib.php');
//require_once($CFG->wwwroot .'/course/lib.php');
//require_once($CFG->libdir .'/filelib.php');

session_name('MoodleSession');
session_start();

global $DB, $OUTPUT, $PAGE, $COURSE;

// user must be logged in for purchasing classes to work
require_login();

// set up moodle page
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('shopTitle', 'enrol_ecommerce'));
$PAGE->set_heading(get_string('shopTitle', 'enrol_ecommerce'));
$PAGE->set_url($CFG->wwwroot.'/enrol/ecommerce/shop.php');
$PAGE->set_cacheable(false);

$plugin = enrol_get_plugin('ecommerce');

// check to see if the Cart has bee created yet. If not, create it; otherwise
// push the course ID into the array. Then check to make sure the course hasn't been
// added twice by mistake. If it has, erase any duplicate values.
if (!isset($_SESSION['courseCart'])){
    $_SESSION['courseCart'] = array();
} else if (isset($_POST['id'])) {
    array_push($_SESSION['courseCart'], $_POST['id']);
    $_SESSION['courseCart'] = array_unique($_SESSION['courseCart']);
}

// --search bar-------------------------------------

$sql = 'SELECT name, id FROM {course_categories}';
$ccat = $DB->get_records_sql($sql, array(1));

// -------------------------------------------------

echo $OUTPUT->header();

// css for the page. I don't recommend fiddling with this. If you need to make style changes, do it to the theme.
// This is what makes the courses slide down. To avoid using javascript, the divs were given a tab index and then :focus was set so that it will expand upon being clicked.
// !--NOTICE--!  If you want to change the styling on the course boxes, use "shopHeader" and "ahopDesc" as they are unique to this page and will not affect the rest of the site.
echo '
<style>
.storeInst {
    display: inline-block;
    vertical-align: top;
    text-align: center;
    padding .5%;
    width: 95%;
    height: 100%;
    margin: 0 1% 0 0;
    overflow: hidden;
}
.storeInst:focus .coursebox {
    max-height: 500px;
    overflow: auto;
    outline: none;
    outline:0;
}
.plusMark{
    float: right;
    display:inline;
    padding-right: 5px;
    text-align: right;
    color: #668080;
    font-weight: bold;
}
.coursebox {
    word-wrap: break-word;
    padding: 1%;
    padding-top: 0;
    display: block;
    max-height: 0;
    overflow: hidden;
    transition: all 2s ease-in-out;
}
.title{
    padding-top: 1%;
    padding-bottom: 0.5;
    margin-bottom: 0;
}
#courseRegion {
    width: 100%;
    height: 100%;
}
#checkout {
    width: 100%;
    text-align: right;
    font-size: 97%;
    font-weight: bold;
    float: right;
    margin-bottom: -.5%;
}
#search {
    display: block-inline;
}
#cButt {
    border-radius: 50px;
    transition: all .5s ease-in-out;
}
#cButt:hover {
    color: blue;
    transition: all .5s ease-in-out;
}
#shopTitle {
    font-size: 200%;
    font-weight: bold;
    border-style: solid;
    border-width: 0;
    border-bottom: 1px;
    width: 100%;
}
.buttons {
    text-align: right;
}
strong {
    font-size: 150%;
}
b {
    font-size: 120%;
}
td {
    width: 40%;
    padding-bottom: 1%;
}
select {
    margin-right: 1%;
}
</style>';

echo '<div class="content">';

if (file_exists($CFG->wwwroot.'/enrol/ecommerce/pics/checkout.png')){
    $checkoutstr = $CFG->wwwroot.'/enrol/ecommerce/pics/checkout.png';
} else {
    $checkoutstr = '<button id="cButt">'.get_string('cOutBttn', 'enrol_ecommerce').'</button>';
}

$carturl = $CFG->wwwroot.'/enrol/ecommerce/cart.php';
echo '
<div id="checkout">
    <div id="search">
        <form method="GET" action="?">
            <select name="cat">
                <option value="null" selected disabled>'.get_string('catSearch', 'enrol_ecommerce').'</option>';
                foreach ($ccat as $y) {
                    echo '<option value="'.$y->id.'">'.$y->name.'</option>';
                }
            echo' </select>
            <input type="text" name="name" placeholder="'.get_string('nameSearch', 'enrol_ecommerce').'" autocomplete="on">
            <input type="submit" value="'.get_string('search', 'enrol_ecommerce').'">
        </form>
    </div>
    <a href="'.$carturl.'">'.$checkoutstr.' </a>
</div>';

// first get list of courses
$courses = get_courses();
$courses = array_reverse($courses);
$x = 0;

// table is there to get the courses to line up correctly.
echo '<table>
    <th colspan="2"><div id="shopTitle">Available Courses:<hr></th>';

/* for each course in the catalog (excluding the front page which has an id of '1' and any course that are hidden)
print the course fullname, id, description and a button to add to cart */
foreach ($courses as $course){

    // there are two courses per row, so every other course beginning with the first will start a new row.
    $test = $x % 2;
    if ( $test == 0 ){
        echo '<tr>';
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $enrolled = is_enrolled($context, $USER->id, '', true);

    $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', null);

    $sql = 'SELECT cost FROM {enrol} WHERE courseid = '.$course->id.' AND enrol = "ecommerce" ';
    $cost = $DB->get_record_sql($sql, array(1));

    $csearch = '*';
    $nsearch = '*';
    if (isset($_GET['cat'])){
        $csearch = $_GET['cat'];
    }
    if (isset($_GET['name'])){
        if (strpos(strtolower($course->fullname), strtolower ($_GET['name'])) !== false){
            $namesearch = true;
        } else {
            $namesearch = false;
        }
    } else {
        $namesearch = true;
    }
    $buttonString = '';
    
    $sql = 'SELECT customint1 FROM {enrol} WHERE courseid = '.$course->id.' AND enrol = "ecommerce" ';
    $subval = $DB->get_record_sql($sql, array(1));

    foreach($DB->get_records('enrol_ecommerce') as $record){
        $authkey = $record->authkey;
    }
    
    // Don't display the front page, don't display hidden courses and do not display a course that hasn't had a price set up yet.
    if ($course->id != 1 && $course->visible == 1 && $cost->cost != null && fnmatch($csearch, $course->category) && $namesearch){

        echo '<td><div class="storeInst" tabindex="0" id="'.$x.'"><div class="navbar-inner shopHeader"><div class="title"><strong>'.$course->fullname.'
        </strong><div class="plusMark">[+] </div></div></div><div class="coursebox shopDesc"><p>'.$summary.'</p><div class="buttons">';

        // Shop will not allow users that are already enroled in that course to purchase it again. Once the user is no longer enroled, they may purchase the course again.
        if ($enrolled){ echo '<b>You are already enrolled in this course!</b></div></div><br>';

        // Shop will not allow you to add course to cart twice. If this fails, it will still erase the duplicate entry.
        } else if (in_array($course->id, $_SESSION['courseCart'])){
            echo '<b>Added to Cart!</b></div></div><br>';

        // To avoid using javascript, clicking the "add to cart" button will refresh the page with $_POST
        // variables to add to the $_SESSION array. It will then jump back to the course which was clicked.
        } else{
            if($subval->customint1 == 1){
                echo '<form method="POST" action="http://elightenmentlearning.com/payment/paypal.php"><b>$'.$cost->cost.' </b>
                <input type="hidden" name="pID" value="'.base64_encode(json_encode(array($course->id))).'">
                <input type="hidden" name="pName" value="'.base64_encode(json_encode(array($course->fullname))).'">
                <input type="hidden" name="amt" value="'.base64_encode(json_encode(array($cost->cost))).'">
                <input type="hidden" name="siteURL" value="'.$CFG->wwwroot.'">
                <input type="hidden" name="authkey" value="'.$authkey.'">
                <input type="hidden" name="uID" value="'.$USER->id.'">
                <input type="hidden" name="subsc" value="true">
                <input type="hidden" name="subLen" value="'.$plugin->get_config('enrolperiod').'">
                <input type="submit" value="'.get_string('subscribe', 'enrol_ecommerce').'"></form></div></div></div><br>';
            } else {
                echo '<form method="POST" action="#'.$x.'"><b>$'.$cost->cost.' </b>
                <input type="hidden" name="id" value="'.$course->id.'"><input type="submit" value="'.get_string('sendpaymentbutton', 'enrol_ecommerce').'"></form></div></div></div><br>'; }
            }
        echo '</td>';
        $x++;
        
    }
    if ( $test != 0 ){
        echo '</tr>';
    }
}
    if (isset($_GET['name']) && $x == 0){
        echo '<td><div class="storeInst">No courses met your search criteria!</div></td></tr>';
    }
echo '</table>';
// -----DEBUG STUFF! Don't uncomment unless you want all your users to see all your course and session data!------//
/*

echo '<div style="width: 80%; height: 200px; overflow-y: auto; overflow-x: hidden; margin-left:-5%;">';
print_r($courses);
echo '</div><br><br>';

echo '<form method="POST" action-"#"><input type="hidden" name="reset" value="1"><input type="submit" value="RESET"></form>';
if($_POST['reset'] == 1){
    $_SESSION['courseCart'] = array();
}
print_r($_SESSION['courseCart']);

*/
// ------ END DEBUG STUFF!---------------------------------------------------------------------------------------//

echo $OUTPUT->footer();

