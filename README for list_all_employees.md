# WordPress Employee List Plugin

This WordPress plugin displays a list of employees in the WordPress admin area, using a custom `WP_List_Table` class to provide an interactive, paginated, sortable, and searchable table interface. The plugin also includes task assignment functionality for employees.

## Features

- Display employees in an admin table with pagination.
- Sort and filter employees by different columns.
- Search employees by ID, name, or position.
- Bulk actions and row actions for managing employee records.
- Ability to mark records as trash or permanently delete.
- Assign tasks to employees and view assigned tasks via modal pop-ups.

## Installation

1. Download the plugin files and place them in the `wp-content/plugins` directory.
2. Activate the plugin in the WordPress admin area under **Plugins**.
3. The employee list will be available in the WordPress admin menu.

## Usage

Once activated, the plugin displays an employee table with the following functionalities:

- **Search:** Search employees by ID, name, or position.
- **Sorting:** Click on column headers (ID or Name) to sort employees.
- **Pagination:** Browse employee records page by page.
- **Status Links:** Filter records by **All**, **Published**, or **Trash**.
- **Bulk Actions:** Edit, Trash, Restore, or Permanently Delete multiple records.
- **Row Actions:** Restore, Delete, Edit, View, and Quick Edit actions per employee.

### Code Structure

The plugin uses the following methods within the `WP_List_Table` class:

- **`prepare_items()`** - Prepares and retrieves employee data from the database with pagination, sorting, and search functionality.
- **`get_columns()`** - Defines table columns like ID, Employee ID, Name, Email, Position, etc.
- **`get_sortable_columns()`** - Defines sortable columns.
- **`column_default()`** - Displays default values for each column.
- **`column_emp_image()`** - Displays employee photos.
- **`column_cb()`** - Adds checkboxes for bulk actions.
- **`employee_position()`** - Maps employee position IDs to position names.
- **`column_emp_position()`** - Displays mapped position names.
- **`get_bulk_actions()`** - Defines bulk actions like Trash, Restore, and Delete Permanently.
- **`extra_tablenav()`** - Adds custom status links above the table for filtering.
- **`no_items()`** - Displays a message if no employee data is found.
- **`column_assign_task()`** - Displays task assignment and viewing buttons.

### Task Assignment

The `column_assign_task()` method checks if a task is already assigned to the employee. If so, it shows a **View Task** button; otherwise, it displays an **Assign Task** button. Clicking these buttons triggers modal pop-ups to view or assign tasks.

## Screenshots

### Employee List Table with Pagination and Search

![Employee List Table](screenshot1.png)

### Task Assignment Modal

![Task Assignment Modal](screenshot2.png)

## License

This plugin is open-source and available under the [MIT License](LICENSE).

## Contributing

Feel free to fork the repository and make contributions. Pull requests are welcome for bug fixes, feature enhancements, or code improvements.
