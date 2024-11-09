# Employee Details Plugin

This repository contains two versions of a WordPress plugin that manage employee details and task assignments. Both plugins offer similar functionality but differ in their approach to displaying employee data in the WordPress admin dashboard.

## Versions

1. **Basic Version**: This version provides core features without using the `WP_List_Table` class.   (employee_details.php)
2. **Advanced Version (Using WP_List_Table)**: This version extends the basic plugin with a more advanced and user-friendly employee list interface by utilizing the `WP_List_Table` class.  (list_all_employees.php)

## Features

- **Add Employee Details**: Add employee information, including name, email, position, description, timezone, and profile image.
- **List Employees**: Display a searchable and paginated list of all employees.
- **Assign Tasks to Employees**: Add task details, such as task name and description, to specific employees.

## Differences Between Versions

### 1. Basic Version
- **Simplified Table Display**: Employees are displayed in a straightforward, custom-styled table.
- **Minimal Functionality**: Basic pagination and searching features are handled manually.
- **Quick Setup**: Easier to implement with fewer dependencies on WordPress classes.
  
This version is ideal if you’re looking for a straightforward solution that requires minimal customization and does not rely on advanced WordPress features.

### 2. Advanced Version (Using WP_List_Table)
- **WP_List_Table Integration**: Uses WordPress’s `WP_List_Table` class to create a more flexible and robust employee list.
- **Enhanced Admin Interface**: Provides advanced pagination, search, and column sorting natively supported by WordPress.
- **Scalable and Extendable**: This approach is highly recommended for handling large datasets and creating a familiar WordPress-style list interface.

The `WP_List_Table` version is better suited for scenarios where you need an optimized, scalable, and native WordPress solution for managing large employee lists with minimal code complexity.

## Installation

1. Clone or download the plugin files to your `/wp-content/plugins/` directory.
2. Activate the preferred version (Basic or WP_List_Table) through the 'Plugins' screen in WordPress.

## Usage

### Adding an Employee
1. Go to **Employee > Add Employee** in the WordPress admin dashboard.
2. Fill in the required employee information and upload an optional profile image.
3. Submit to add the employee to the database.

### Viewing Employee List
- In the **Basic Version**: Employee data is displayed in a simple table with basic pagination.
- In the **WP_List_Table Version**: Employee data is displayed in a dynamic, sortable, and paginated table, offering a native WordPress user experience.

### Assigning Tasks
1. Navigate to the **List Employee** page.
2. Assign tasks by entering task name and description.

## Benefits of WP_List_Table

The `WP_List_Table` class provides built-in functionalities such as:
- **Column Sorting**: Easily sort employees by name, email, or position.
- **Advanced Pagination**: Handles larger datasets more efficiently.
- **Search Integration**: Enables searching by specific columns, providing a smoother admin experience.
  
## Development Notes

- AJAX functions are used for adding employees, assigning tasks, and retrieving employee lists.
- Nonces are implemented to ensure secure AJAX requests.
- The code structure and functionality are largely similar, but the `WP_List_Table` version integrates more closely with WordPress core functions for a refined admin experience.

## License

This project is licensed under the GPL2 License.

---

*Author: Shubh*
