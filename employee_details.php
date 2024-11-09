<?php
/*
 * Plugin Name: Add Employee Details Plugin
 * Description: A simple plugin to add and manage employee details.
 * Author: Shubh    
 * License: GPL2
 * Text Domain: simple-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}
//defined plugin dir path
define("EMP_PLUGIN_PATH", plugin_dir_path(__FILE__));

class EmployeeDetails
{
    public function __construct()
    {
        //register admin menu
        add_action('admin_menu', [$this, 'employeeDetails_plugin_add_admin_menu']);

        //load assets
        add_action('admin_enqueue_scripts', [$this, 'load_assets']);

        //add new employee
        add_action('wp_ajax_add_new_employee', [$this, 'handle_employee_data']);
        add_action('wp_ajax_nopriv_add_new_employee', [$this, 'handle_employee_data']);

        //get all employee data
        add_action('wp_ajax_get_all_employee_details', [$this, 'get_all_employee_details']);
        add_action('wp_ajax_nopriv_get_all_employee_details', [$this, 'get_all_employee_details']);

        //assign task
        add_action('wp_ajax_add_task', [$this, 'handle_add_task']);
        add_action('wp_ajax_nopriv_add_task', [$this, 'handle_add_task']);

        //loadTable task
        add_action('wp_ajax_loadTable', [$this, 'loadTable']);
        add_action('wp_ajax_nopriv_loadTable', [$this, 'loadTable']);
    }

    public function load_assets()
    {

        if (@$_GET['page'] == "employee-details" || @$_GET['page'] == "employees-list" || @$_GET['page'] ==  "add-new-employee" || @$_GET['page'] == "register-employee") {
            wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');

            // Enqueue jQuery
            wp_enqueue_script('jquery');

            // Enqueue Popper.js (Bootstrap's dependency)
            wp_enqueue_script('popper-js', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js', array('jquery'), null, true);

            // Enqueue Bootstrap JavaScript
            wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery', 'popper-js'), null, true);

            wp_enqueue_style(
                'employeeDetails_plugin_style',
                plugin_dir_url(__FILE__) . 'css/employeeDetails_plugin_style.css'
            );

            wp_enqueue_script(
                'bootpag-cdn',
                'https://cdnjs.cloudflare.com/ajax/libs/jquery-bootpag/1.0.4/jquery.bootpag.min.js',
                array('jquery'),
                null,
                true
            );
            wp_enqueue_media();


            wp_enqueue_script(
                'add_employee_js',
                plugin_dir_url(__FILE__)  . 'js/add_employee.js',
                array('jquery'),
                null,
                true
            );

            wp_enqueue_script(
                'add_employee',
                plugin_dir_url(__FILE__) . 'js/assign_task.js',
                array('jquery'),
                null,
                true
            );

            wp_localize_script(
                'add_employee_js',
                'add_new_employee',
                [
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('add_new_employee_nonce')
                ]
            );
        }
    }

    public function employeeDetails_plugin_add_admin_menu()
    {
        add_menu_page(
            'Employee Page',
            'Employee',
            'manage_options',
            'employee-details',
            [$this, 'employeeDetails_page'],
            'dashicons-media-document',
            25
        );

        add_submenu_page(
            'employee-details',
            'Employee Page',
            'All Employee',
            'manage_options',
            'employee-details',
            [$this, 'employeeDetails_page'],
            25
        );

        add_submenu_page(
            'employee-details',
            'Add Employee',
            'Add Employee',
            'manage_options',
            'add-new-employee',
            [$this, 'add_new_employee_page'],
        );

        add_submenu_page(
            'employee-details',
            'add employee',
            'Add New Employee',
            'manage_options',
            'register-employee',
            [$this, 'register_new_employee']
        );

        add_submenu_page(
            'employee-details',
            'List Employee',
            'List Employee',
            'manage_options',
            'employees-list',
            [$this, 'list_of_all_employees_page'],
        );
    }


    public function list_of_all_employees_page()
    {
        include_once(EMP_PLUGIN_PATH . '/list_all_employees.php');

        if (class_exists('list_of_all_employees')) {

            //created object for class list_of_all_employees
            $list_all_employee_object = new list_of_all_employees();

            // Prepare the table items and display the table
            $list_all_employee_object->prepare_items();

            echo '<div class="wrap">';
            echo '<h2 class="text-center">List of Employees</h2>';
            $list_all_employee_object->extra_tablenav("top");
            echo '<form method="GET" id="form_search">';
            echo '<input type="hidden"name="page" value="employees-list">';

            //add search box
            $list_all_employee_object->search_box("Search Employee", "search_employees");

            //display records
            $list_all_employee_object->display();

            echo '</form>';
            echo '</div>';
        } else {
            echo 'Unable to load employee list.';
        }
    }


    public function employeeDetails_page()
    {
        require_once('html/employee_details_page.php');
    }


    public function register_new_employee()
    {


        include_once(plugin_dir_path(__FILE__) . '/html/register_employee.php');
    }

    public function get_all_employee_details()
    {
        global $wpdb;

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'add_new_employee_nonce')) {
            wp_send_json_error('Nonce verification failed');
            wp_die();
        }

        $items_per_page = 8;
        $page = isset($_POST['page']) ? abs((int)$_POST['page']) : 1;
        $search_text = isset($_POST['search_text']) ? sanitize_text_field($_POST['search_text']) : '';
        $offset = ($page - 1) * $items_per_page;
        $search_text = $search_text ? '%' . $wpdb->esc_like($search_text) . '%' : '';

        if (empty($search_text)) {
            $query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}employee");
            $employees_query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}employee ORDER BY id DESC LIMIT %d, %d", $offset, $items_per_page);
        } else {
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}employee WHERE emp_id LIKE %s OR emp_name LIKE %s OR emp_email LIKE %s",
                $search_text,
                $search_text,
                $search_text
            );
            $employees_query = $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}employee WHERE emp_id LIKE %s OR emp_name LIKE %s OR emp_email LIKE %s ORDER BY id DESC LIMIT %d, %d ",
                $search_text,
                $search_text,
                $search_text,
                $offset,
                $items_per_page
            );
        }

        $total_records = $wpdb->get_var($query);
        $total_pages = ceil($total_records / $items_per_page);

        $employees = $wpdb->get_results($employees_query, ARRAY_A);
        ob_start();

        require_once('html/get_all_employee_details.php');

        wp_send_json_success(array(
            'html' => ob_get_clean(),
            'total_pages' => $total_pages
        ));
    }
    public function add_new_employee_page()
    {

        //add new employee page
        require_once('html/add_new_employee_page.php');
    }


    public  function loadTable()
    {
        $this->list_of_all_employees_page();
    }


    //handle add employee data here 
    public function handle_employee_data()
    {
        // Verify nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'add_new_employee_nonce')) {
            wp_send_json_error(array('message' => 'Nonce verification failed'));
            wp_die();
        }

        // Sanitize and validate form inputs
        $employee_name = isset($_POST['employee_name']) ? sanitize_text_field($_POST['employee_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $position = isset($_POST['position']) ? sanitize_text_field($_POST['position']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $timezone = isset($_POST['timezone']) ? sanitize_text_field($_POST['timezone']) : '';
        $image_url = '';

        // Validate form data
        if (empty($employee_name) || !is_email($email) || empty($position) || empty($description) || empty($timezone)) {
            wp_send_json_error(array('message' => 'All fields are required.'));
            wp_die();
        }

        // Handle file upload
        if (isset($_FILES['employee_image']) && !empty($_FILES['employee_image']['name'])) {
            $uploaded_file = $_FILES['employee_image'];
            $file_extension = explode('.', $uploaded_file['name']);
            $file_size = $uploaded_file['size'];
            $file_extension = strtolower(end($file_extension));
            $accepted_formate = array(
                'jpeg',
                'jpg',
                'png'
            );

            if (in_array($file_extension, $accepted_formate)) {
                if ($file_size > 2097152) {
                    wp_send_json_error(array('message' => 'File too large. File must be less than 2MB.'));
                    wp_die();
                } else {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    require_once(ABSPATH . 'wp-admin/includes/media.php');

                    $upload_overrides = array('test_form' => false);
                    $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
                    if ($movefile && !isset($movefile['error'])) {
                        $image_url = $movefile['url'];   //get url
                    } else {
                        wp_send_json_error(array('message' => 'Image upload error: ' . $movefile['error']));
                        wp_die();
                    }
                }
            } else {
                wp_send_json_error(array('message' => 'Image upload error : ' . $file_extension . ' file type not allowed'));
                wp_die();
            }
        }

        // Insert data into the database
        global $wpdb;
        $table_name = $wpdb->prefix . 'employee';
        $data = [
            'emp_id' => '',
            'emp_name' => $employee_name,
            'emp_email' => $email,
            'emp_position' => $position,
            'emp_description' => $description,
            'emp_timezone' => $timezone,
            'emp_image' => $image_url
        ];
        $format = ['%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $inserted = $wpdb->insert(
            $table_name,
            $data,
            $format
        );

        if ($inserted) {
            $inserted_id = $wpdb->insert_id;
            $update_id = "EMP" . $inserted_id;
            $update_data = ['emp_id' => $update_id];
            $where = ['id' => $inserted_id];
            $updated = $wpdb->update($table_name, $update_data, $where, array('%s'), array('%d'));

            if ($updated) {
                wp_send_json_success(array('message' => 'Employee added successfully.'));
            } else {
                wp_send_json_error(array('message' => 'Employee ID not generated'));
            }
        } else {
            wp_send_json_error(array('message' => 'Failed to insert employee data. ' . $wpdb->last_error));
        }

        wp_die();
    }



    // public function handle_add_task()
    // {
    //     if (!isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'add_new_employee_nonce')) {
    //         wp_send_json_error(['message' => 'Invalid security token.']);
    //         return;
    //     }

    //     parse_str($_POST['formData'], $form_data);

    //     // Retrieve form fields
    //     $employee_id = isset($form_data['employee_id']) ? intval($form_data['employee_id']) : 0;
    //     $task_name = isset($form_data['task_name']) ? sanitize_text_field($form_data['task_name']) : '';
    //     $task_descriptions = isset($form_data['task_description']) ? array_map('sanitize_textarea_field', $form_data['task_description']) : [];


    //     // Validate form data
    //     if (empty($task_name) || empty($task_descriptions) || !array_filter($task_descriptions)) {
    //         wp_send_json_error(['message' => 'All fields are required and at least one task description must be provided.']);
    //         wp_die();
    //     }
    //     global $wpdb;
    //     $table_prefix = $wpdb->prefix;
    //     $table_name = $table_prefix . 'manage_task';


    //     $data = [
    //         'emp_id' => $employee_id,
    //         'task_name' => $task_name,
    //         'task_description' => serialize($task_descriptions), // Serialize if storing as a string
    //         'created_at' => current_time('mysql')
    //     ];


    //     $query = $wpdb->insert($table_name, $data);


    //     if ($query !== false) {
    //         wp_send_json_success(['message' => 'Task added successfully.']);
    //     } else {
    //         wp_send_json_error(['message' => "Task Not Assigned: " . $wpdb->last_error]);
    //     }

    //     wp_die();
    // }

    public function handle_add_task()
    {
        if (!isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'add_new_employee_nonce')) {
            wp_send_json_error(['message' => 'Invalid security token.']);
            return;
        }



        // // Retrieve form fields
        $employee_id = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;
        $task_name = isset($_POST['task_name']) ? sanitize_text_field($_POST['task_name']) : '';
        $task_descriptions = isset($_POST['task_description']) ? $_POST['task_description'] : [];


        // // Validate form data
        if (empty($task_name) || empty($task_descriptions)) {
            wp_send_json_error(['message' => 'All fields are required and at least one task description must be provided.']);
            wp_die();
        }


        // if (is_array($task_descriptions)) {
        //     wp_send_json_error('');
        // }

        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . 'manage_task';


        $data = [
            'emp_id' => $employee_id,
            'task_name' => $task_name,
            'task_description' => ($task_descriptions), // Serialize if storing as a string
            'created_at' => current_time('mysql')
        ];


        $query = $wpdb->insert($table_name, $data);


        if ($query !== false) {
            wp_send_json_success(['message' => 'Task added successfully.']);
        } else {
            wp_send_json_error(['message' => "Task Not Assigned: " . $wpdb->last_error]);
        }

        wp_die();
    }
}
new EmployeeDetails();
