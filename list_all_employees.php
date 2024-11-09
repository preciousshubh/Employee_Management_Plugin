<?php
if (!class_exists("WP_List_Table")) {

    include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
class list_of_all_employees extends WP_List_Table
{

    public function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'employee';

        //limit per page show 4 records 
        $per_page = 4;

        //get current page using get_pagenum() method 
        $current_page = $this->get_pagenum();
        // echo "Current Page : " . $current_page . "<br>";

        //calculate offset
        $offset = ($current_page - 1) * $per_page;

        //get current action
        $rowAction = $this->current_action();
        if (!empty($rowAction)) {
            $this->process_row_action($rowAction);
        }

        //get orderby and order when isset
        $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : "id";
        $order = isset($_GET['order']) ? $_GET['order'] : "DESC";

        //get search text when isset
        $search = isset($_GET['s']) ? '%' .  $_GET['s'] . '%' : false;

        // echo "search : " . $search . "<br>";

        if ($search) {
            //selcts all the data from wp_employee table and also search text 
            $query = $wpdb->get_results(
                "SELECT * FROM {$table_name}
                WHERE emp_id LIKE '{$search}'
                OR emp_name LIKE '{$search}'
                OR emp_position LIKE '{$search}
                "
            );


            //get all employee data order them desc basend on id col
            $employees = $wpdb->get_results(
                "SELECT * FROM {$table_name}
                WHERE emp_id LIKE '{$search}' 
                OR emp_name LIKE '{$search}'
                OR emp_position LIKE '{$search}'
                ORDER BY {$orderby} {$order}  
                LIMIT {$offset}, {$per_page}",
                ARRAY_A
            );
        } else {
            $isTrashCondition = "";
            //selcts all the data from wp_employee table 
            if ($rowAction == "show_all") {
            } elseif (($rowAction == "show_published")) {
                $isTrashCondition = "WHERE is_trash=0";
            } elseif (($rowAction == "show_trash")) {
                $isTrashCondition = "WHERE is_trash=1";
            }
            $query = $wpdb->get_results("SELECT * FROM {$table_name} {$isTrashCondition}");


            //get all employee data order them desc basend on id col
            $employees = $wpdb->get_results(
                "SELECT * FROM {$table_name}
                {$isTrashCondition}
                ORDER BY {$orderby} {$order}  
                LIMIT {$offset}, {$per_page}",
                ARRAY_A
            );
        }

        //counts total number of records in table wp_employee
        $totalrecords = count($query);
        //echo "totalrecords : " . $totalrecords . "<br>";

        //sets custom column header
        $this->_column_headers = array(
            $this->get_columns(),
            [],
            $this->get_sortable_columns()
        );

        $this->set_pagination_args(
            array(
                "total_items" => $totalrecords,
                "per_page" => $per_page,
                "total_pages" => ceil($totalrecords / $per_page)
            )
        );

        //fetch every employee data using items method
        $this->items = $employees;
    }

    // Return column name
    public function get_columns()
    {
        // Key => value
        // DB Table column key => Front-end table column headers
        $columns = [
            "cb" => '<input type="checkbox" />',
            "id" => "ID",
            "emp_id" => "Employee ID",
            "emp_name" => "Name",
            "emp_email" => "Email",
            "emp_position" => "Position",
            "assign_task" => "Assign Task",
            "emp_description" => "Description",
            "emp_image" => "Photo",
        ];

        return $columns;
    }

    //get sortable columns
    public function get_sortable_columns()
    {
        $columns = array(
            'id' => array('id', true),
            'emp_name' => array('emp_name', false),
        );
        return $columns;
    }

    //return every signle employee data basend on col name
    public function column_default($singleEmployee, $col_name)
    {

        return isset($singleEmployee[$col_name]) ? '<span class="data_' . $col_name . '">' . $singleEmployee[$col_name] . '</span>' : "N/A";
    }

    public function column_emp_image($employee)
    {
        if ($employee['emp_image'] == 0) {
            return '<img src="' . plugin_dir_url(__FILE__) . 'img/images.png' . '" height="100px" width="120px"/>';
        } else {
            return '<img src="' . $employee['emp_image'] . '" height="100px" width="120px"/>';
        }
    }

    //add check box for every records
    public function column_cb($employee)
    {
        return '<input type="checkbox" name="employee_id[]"  value="' . $employee['id'] . '" />';
    }

    //function to get employees position based on their table records at  position col
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
        return isset($positions[$position]) ? '<span class="emp_position" data-emp-position="' . $position . '" >' . $positions[$position] . '</span>' : 'Employee';
    }

    //get all employees position  
    public function column_emp_position($employee)
    {
        return $this->employee_position($employee['emp_position']);
    }

    //get bulk actions
    public function get_bulk_actions()
    {
        $current_action = $this->current_action();
        $actions = [];
        if ($current_action == "show_trash") {
            $actions = array(
                'restore' => 'Restore',
                'delete_permanent' => 'Delete Permanently'
            );
        } else if ($current_action == 'show_published') {
            $actions = array(
                'edit' => 'Edit',
                'trash' => 'Move to trash'
            );
        } else {
            $actions = array(
                'edit' => 'Edit',
                'trash' => 'Move to trash'
            );
        }

        return $actions;
    }

    //add row action links to columns
    public function column_id($item)
    {
        $actions = [];
        $action = $this->current_action();
        if ($action == "show_trash") {
            $actions['restore'] = "<a onclick='return confirm(\"Are you sure want to move to restore?\");' href='admin.php?page=employees-list&action=restore&employee_id=" . $item['id'] . "'>Restore</a>";
            $actions['delete_permanent'] =
                "<span class='trash'><a  onclick='return confirm(\"Are you sure want to delete it permanently?\");' href='admin.php?page=employees-list&action=delete_permanent&employee_id=" . $item['id'] . "'>Delete Permanently</a></span>";
        } else if ($action == 'show_published') {
            $actions['edit'] = '<a href="#">Edit</a>';
            $actions['quck_edit'] = '<a href="#">Quick Edit</a>';
            $actions['trash'] = "<a onclick='return confirm(\"Are you sure want to move to trash?\");' href='admin.php?page=employees-list&action=trash&employee_id=" . $item['id'] . "'> Move to trash</a>";
            $actions['view'] = '<a href="#">View</a>';
        } else if ($action == 'show_all') {

            if ($item['is_trash'] == 1) {
                $actions['restore'] =
                    "<a onclick='return confirm(\"Are you sure want to move to restore?\");' href='admin.php?page=employees-list&action=restore&employee_id=" . $item['id'] . "' >Restore</a>";
                $actions['delete_permanent'] = "<span class='trash'><a  onclick='return confirm(\"Are you sure want to delete it permanently?\");' href='admin.php?page=employees-list&action=delete_permanent&employee_id=" . $item['id'] . "'>Delete Permanently</a></span>";
            } else {
                $actions['edit'] = '<a href="#">Edit</a>';
                $actions['quck_edit'] = '<a href="#">Quick Edit</a>';
                $actions['trash'] = "<a onclick='return confirm(\"Are you sure want to move to trash?\");' href='admin.php?page=employees-list&action=trash&employee_id=" . $item['id'] . "'> Move to trash</a>";
                $actions['view'] = '<a href="#">View</a>';
            }
        } else {
            $actions['edit'] = '<a href="#">Edit</a>';
            $actions['quck_edit'] = '<a class="btn-quick-edit" href="#">Quick Edit</a>';
            $actions['trash'] = "<a onclick='return confirm(\"Are you sure want to move to trash?\");' href='admin.php?page=employees-list&action=trash&employee_id=" . $item['id'] . "'> Move to trash</a>";
            $actions['view'] = '<a href="#">View</a>';
        }

        return sprintf('%1$s %2$s', '<span class="emp-id">' . $item['id'] . '</span>', $this->row_actions($actions));
    }

    public function process_row_action($action_type)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'employee';
        if ($action_type == 'trash') {
            // echo "<pre>";
            // print_r($_REQUEST);
            // echo "</pre>";
            // wp_die();
            $employee_ids = isset($_REQUEST['employee_id']) ? $_REQUEST['employee_id'] : "";

            if (is_array($employee_ids)) {
                //Bulk Action
                foreach ($employee_ids as $employee_id) {
                    $update_data = ['is_trash' => 1];
                    $where = ['id' => $employee_id];
                    //update query
                    $wpdb->update($table_name, $update_data, $where);
                }
?>
                <script>
                    window.location.href = '<?php echo admin_url("admin.php?page=employees-list&action=show_trash") ?>';
                </script>
                <?php
            } else {
                //single row action
                if (!empty($employee_ids)) {

                    $update_data = ['is_trash' => 1];

                    $where = ['id' => $employee_ids];

                    //update query
                    $wpdb->update($table_name, $update_data, $where);

                    //shows error instead of this use js
                    //header("location:admin.php?page=employees-list");
                ?>

                    <script>
                        window.location.href = '<?php echo admin_url("admin.php?page=employees-list&action=show_trash") ?>';
                    </script>
                <?php
                }
            }
        } elseif ($action_type == 'restore') {
            $employee_ids = isset($_REQUEST['employee_id']) ? $_REQUEST['employee_id'] : "";
            if (is_array($employee_ids)) {
                //Bulk Action
                foreach ($employee_ids as $employee_id) {
                    $update_data = ['is_trash' => 0];
                    $where = ['id' => $employee_id];
                    //update query
                    $wpdb->update($table_name, $update_data, $where);
                }
                ?>
                <script>
                    window.location.href = '<?php echo admin_url("admin.php?page=employees-list&action=show_published") ?>';
                </script>
                <?php
            } else {
                if (!empty($employee_ids)) {

                    $update_data = ['is_trash' => 0];

                    $where = ['id' => $employee_ids];

                    //update query
                    $wpdb->update($table_name, $update_data, $where);
                ?>

                    <script>
                        window.location.href = '<?php echo admin_url("admin.php?page=employees-list&action=show_published") ?>';
                    </script>
                <?php
                }
            }
        } elseif ($action_type == 'delete_permanent') {
            $employee_ids = isset($_REQUEST['employee_id']) ? $_REQUEST['employee_id'] : "";
            if (is_array($employee_ids)) {
                //Bulk Action
                foreach ($employee_ids as $employee_id) {
                    $where = ['id' => $employee_id];
                    $wpdb->delete($table_name, $where);
                }
                ?>
                <script>
                    window.location.href = '<?php echo admin_url("admin.php?page=employees-list&action=show_published") ?>';
                </script>
                <?php
            } else {
                if (!empty($employee_ids)) {

                    $where = ['id' => $employee_ids];

                    //delete query
                    $wpdb->delete($table_name, $where);
                ?>

                    <script>
                        window.location.href = '<?php echo admin_url("admin.php?page=employees-list&action=show_published") ?>';
                    </script>
<?php
                }
            }
        }
    }

    //to add status check links
    public function extra_tablenav($position)
    {
        if ($position == "top") {
            global $wpdb;
            $table_name = $wpdb->prefix . 'employee';
            $action_type = $this->current_action();
            $status_links = array(
                'all' => count($wpdb->get_results("SELECT * FROM {$table_name}", ARRAY_A)),
                'published' => count($wpdb->get_results("SELECT * FROM {$table_name} WHERE is_trash=0 ", ARRAY_A)),
                'trash' => count($wpdb->get_results("SELECT * FROM {$table_name} WHERE is_trash=1 ", ARRAY_A))
            );
            echo '<div class="alignleft action">';

            echo '<ul class="subsubsub status-links ">';
            foreach ($status_links as $status => $count) {
                $currentClass = "";
                if ($action_type == "show_" . $status) {
                    $currentClass = "current";
                }
                echo '<li><a href="admin.php?page=employees-list&action=show_' . $status . '" class="' . $currentClass . '">' .  ucfirst($status) . ' (' . $count . ') ' . '</a></li>  |';
            }

            echo '</ul>';
            echo '</div>';
        }
    }
    //executes when their is no data found
    public function no_items()
    {
        echo "No employee  data found.";
    }


    public function column_assign_task($employee)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'manage_task';
        $emp_id = $employee['id'];
        $emp_name = $employee['emp_name'];
        $sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `emp_id`=%d", $emp_id);
        $result = $wpdb->get_row($sql, ARRAY_A);
        if ($result) {

            $task_name = $result['task_name'];
            $task_descriptions = $result['task_description'];
            $task_descriptions = urldecode($task_descriptions);
            $button = '<button type="button" class="btn btn-primary view-modal-btn"
            data-emp-id="' . $employee['id'] . '"
            data-emp-name="' . $employee['emp_name'] . '"
            data-task-name="' . $task_name . '"
            data-task-description="' . $task_descriptions . '"
            data-toggle="modal"
            data-target="#viewTaskModal">
            View Task
            </button>
            ';


            include_once(plugin_dir_path(__FILE__) . 'html/column_assign_task_view_button.php');
            return $button;
        } else {

            $button = '<button type="button" class="btn btn-primary open-modal-btn"
            data-emp-id="' . $employee['id'] . '"
            data-emp-name="' . $employee['emp_name'] . '"
            data-toggle="modal"
            data-target="#assignTaskModal">
            Assign Task
            </button>';
            include_once(plugin_dir_path(__FILE__) . 'html/column_assign_task_assign_button.php');
            return $button;
        }
    }
}
