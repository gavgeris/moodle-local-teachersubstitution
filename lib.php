<?php
/**
 * Teacher Substitution Plugin library functions
 *
 * @package    local_teachersubstitution
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This function extends the navigation with the teacher substitution item
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param context $context The context of the course
 * @return void|null return null if we don't want to display the node.
 */
function local_teachersubstitution_extend_navigation_course($navigation, $course, $context) {
    // Check if user has capability to substitute teachers
    if (has_capability('local/teachersubstitution:substitute', $context)) {
        $url = new moodle_url('/local/teachersubstitution/index.php');
        $navigation->add(
            get_string('substituteteacher', 'local_teachersubstitution'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'teachersubstitution'
        );
    }
}

/**
 * Get all course categories
 * @return array Array of categories with id and name
 */
function local_teachersubstitution_get_categories() {
    global $DB;
    
    $categories = $DB->get_records('course_categories', ['visible' => 1], 'name', 'id, name');
    $options = [];
    foreach ($categories as $category) {
        $options[$category->id] = $category->name;
    }
    return $options;
}

/**
 * Get courses from a specific category
 * @param int $categoryid Category ID
 * @return array Array of courses with id and fullname
 */
function local_teachersubstitution_get_courses_by_category($categoryid) {
    global $DB;
    
    $courses = $DB->get_records('course', ['category' => $categoryid, 'visible' => 1], 'fullname', 'id, fullname');
    $options = [];
    foreach ($courses as $course) {
        $options[$course->id] = $course->fullname;
    }
    return $options;
}

/**
 * Get teachers from a specific course
 * @param int $courseid Course ID
 * @return array Array of teachers with username and fullname
 */
function local_teachersubstitution_get_teachers_by_course($courseid) {
    global $DB;
    
    $context = context_course::instance($courseid);
    $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
    
    if (!$teacherrole) {
        return [];
    }
    
    $teachers = get_role_users($teacherrole->id, $context, false, 'u.id, u.username, u.firstname, u.lastname, u.email');
    
    $options = [];
    foreach ($teachers as $teacher) {
        $group = local_teachersubstitution_get_user_group($courseid, $teacher->id);
        $groupname = $group ? $group->name : '';
        $fullname = fullname($teacher) . ($groupname ? " ($groupname)" : '');
        $options[$teacher->username] = $fullname;
    }
    return $options;
}

/**
 * Get user's group in a course
 * @param int $courseid Course ID
 * @param int $userid User ID
 * @return object|null Group object or null
 */
function local_teachersubstitution_get_user_group($courseid, $userid) {
    $groups = groups_get_all_groups($courseid, $userid);
    if (!empty($groups)) {
        return array_shift($groups); // Return first group
    }
    return null;
}

/**
 * Get available teachers from epimorfwtes table and teacherplus role
 * @param int $courseid Course ID
 * @return array Array of available teachers
 */
function local_teachersubstitution_get_available_teachers($courseid) {
    global $DB;
    
    $options = [];
    
    // First get teacherplus users from the course
    $context = context_course::instance($courseid);
    $teacherplusrole = $DB->get_record('role', ['shortname' => 'teacherplus']);
    
    if ($teacherplusrole) {
        $teacherplususers = get_role_users($teacherplusrole->id, $context, false, 'u.id, u.username, u.firstname, u.lastname, u.email');
        foreach ($teacherplususers as $user) {
            $fullname = fullname($user) . ' (Επιμορφωτής+)';
            $options[$user->username] = $fullname;
        }
    }
    
    // Then get available teachers from epimorfwtes table
    $sql = "SELECT emailpsd, name, surname
          FROM {epimorfwtes}
         WHERE accepted IS NULL
           AND (finaltmima IS NULL OR finaltmima = '')
      ORDER BY surname, name";
    $availableteachers = $DB->get_records_sql($sql);
    foreach ($availableteachers as $teacher) {
        $fullname = $teacher->name . ' ' . $teacher->surname;
        $options[$teacher->emailpsd] = $fullname;
    }
    
    return $options;
}

/**
 * Send data to webhook
 * @param array $data Data to send
 * @return bool Success status
 */
function local_teachersubstitution_send_to_webhook($data) {
    global $CFG;
    
    require_once($CFG->libdir . '/filelib.php');
    
    $webhookurl = get_config('local_teachersubstitution', 'webhookurl');
    
    if (empty($webhookurl)) {
        return false;
    }
    
    $curl = new curl();
    $curl->setHeader('Content-Type: application/json');
    
    $response = $curl->post($webhookurl, json_encode($data));
    $httpcode = $curl->getInfo(CURLINFO_HTTP_CODE);
    
    return ($httpcode >= 200 && $httpcode < 300);
}