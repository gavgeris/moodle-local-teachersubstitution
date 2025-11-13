<?php
/**
 * Teacher Substitution Plugin web services
 *
 * @package    local_teachersubstitution
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_teachersubstitution_get_courses' => [
        'classname' => 'local_teachersubstitution\external',
        'methodname' => 'get_courses',
        'classpath' => 'local/teachersubstitution/classes/external.php',
        'description' => 'Get courses by category',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'moodle/course:viewhiddencourses'
    ],
    
    'local_teachersubstitution_get_teachers' => [
        'classname' => 'local_teachersubstitution\external',
        'methodname' => 'get_teachers',
        'classpath' => 'local/teachersubstitution/classes/external.php',
        'description' => 'Get teachers by course',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'moodle/course:viewhiddencourses'
    ],
    
    'local_teachersubstitution_get_available_teachers' => [
        'classname' => 'local_teachersubstitution\external',
        'methodname' => 'get_available_teachers',
        'classpath' => 'local/teachersubstitution/classes/external.php',
        'description' => 'Get available teachers for substitution',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'moodle/course:viewhiddencourses'
    ]
];