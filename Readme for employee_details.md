
# Add Employee Details Plugin

A WordPress plugin for managing employee details and assigning tasks with an easy-to-use interface. This plugin provides functionalities for adding, listing, and managing employees, including image uploads and task assignment.

## Features

- Add employee details, including name, email, position, description, timezone, and profile image.
- List all employees in the admin area with search functionality.
- Assign tasks to employees with a task name and description.
- Pagination for efficient employee management.
- Utilizes WordPress AJAX for smooth and dynamic interaction.
- Secure with nonce verification and sanitization.

## Installation

1. Download the plugin files and place them in the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.

## Usage

### Adding a New Employee
1. Navigate to **Employee > Add Employee** in the WordPress admin dashboard.
2. Enter the employee’s information and upload a profile picture.
3. Submit to add the employee to the database.

### Listing All Employees
1. Navigate to **Employee > All Employees**.
2. View, search, and paginate through the list of employees.

### Assigning Tasks to Employees
1. Go to the **Employee > List Employee** page.
2. Assign tasks by filling out task details, including task name and description.

## Code Structure

- **Main Plugin File**: Contains hooks for setting up the plugin, creating menu pages, and registering AJAX functions.
- **AJAX Handlers**: Handles AJAX requests for adding employees, retrieving employee lists, and assigning tasks.
- **Admin Pages**: Each page (e.g., add employee, list employees) is a separate file included in the plugin.
- **CSS/JS**: Assets are enqueued only on relevant admin pages for efficient loading.

## AJAX Endpoints

- **Add Employee**: `wp-admin/admin-ajax.php?action=add_new_employee`
- **Get All Employees**: `wp-admin/admin-ajax.php?action=get_all_employee_details`
- **Add Task**: `wp-admin/admin-ajax.php?action=add_task`
- **Load Employee Table**: `wp-admin/admin-ajax.php?action=loadTable`

## Development Notes

- Nonces are used to verify AJAX requests for security.
- Custom database tables are recommended for storing employee and task data for scalability.
- CSS and JS dependencies, such as Bootstrap and jQuery, are enqueued only on plugin-related pages.

## Contributing

Feel free to submit issues or contribute via pull requests.

## License

This project is licensed under the GPL2 License.

---

*Author: Shubh*
