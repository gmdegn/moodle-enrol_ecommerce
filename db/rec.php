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

require_once('../../../config.php');
require_once('../../../course/lib.php');
require_once('../../../lib/filelib.php');

global $DB, $OUTPUT, $PAGE, $COURSE;

if(! $DB->record_exists('enrol_elightenment', array())) {
    $record = new stdClass();
    $record->authkey = optional_param('keyval', null, PARAM_TEXT);;
    $insert = $DB->insert_record('enrol_elightenment', $record, false);
    header("Location: ".$CFG->wwwroot."/enrol/elightenment/shop.php");
    die();
}