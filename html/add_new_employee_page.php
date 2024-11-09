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
                 <label for="employee_image">Employee Image</label>
                 <input type="file" class="form-control-file" id="employee_image" name="employee_image">
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