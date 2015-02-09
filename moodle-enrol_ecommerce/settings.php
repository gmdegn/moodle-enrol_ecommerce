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

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // --- settings ------------------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_ecommerce_settings', '', get_string('pluginname_desc', 'enrol_ecommerce')));

    $settings->add(new admin_setting_configcheckbox('enrol_ecommerce/mailstudents', get_string('mailstudents', 'enrol_ecommerce'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_ecommerce/mailteachers', get_string('mailteachers', 'enrol_ecommerce'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_ecommerce/mailadmins', get_string('mailadmins', 'enrol_ecommerce'), '', 0));

    //  Note: let's reuse the ext sync constants and strings here, internally it is very similar,
    //        it describes what should happen when users are not supposed to be enrolled any more.
    $options = array(
        ENROL_EXT_REMOVED_KEEP           => get_string('extremovedkeep', 'enrol'),
        ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'),
        ENROL_EXT_REMOVED_UNENROL        => get_string('extremovedunenrol', 'enrol'),
    );
    $settings->add(new admin_setting_configselect('enrol_ecommerce/expiredaction', get_string('expiredaction', 'enrol_ecommerce'), get_string('expiredaction_help', 'enrol_ecommerce'), ENROL_EXT_REMOVED_SUSPENDNOROLES, $options));

    // --- enrol instance defaults ----------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_ecommerce_defaults',
        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    $settings->add(new admin_setting_configtext('enrol_ecommerce/cost', get_string('cost', 'enrol_ecommerce'), '', 0, PARAM_FLOAT, 4));

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect('enrol_ecommerce/roleid',
            get_string('defaultrole', 'enrol_ecommerce'), get_string('defaultrole_desc', 'enrol_ecommerce'), $student->id, $options));
    }

    $settings->add(new admin_setting_configduration('enrol_ecommerce/enrolperiod',
        get_string('enrolperiod', 'enrol_ecommerce'), get_string('enrolperiod_desc', 'enrol_ecommerce'), 0));
}
