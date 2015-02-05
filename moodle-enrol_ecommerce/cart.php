<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Elightenment ecommerce enrolment plugin.
 *
 * This plugin allows you to set up a course shop and shopping cart
 *
 * @package    enrol_ecommerce
 * @copyright  2015 Gary McKnight
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->wwwroot .'/course/lib.php');
require_once($CFG->libdir .'/filelib.php');

session_name('MoodleSession');
session_start();

global $DB, $OUTPUT, $PAGE, $USER;

//user must be logged in for purchasing classes to work
require_login();

//set up moodle page
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('cartTitle', 'enrol_ecommerce'));
$PAGE->set_heading(get_string('cartTitle', 'enrol_ecommerce'));
$PAGE->set_url($CFG->wwwroot.'/enrol/ecommerce/cart.php');
$PAGE->set_cacheable(false);

//check to see if the Cart has bee created yet. If not, create it; otherwise
//push the course ID into the array. Then check to make sure the course hasn't been
//added twice by mistake. If it has, erase any duplicate values.
if(!isset($_SESSION['courseCart'])){
	$_SESSION['courseCart'] = array();
} else if(isset($_POST['id'])) {
	array_push($_SESSION['courseCart'], $_POST['id']);
	$_SESSION['courseCart'] = array_unique($_SESSION['courseCart']);
}
	//This will sort the array so that classes are in ascending order by their ID values.
	asort($_SESSION['courseCart']);
	

$courses = get_courses();
$total = 0;
$IDa = array();
$NAMEa = array();
$AMTa = array();
$URLstring = $CFG->wwwroot;
$keyDB = $DB->get_records('enrol_ecommerce');
$press = '';
				
foreach($keyDB as $record){
	$authKey = $record->authkey;
}

//If 'remove' was clicked, this will find the key of the id value and unset it.
//Then it will redo the SESSION array so that it doesn't have any blank spaces.
if(isset($_POST['remove'])){
	$key = array_search($_POST['remove']);
	unset($_SESSION['courseCart']);
	$_SESSION['courseCart'] = array_values($_SESSION['courseCart']);
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

	if(empty($_SESSION['courseCart'])){
		echo '<div class="coursebox"><strong><br>Your Cart Is Empty!<hr></strong></div>';
		$press = 'disabled';
	}
	
	
	foreach($_SESSION['courseCart'] as $courseid){
		//This is a really innefficient way to find out which course was selected using it's ID. Ideally I would simply search the database of courses by ID and pull up the record,
		//but it isn't cooperating. Will be fixed later.
		foreach($courses as $search){
			if($search->id == $courseid){
				$found = $search;
			}
		}
		//find the cost of the course from the moodle database.
		$sql = 'SELECT cost FROM {enrol} WHERE courseid = '.$found->id.' AND enrol = "ecommerce" ';
		$cost = $DB->get_record_sql($sql, array(1));
		
		echo '
			<div class="coursebox">
				<div class="title">
					<strong>'.$found->fullname.'</strong>
					<b>$'.$cost->cost.' </b>
				</div>
				<div class="buttons"> 
					<form method="POST" action="#">
						<input type="hidden" name="remove" value="'.$found->id.'">
						<input type="submit" value="'.get_string('removeCourse', 'enrol_ecommerce').'">
					</form>
				</div>
			</div>';
		
		$total += $cost->cost;
		
		array_push($IDa, $found->id);
		array_push($NAMEa, $found->fullname);
		array_push($AMTa, $cost->cost);
	}
	$NAMEs = base64_encode(json_encode($NAMEa));
	$IDs = base64_encode(json_encode($IDa));
	$AMTs = base64_encode(json_encode($AMTa));
	
	echo '<br>
	<div class="navbar-inner" id="totals">
		<b id="ignore">Total: </b>$'.$total.'
		<br>
		<form method="POST" action="http://elightenmentlearning.com/payment/paypal.php">
			<input type="hidden" name="pID" value="'.$IDs.'">
			<input type="hidden" name="pName" value="'.$NAMEs.'">
			<input type="hidden" name="amt" value="'.$AMTs.'">
			<input type="hidden" name="siteURL" value="'.$URLstring.'">
			<input type="hidden" name="authKey" value="'.$authKey.'">
			<input type="hidden" name="uID" value="'.$USER->id.'">
			<input type="submit" value="'.get_string('purchase', 'enrol_ecommerce').'" '.$press.'>
		</form>
	</div>
	</div>';

//-----DEBUG STUFF! Don't uncomment unless you want all your users to see all your course and session data!------//
/*
print_r($_SESSION['courseCart']);
echo'<br>';
print_r($coursecontext);
echo'<br>';
print_r($courses);
echo'<br>';
print_r($cost);
echo $cost->cost;
*/
//------ END DEBUG STUFF!---------------------------------------------------------------------------------------//

echo $OUTPUT->footer();

?>
