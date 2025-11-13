/**
 * Teacher Substitution Plugin - Dynamic dropdown functionality
 *
 * @package    local_teachersubstitution
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    
    var init = function() {
        var categorySelect = $('#categoryid');
        var courseSelect = $('#courseid');
        var currentTeacherSelect = $('#currentteacher');
        var newTeacherSelect = $('#newteacher');
        var submitBtn = $('#submitbtn');
        
        // Category change handler
        categorySelect.on('change', function() {
            var categoryId = $(this).val();
            
            // Reset dependent dropdowns
            courseSelect.val('').prop('disabled', true);
            currentTeacherSelect.val('').prop('disabled', true);
            newTeacherSelect.val('').prop('disabled', true);
            submitBtn.prop('disabled', true);
            
            if (categoryId) {
                loadCourses(categoryId);
            }
        });
        
        // Course change handler
        courseSelect.on('change', function() {
            var courseId = $(this).val();
            
            // Reset dependent dropdowns
            currentTeacherSelect.val('').prop('disabled', true);
            newTeacherSelect.val('').prop('disabled', true);
            submitBtn.prop('disabled', true);
            
            if (courseId) {
                loadTeachers(courseId);
            }
        });
        
        // Current teacher change handler
        currentTeacherSelect.on('change', function() {
            var courseId = courseSelect.val();
            
            // Reset new teacher dropdown
            newTeacherSelect.val('').prop('disabled', true);
            submitBtn.prop('disabled', true);
            
            if ($(this).val() && courseId) {
                loadAvailableTeachers(courseId);
            }
        });
        
        // New teacher change handler
        newTeacherSelect.on('change', function() {
            if ($(this).val() && currentTeacherSelect.val()) {
                submitBtn.prop('disabled', false);
            } else {
                submitBtn.prop('disabled', true);
            }
        });
        
        // Form submission handler
        $('#teachersubstitutionform').on('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                Notification.alert('Error', 'Please fill in all required fields.');
            }
        });
        
        function loadCourses(categoryId) {
            Ajax.call([{
                methodname: 'local_teachersubstitution_get_courses',
                args: { categoryid: parseInt(categoryId) },
                done: function(response) {
                    courseSelect.empty().append('<option value="">' + 
                        M.util.get_string('selectcourse', 'local_teachersubstitution') + '</option>');
                    
                    if (response.courses && response.courses.length > 0) {
                        $.each(response.courses, function(index, course) {
                            courseSelect.append('<option value="' + course.id + '">' + 
                                course.fullname + '</option>');
                        });
                        courseSelect.prop('disabled', false);
                    }
                },
                fail: function(error) {
                    Notification.alert('Error', 'Failed to load courses: ' + error.message);
                }
            }]);
        }
        
        function loadTeachers(courseId) {
            Ajax.call([{
                methodname: 'local_teachersubstitution_get_teachers',
                args: { courseid: parseInt(courseId) },
                done: function(response) {
                    currentTeacherSelect.empty().append('<option value="">' + 
                        M.util.get_string('selectcurrentteacher', 'local_teachersubstitution') + '</option>');
                    
                    if (response.teachers && response.teachers.length > 0) {
                        $.each(response.teachers, function(index, teacher) {
                            currentTeacherSelect.append('<option value="' + teacher.username + '">' + 
                                teacher.fullname + '</option>');
                        });
                        currentTeacherSelect.prop('disabled', false);
                    }
                },
                fail: function(error) {
                    Notification.alert('Error', 'Failed to load teachers: ' + error.message);
                }
            }]);
        }
        
        function loadAvailableTeachers(courseId) {
            Ajax.call([{
                methodname: 'local_teachersubstitution_get_available_teachers',
                args: { courseid: parseInt(courseId) },
                done: function(response) {
                    newTeacherSelect.empty().append('<option value="">' + 
                        M.util.get_string('selectnewteacher', 'local_teachersubstitution') + '</option>');
                    
                    if (response.availableteachers && response.availableteachers.length > 0) {
                        $.each(response.availableteachers, function(index, teacher) {
                            newTeacherSelect.append('<option value="' + teacher.username + '">' + 
                                teacher.fullname + '</option>');
                        });
                        newTeacherSelect.prop('disabled', false);
                    }
                },
                fail: function(error) {
                    Notification.alert('Error', 'Failed to load available teachers: ' + error.message);
                }
            }]);
        }
        
        function validateForm() {
            return categorySelect.val() && 
                   courseSelect.val() && 
                   currentTeacherSelect.val() && 
                   newTeacherSelect.val() && 
                   $('#reason').val().trim();
        }
    };
    
    return {
        init: init
    };
});