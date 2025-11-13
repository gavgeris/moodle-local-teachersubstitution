# Teacher Substitution Plugin for Moodle 4.x

## Overview
This plugin provides a user interface for substituting teachers in Moodle courses. It allows administrators to select a course, choose a teacher to be replaced, select a new teacher from available candidates, and send the substitution request to an n8n webhook for processing.

## Features
- Dynamic dropdown lists for categories, courses, and teachers
- Integration with custom `epimorfwtes` table for available teachers
- Support for both regular teachers and teacherplus roles
- Webhook integration with n8n workflow
- Greek and English language support
- Responsive Bootstrap-based UI

## Requirements
- Moodle 4.0 or higher
- PHP 7.4 or higher
- Database table `epimorfwtes` (created automatically during installation)

## Installation

1. **Download the plugin**:
   - Download the plugin files as a ZIP archive
   - Extract the files to your local machine

2. **Upload to Moodle**:
   - Upload the `teachersubstitution` folder to your Moodle's `/local/` directory
   - The path should be: `/local/teachersubstitution/`

3. **Install the plugin**:
   - Log in to your Moodle site as administrator
   - Go to **Site Administration** → **Notifications**
   - Follow the installation prompts to complete the plugin installation

4. **Configure the webhook**:
   - Go to **Site Administration** → **Plugins** → **Local plugins** → **Teacher Substitution**
   - Enter your n8n webhook URL
   - Save the configuration

## File Structure
```
local/teachersubstitution/
├── amd/src/dropdowns.js          # JavaScript for dynamic dropdowns
├── classes/external.php          # AJAX service endpoints
├── db/
│   ├── access.php               # Capability definitions
│   ├── install.xml              # Database schema
│   └── services.php             # Web service definitions
├── lang/
│   ├── en/local_teachersubstitution.php    # English strings
│   └── el/local_teachersubstitution.php    # Greek strings
├── index.php                    # Main plugin page
├── lib.php                      # Core plugin functions
├── settings.php                 # Plugin settings
└── version.php                  # Plugin version information
```

## Usage

1. **Access the plugin**:
   - Navigate to **Site Administration** → **Teacher Substitution**
   - Or access directly via: `/local/teachersubstitution/index.php`

2. **Select Category**: Choose a course category from the dropdown

3. **Select Course**: Choose a course from the available courses in the selected category

4. **Select Teacher to Replace**: Choose the current teacher who needs to be replaced (shows fullname and group)

5. **Select New Teacher**: Choose from:
   - Teachers with "teacherplus" role in the course (marked as "Επιμορφωτής+")
   - Available teachers from the `epimorfwtes` table (marked as accepted)

6. **Enter Reason**: Provide a reason for the substitution

7. **Submit**: Click the "Substitute" button to send the request to the webhook

## Webhook Data Format

The plugin sends the following JSON data to the configured webhook:

```json
{
  "courseid": 123,
  "currentteacher": "old_teacher_username",
  "newteacher": "new_teacher_username",
  "reason": "Substitution reason text",
  "timestamp": 1234567890
}
```

## Database Table: epimorfwtes

The plugin uses a custom table with the following structure:

| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary key |
| emailpsd | varchar(100) | Username (email) - Unique |
| isold | int | Is old teacher (0/1) |
| years | int | Years of experience |
| choice1-3 | varchar(100) | Teacher choices |
| email | varchar(100) | Email address |
| eidikotita | varchar(100) | Specialization |
| surname | varchar(100) | Last name |
| name | varchar(100) | First name |
| finalseminar | varchar(100) | Final seminar |
| finaltmima | varchar(100) | Final department |
| registered | int | Is registered (0/1) |
| priority | int | Priority level |
| accepted | int | Is accepted (0/1) |
| batch | varchar(50) | Batch information |
| comments | text | Comments |

## Security
- The plugin requires appropriate capabilities to access course information
- All AJAX endpoints validate user permissions
- Form submissions require valid session keys
- Webhook communication uses proper HTTP methods

## Troubleshooting

### Plugin not appearing in admin menu
- Ensure the plugin is properly installed via **Site Administration** → **Notifications**
- Check that you have the required capabilities

### Dropdowns not populating
- Check browser console for JavaScript errors
- Ensure the AJAX services are properly installed
- Verify that courses and teachers exist in your Moodle instance

### Webhook not receiving data
- Check the webhook URL configuration in plugin settings
- Verify the n8n workflow is properly configured
- Check Moodle logs for any error messages

## Support
For issues and feature requests, please check the plugin documentation or contact the development team.

## License
This plugin is released under the GNU GPL v3 or later license.