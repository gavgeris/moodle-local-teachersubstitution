<?php
/**
 * Teacher Substitution Plugin main page
 *
 * @package    local_teachersubstitution
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');

admin_externalpage_setup('local_teachersubstitution');

$PAGE->set_url(new moodle_url('/local/teachersubstitution/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_teachersubstitution'));
$PAGE->set_heading(get_string('pluginname', 'local_teachersubstitution'));

// Get categories for dropdown
$categories = local_teachersubstitution_get_categories();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey()) {
    $courseid = required_param('courseid', PARAM_INT);
    $currentteacher = required_param('currentteacher', PARAM_TEXT);
    $newteacher = required_param('newteacher', PARAM_TEXT);
    $reason = required_param('reason', PARAM_TEXT);
    
    // Prepare data for webhook
    $webhookdata = [
        'courseid' => $courseid,
        'currentteacher' => $currentteacher,
        'newteacher' => $newteacher,
        'reason' => $reason,
        'timestamp' => time()
    ];
    
    // Send to webhook
    $success = local_teachersubstitution_send_to_webhook($webhookdata);
    
    if ($success) {
        redirect($PAGE->url, get_string('submissionsuccess', 'local_teachersubstitution'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect($PAGE->url, get_string('submissionerror', 'local_teachersubstitution'), null, \core\output\notification::NOTIFY_ERROR);
    }
}

echo $OUTPUT->header();

// Include JavaScript for dynamic dropdowns
$PAGE->requires->js_call_amd('local_teachersubstitution/dropdowns', 'init');

?>

<div class="local_teachersubstitution_container">
    <h2><?php echo get_string('substituteteacher', 'local_teachersubstitution'); ?></h2>
    
    <form id="teachersubstitutionform" method="post" class="mform">
        <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
        
        <div class="form-group row">
            <div class="col-md-3">
                <label for="categoryid"><?php echo get_string('category', 'local_teachersubstitution'); ?></label>
            </div>
            <div class="col-md-9">
                <select name="categoryid" id="categoryid" class="form-control" required>
                    <option value=""><?php echo get_string('selectcategory', 'local_teachersubstitution'); ?></option>
                    <?php foreach ($categories as $id => $name): ?>
                        <option value="<?php echo $id; ?>"><?php echo s($name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group row">
            <div class="col-md-3">
                <label for="courseid"><?php echo get_string('course', 'local_teachersubstitution'); ?></label>
            </div>
            <div class="col-md-9">
                <select name="courseid" id="courseid" class="form-control" required disabled>
                    <option value=""><?php echo get_string('selectcourse', 'local_teachersubstitution'); ?></option>
                </select>
            </div>
        </div>
        
        <div class="form-group row">
            <div class="col-md-3">
                <label for="currentteacher"><?php echo get_string('currentteacher', 'local_teachersubstitution'); ?></label>
            </div>
            <div class="col-md-9">
                <select name="currentteacher" id="currentteacher" class="form-control" required disabled>
                    <option value=""><?php echo get_string('selectcurrentteacher', 'local_teachersubstitution'); ?></option>
                </select>
            </div>
        </div>
        
        <div class="form-group row">
            <div class="col-md-3">
                <label for="newteacher"><?php echo get_string('newteacher', 'local_teachersubstitution'); ?></label>
            </div>
            <div class="col-md-9">
                <select name="newteacher" id="newteacher" class="form-control" required disabled>
                    <option value=""><?php echo get_string('selectnewteacher', 'local_teachersubstitution'); ?></option>
                </select>
            </div>
        </div>
        
        <div class="form-group row">
            <div class="col-md-3">
                <label for="reason"><?php echo get_string('reason', 'local_teachersubstitution'); ?></label>
            </div>
            <div class="col-md-9">
                <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
            </div>
        </div>
        
        <div class="form-group row">
            <div class="col-md-9 offset-md-3">
                <button type="submit" class="btn btn-primary" id="submitbtn" disabled>
                    <?php echo get_string('substitute', 'local_teachersubstitution'); ?>
                </button>
            </div>
        </div>
    </form>
</div>

<?php
echo $OUTPUT->footer();