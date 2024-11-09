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

class EmployeeDetails
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'employeeDetails_plugin_add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'load_assets']);
        add_action('wp_ajax_add_new_employee', [$this, 'handle_employee_data']);
        add_action('wp_ajax_nopriv_add_new_employee', [$this, 'handle_employee_data']);

        //get all employee data
        add_action('wp_ajax_get_all_employee_details', [$this, 'get_all_employee_details']);
        add_action('wp_ajax_nopriv_get_all_employee_details', [$this, 'get_all_employee_details']);
    }

    public function load_assets()
    {
        wp_enqueue_style(
            'employeeDetails_plugin_style',
            plugin_dir_url(__FILE__) . 'css/employeeDetails_plugin_style.css'
        );

        wp_enqueue_style(
            'bootstrap_style_css',
            'https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css'
        );

        wp_enqueue_script(
            'jquery-file',
            'https://code.jquery.com/jquery-3.7.1.min.js',
            [],
            null,
            true
        );

        wp_enqueue_script(
            'add_employee',
            plugin_dir_url(__FILE__) . 'js/add_employee.js',
            ['jquery-file'],
            null,
            true
        );

        wp_localize_script(
            'add_employee',
            'add_new_employee',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('add_new_employee_nonce')
            ]
        );

        wp_enqueue_script(
            'bootpag-cdn',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery-bootpag/1.0.4/jquery.bootpag.min.js',
            ['jquery-file'],
            '1.0.4',
            true
        );
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
            'Add Employee',
            'Add Employee',
            'manage_options',
            'add-new-employee',
            [$this, 'add_new_employee_page'],
            'dashicons-admin-users'
        );
    }

    public function employeeDetails_page()
    {
?>
        <div class="container d-flex flex-direction-row">
            <div class="add-employee-button ">
                <a href="<?php echo admin_url('admin.php?page=add-new-employee'); ?>" class="add-employee-link">Add New Employee +</a>
                <form id="search_form" class="search-form">
                    <input type="text" name="search_text" class="form-control search_text" placeholder="Search...">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>

        <h1><?php _e('Employee Details'); ?></h1>
        <div id="employee_table">
            <!-- Employee table will be loaded here by AJAX -->
        </div>
        <div id="show_paginator"></div>
        <?php
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
            $employees_query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}employee LIMIT %d, %d", $offset, $items_per_page);
        } else {
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}employee WHERE emp_id LIKE %s OR emp_name LIKE %s OR emp_email LIKE %s",
                $search_text,
                $search_text,
                $search_text
            );
            $employees_query = $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}employee WHERE emp_id LIKE %s OR emp_name LIKE %s OR emp_email LIKE %s LIMIT %d, %d",
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
        if ($employees) {
        ?>
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th class="text-primary text-center"><?php _e('Employee ID'); ?></th>
                        <th class="text-primary text-center"><?php _e('Employee Name'); ?></th>
                        <th class="text-primary text-center"><?php _e('Email ID'); ?></th>
                        <th class="text-primary text-center"><?php _e('Position'); ?></th>
                        <th class="text-primary text-center"><?php _e('Description'); ?></th>
                        <th class="text-primary text-center"><?php _e('Date'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    function entry_date($date_time)
                    {
                        if (!empty($date_time)) {
                            $date_time = strtotime($date_time);
                            $new_date_time = date("M-d-y H:i:s  A", $date_time);
                            return ($new_date_time);
                        } else {
                            return 'N/A';
                        }
                    }
                    function employee_position($position)
                    {
                        switch ($position) {
                            case '1':
                                return "Developer";
                            case '2':
                                return "Designer";
                            case '3':
                                return "Analyst";
                            case '4':
                                return "Manager";
                            case '5':
                                return "HR Specialist";
                            case '6':
                                return "Support Specialist";
                            case '7':
                                return "Product Manager";
                            case '8':
                                return "Marketing Coordinator";
                            default:
                                return "Employee";
                        }
                    }
                    foreach ($employees as $employee) { ?>
                        <tr>
                            <td><?php echo esc_html($employee['emp_id']); ?></td>
                            <td><?php echo esc_html($employee['emp_name']); ?></td>
                            <td><?php echo esc_html($employee['emp_email']); ?></td>
                            <td><?php echo esc_html(employee_position($employee['emp_position'])); ?></td>
                            <td><?php echo esc_html($employee['emp_description']); ?></td>
                            <td><?php echo esc_html(entry_date($employee['date_time'])); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        <?php
            echo 'total_pages : ' . $total_pages . "<br>";
        } else {
            echo '<p>No employees found.</p>';
        }

        ob_start();
        ?>

    <?php

        $pagination_html = ob_get_clean();
        wp_send_json_success(array(
            'html' => ob_get_clean(),
            'pagination' => $pagination_html,
            'total_pages' => $total_pages
        ));
    }
    public function add_new_employee_page()
    {
    ?>
        <div class="container">
            <div class="d-flex justify-content-end mb-3">
                <div class="add-employee-button">
                    <a href="<?php echo admin_url('admin.php?page=employee-details'); ?>" class="add-employee-link btn btn-primary">View Employee +</a>
                </div>
            </div>
        </div>

        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="w-100" style="max-width: 800px;">
                <h1 class="text-center text-primary mb-1">Add New Employee</h1>
                <form method="POST" action="" class="form-width mx-auto" id="form_data">
                    <div class="form-group">
                        <label for="employee_name">Employee Name</label>
                        <input type="text" class="form-control" id="employee_name" name="employee_name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email ID</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="position">Position</label>
                        <select class="form-control" name="position" id="position">
                            <option value="">Select position</option>
                            <option value="1">Developer</option>
                            <option value="2">Designer</option>
                            <option value="3">Analyst</option>
                            <option value="4">Manager</option>
                            <option value="5">HR Specialist</option>
                            <option value="6">Support Specialist</option>
                            <option value="7">Product Manager</option>
                            <option value="8">Marketing Coordinator</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                        <small class="text-danger" id="submit_message"></small>
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Add Employee</button>
                    </div>
                </form>
            </div>
        </div>
<?php
    }

    public function handle_employee_data()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'add_new_employee_nonce')) {
            wp_send_json_error('Nonce verification failed');
            wp_die();
        }

        $employee_name = isset($_POST['employee_name']) ? sanitize_text_field($_POST['employee_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $position = isset($_POST['position']) ? sanitize_text_field($_POST['position']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $error = '';

        if (empty($employee_name) || !is_email($email) || empty($position) || empty($description)) {
            $error = 'All fields are required.';
        }

        if (!empty($error)) {
            wp_send_json_error($error);
            wp_die();
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . 'employee';
            $data = [
                'emp_name' => $employee_name,
                'emp_email' => $email,
                'emp_position' => $position,
                'emp_description' => $description,
                'date_time' => current_time('mysql'),
            ];
            $format = ['%s', '%s', '%s', '%s', '%s'];

            $inserted = $wpdb->insert($table_name, $data, $format);

            if ($inserted) {
                $inserted_id = (int)$wpdb->insert_id;
                $update_emp_id = 'EMP' . $inserted_id;
                $update_data = ['emp_id' => $update_emp_id];
                $where = ['id' => $inserted_id];

                $updated = $wpdb->update($table_name, $update_data, $where, ['%s'], ['%d']);

                if ($updated !== false) {
                    wp_send_json_success('Employee added successfully');
                } else {
                    wp_send_json_error('Insert was successful, but update failed.');
                }
            } else {
                wp_send_json_error('Failed to add employee.');
            }
            wp_die();
        }
    }

    // Helper function to format date
    public function entry_date($date_time)
    {
        if (!empty($date_time)) {
            $date_time = strtotime($date_time);
            return date("M/d/y H:i:s A", $date_time);
        }
        return 'N/A';
    }

    // Helper function to convert position code to title
    public function employee_position($position)
    {
        $positions = [
            '1' => 'Developer',
            '2' => 'Designer',
            '3' => 'Analyst',
            '4' => 'Manager',
            '5' => 'HR Specialist',
            '6' => 'Support Specialist',
            '7' => 'Product Manager',
            '8' => 'Marketing Coordinator'
        ];
        return isset($positions[$position]) ? $positions[$position] : 'Employee';
    }
}

new EmployeeDetails();

?>
<script>
    jQuery(document).ready(function($) {
        function loadEmployeeData(page = 1, search_text = "") {
            $.ajax({
                url: add_new_employee.ajaxurl,
                type: "POST",
                data: {
                    action: "get_all_employee_details",
                    page: page,
                    search_text: search_text,
                    nonce: add_new_employee.nonce,
                },
                success: function(response) {
                    if (response.success) {
                        $("#employee_table").html(response.data.html);
                        // Initialize or update Bootpag with the total pages
                        $("#show_paginator").bootpag({
                            total: response.data.total_pages,
                            page: page,
                            maxVisible: 5,
                            leaps: true,
                            firstLastUse: true,
                            first: "←",
                            last: "→",
                            wrapClass: "pagination",
                            activeClass: "active",
                            disabledClass: "disabled",
                            nextClass: "next",
                            prevClass: "prev",
                            lastClass: "last",
                            firstClass: "first",
                        });
                    } else {
                        $("#employee_table").html("<p>No employees found.</p>");
                        $("#show_paginator").html("");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                },
            });
        }

        // Load data when the page loads
        loadEmployeeData();

        // Search functionality
        $("#search_form").on("submit", function(e) {
            e.preventDefault();
            var search_text = $(".search_text").val();
            loadEmployeeData(1, search_text);
        });

        // Initialize Bootpag
        $("#show_paginator")
            .bootpag({
                total: 1, // Start with a default value, will be updated dynamically
                page: 1,
                maxVisible: 5,
                leaps: true,
                firstLastUse: true,
                first: "←",
                last: "→",
                wrapClass: "pagination",
                activeClass: "active",
                disabledClass: "disabled",
                nextClass: "next",
                prevClass: "prev",
                lastClass: "last",
                firstClass: "first",
            })
            .on("page", function(event, num) {
                var search_text = $(".search_text").val();
                loadEmployeeData(num, search_text);
            });

        $("#form_data").on("submit", function(e) {
            e.preventDefault();
            var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            var input = document.createElement("input");
            input.name = "timezone";
            input.type = "hidden";
            input.value = timezone;
            $(this).append(input);
            var form_data = $(this).serialize();
            $.ajax({
                url: add_new_employee.ajaxurl,
                type: "POST",
                data: form_data + "&action=add_new_employee&nonce=" + add_new_employee.nonce,
                success: function(response) {
                    if (response == 1) {
                        $("#submit_message").html("<h5>Employee Added successfully<h5>");
                        $("#form_data")[0].reset();
                        var redirectUrl =
                            "http://localhost/wordpress/wp-admin/admin.php?page=employee-details";
                        window.location.href = redirectUrl;
                    } else {
                        $("#submit_message").html("<h5>" + response + "</h5>");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                },
            });
        });
    });
</script>