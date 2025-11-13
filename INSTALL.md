# Teacher Substitution Plugin - Installation Guide

## Quick Installation

### Method 1: ZIP Upload (Recommended)
1. Download the `teachersubstitution.zip` file
2. Log in to Moodle as administrator
3. Go to **Site Administration** → **Plugins** → **Install plugins**
4. Upload the ZIP file
5. Follow the installation prompts

### Method 2: Manual Installation
1. Extract the plugin files
2. Copy the `teachersubstitution` folder to `/local/` directory in your Moodle installation
3. The final path should be: `moodle/local/teachersubstitution/`
4. Log in to Moodle as administrator
5. Go to **Site Administration** → **Notifications**
6. Click "Upgrade Moodle database now" to complete installation

## Post-Installation Setup

### 1. Configure Webhook URL
1. Go to **Site Administration** → **Plugins** → **Local plugins** → **Teacher Substitution**
2. Enter your n8n webhook URL (e.g., `https://your-n8n-instance.com/webhook/moodle-substitution`)
3. Save changes

### 2. Set Permissions
1. Go to **Site Administration** → **Users** → **Permissions** → **Define roles**
2. Edit the roles that should have access to teacher substitution
3. Add the capability `local/teachersubstitution:substitute`

### 3. Add Sample Data (Optional)
If you want to test the plugin, you can add sample data to the `epimorfwtes` table:

```sql
INSERT INTO mdl_epimorfwtes (emailpsd, name, surname, accepted) VALUES
('teacher1@example.com', 'John', 'Doe', 1),
('teacher2@example.com', 'Jane', 'Smith', 1),
('teacher3@example.com', 'Bob', 'Johnson', 1);
```

## Verification

1. **Check Plugin Installation**:
   - Go to **Site Administration** → **Plugins** → **Local plugins**
   - Verify "Teacher Substitution" appears in the list

2. **Test the Interface**:
   - Navigate to **Site Administration** → **Teacher Substitution**
   - Verify the form loads correctly
   - Test the dropdown functionality

3. **Check Webhook Integration**:
   - Submit a test substitution
   - Verify the webhook receives the data

## Common Issues

### Issue: "Plugin not found" error
**Solution**: Ensure the plugin folder is named exactly `teachersubstitution` and is located in the `local/` directory

### Issue: Database tables not created
**Solution**: Run the installation process again via **Site Administration** → **Notifications**

### Issue: Dropdowns not working
**Solution**: 
- Check browser console for JavaScript errors
- Ensure the AMD module is properly built: `php admin/cli/check_database_schema.php`

### Issue: Permission denied
**Solution**: Assign the `local/teachersubstitution:substitute` capability to appropriate roles

## Next Steps

1. **Configure n8n Workflow**: Set up your n8n workflow to process the webhook data
2. **Train Users**: Provide training to administrators who will use the plugin
3. **Monitor Usage**: Check logs and webhook responses for any issues

## Support

If you encounter issues during installation:
1. Check the Moodle logs in **Site Administration** → **Reports** → **Logs**
2. Verify all file permissions are correct
3. Ensure your Moodle version is compatible (4.0+)
4. Check PHP requirements (7.4+)