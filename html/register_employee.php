  <?php
    if ($_SERVER['REQUEST_METHOD'] == "POST") {



        // Sanitize and validate form inputs
        $employee_name = isset($_POST['employee_name']) ? sanitize_text_field($_POST['employee_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $position = isset($_POST['position']) ? sanitize_text_field($_POST['position']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $image_url = isset($_POST['emp_image']) ? sanitize_textarea_field($_POST['emp_image']) : '';

        global $wpdb;
        $table_name = $wpdb->prefix . 'employee';
        $data = [
            'emp_id' => '',
            'emp_name' => $employee_name,
            'emp_email' => $email,
            'emp_position' => $position,
            'emp_description' => $description,
            'emp_timezone' => 'Asia/Calcutta',
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
    ?>
              <script>
                  window.location.href = ("http://localhost/wordpress/wp-admin/admin.php?page=employees-list");
              </script>
  <?php

                exit();
            } else {
                echo 'Employee ID not generated';
            }
        }
    }

    ?><div class="container">
      <div class="d-flex justify-content-end mb-3">
          <div class="add-employee-button">
              <a href="<?php echo admin_url('admin.php?page=employees-list'); ?>" class="add-employee-link btn btn-primary">View Employee +</a>
          </div>
      </div>
  </div>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
      <div class="w-100" style="max-width: 800px;">
          <h1 class="text-center text-primary mb-1">Add New Employee</h1>
          <form method="POST" action="<?php echo admin_url('admin.php?page=register-employee'); ?>" class="form-width mx-auto" id="">
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
                  <label for="emp_image">Cover Image</label>
                  <input type="text" class="form-control" name="emp_image" id="emp_image" readonly>
                  <button class="btn btn-white" id="btn-upload-image" type="button">Upload Image</button>
              </div>
              <!-- <div class="form-group">
                <label for="employee_image">Employee Image</label>
                <input type="file" class="form-control-file" id="employee_image" name="employee_image" readonly>
            </div> -->
              <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                  <small class="text-danger" id="submit_message"></small>
              </div>

              <div class="form-group text-center">
                  <button type="submit" class="btn btn-primary btn-register">Add Employee</button>
              </div>
          </form>
      </div>
  </div>