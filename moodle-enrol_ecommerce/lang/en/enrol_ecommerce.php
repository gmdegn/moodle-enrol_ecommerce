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

$string['emptyCart'] = 'Your Cart is Empty';
$string['removeCourse'] = 'Remove';
$string['purchase'] = 'Purchase';
$string['catSearch'] = 'Search by Category';
$string['nameSearch'] = 'Search by Course Name';
$string['search'] = 'Search';
$string['cOutBttn'] = 'Checkout';
$string['assignrole'] = 'Assign role';
$string['shopTitle'] = 'Course Catalog';
$string['cartTitle'] = 'Course Cart';
$string['businessemail'] = 'Paypal Email';
$string['businessemail_desc'] = '';
$string['cost'] = 'Enrol cost';
$string['costerror'] = 'The enrolment cost is not numeric';
$string['costorkey'] = 'Please choose one of the following methods of enrolment.';
$string['currency'] = 'Currency Code';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during enrolments';
$string['subscribe'] = 'Subscription';
$string['subscribe_desc'] = 'Check if this is a subscription';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can be enrolled until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolperiod'] = 'Enrolment/Subscription duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. This must be set if this course is a subscription!';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user is enrolled. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can be enrolled from this date onward only.';
$string['expiredaction'] = 'Enrolment expiration action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['mailadmins'] = 'Notify admin';
$string['mailstudents'] = 'Notify students';
$string['mailteachers'] = 'Notify teachers';
$string['nocost'] = 'There is no cost for this course!';
$string['ecommerce:config'] = 'Configure enrol instances';
$string['ecommerce:manage'] = 'Manage enrolled users';
$string['ecommerce:unenrol'] = 'Unenrol users from course';
$string['ecommerce:unenrolself'] = 'Unenrol self from the course';
$string['pluginname'] = 'Ecommerce';
$string['pluginname_desc'] = 'Elightenment Learning payment system';
$string['sendpaymentbutton'] = 'Add to Cart';
$string['status'] = 'Allow paid enrolments';
$string['status_desc'] = 'Allow users to use Ecommerce to enrol into a course by default.';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
