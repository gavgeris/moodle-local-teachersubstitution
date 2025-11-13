<?php
/**
 * Teacher Substitution Plugin external API
 *
 * @package    local_teachersubstitution
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_teachersubstitution;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once(__DIR__ . '/../lib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;

/**
 * External functions for teacher substitution
 */
class external extends external_api {

    /**
     * Returns description of get_courses parameters
     * @return external_function_parameters
     */
    public static function get_courses_parameters() {
        return new external_function_parameters([
            'categoryid' => new external_value(PARAM_INT, 'Category ID')
        ]);
    }

    /**
     * Get courses by category
     * @param int $categoryid
     * @return array
     */
    public static function get_courses($categoryid) {
        $params = self::validate_parameters(self::get_courses_parameters(), ['categoryid' => $categoryid]);
        
        $courses = local_teachersubstitution_get_courses_by_category($params['categoryid']);
        
        $result = [];
        foreach ($courses as $id => $fullname) {
            $result[] = [
                'id' => $id,
                'fullname' => $fullname
            ];
        }
        
        return ['courses' => $result];
    }

    /**
     * Returns description of get_courses return values
     * @return external_single_structure
     */
    public static function get_courses_returns() {
        return new external_single_structure([
            'courses' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Course ID'),
                    'fullname' => new external_value(PARAM_TEXT, 'Course fullname')
                ])
            )
        ]);
    }

    /**
     * Returns description of get_teachers parameters
     * @return external_function_parameters
     */
    public static function get_teachers_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID')
        ]);
    }

    /**
     * Get teachers by course
     * @param int $courseid
     * @return array
     */
    public static function get_teachers($courseid) {
        global $USER;
        
        $params = self::validate_parameters(self::get_teachers_parameters(), ['courseid' => $courseid]);
        
        // Check if user can view this course
        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);
        
        if (!has_capability('moodle/course:viewhiddencourses', $context) && !is_enrolled($context, $USER->id)) {
            throw new \moodle_exception('nopermissiontoviewcourse');
        }
        
        $teachers = local_teachersubstitution_get_teachers_by_course($params['courseid']);
        
        $result = [];
        foreach ($teachers as $username => $fullname) {
            $result[] = [
                'username' => $username,
                'fullname' => $fullname
            ];
        }
        
        return ['teachers' => $result];
    }

    /**
     * Returns description of get_teachers return values
     * @return external_single_structure
     */
    public static function get_teachers_returns() {
        return new external_single_structure([
            'teachers' => new external_multiple_structure(
                new external_single_structure([
                    'username' => new external_value(PARAM_TEXT, 'Teacher username'),
                    'fullname' => new external_value(PARAM_TEXT, 'Teacher fullname')
                ])
            )
        ]);
    }

    /**
     * Returns description of get_available_teachers parameters
     * @return external_function_parameters
     */
    public static function get_available_teachers_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID')
        ]);
    }

    /**
     * Get available teachers for substitution
     * @param int $courseid
     * @return array
     */
    public static function get_available_teachers($courseid) {
        global $USER;
        
        $params = self::validate_parameters(self::get_available_teachers_parameters(), ['courseid' => $courseid]);
        
        // Check if user can view this course
        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);
        
        if (!has_capability('moodle/course:viewhiddencourses', $context) && !is_enrolled($context, $USER->id)) {
            throw new \moodle_exception('nopermissiontoviewcourse');
        }
        
        $teachers = local_teachersubstitution_get_available_teachers($params['courseid']);
        
        $result = [];
        foreach ($teachers as $username => $fullname) {
            $result[] = [
                'username' => $username,
                'fullname' => $fullname
            ];
        }
        
        return ['availableteachers' => $result];
    }

    /**
     * Returns description of get_available_teachers return values
     * @return external_single_structure
     */
    public static function get_available_teachers_returns() {
        return new external_single_structure([
            'availableteachers' => new external_multiple_structure(
                new external_single_structure([
                    'username' => new external_value(PARAM_TEXT, 'Teacher username'),
                    'fullname' => new external_value(PARAM_TEXT, 'Teacher fullname')
                ])
            )
        ]);
    }
}