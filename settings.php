<?php
/**
 * Teacher Substitution Plugin settings
 *
 * @package    local_teachersubstitution
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_teachersubstitution',
        get_string('pluginname', 'local_teachersubstitution')
    );
    
    $settings->add(new admin_setting_configtext(
        'local_teachersubstitution/webhookurl',
        get_string('webhookurl', 'local_teachersubstitution'),
        get_string('webhookurldesc', 'local_teachersubstitution'),
        '',
        PARAM_URL
    ));
    
    $ADMIN->add('localplugins', $settings);
}