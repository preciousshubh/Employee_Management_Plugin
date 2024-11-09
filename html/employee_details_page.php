<div class="add-employee-button">
    <a href="<?php echo admin_url('admin.php?page=add-new-employee'); ?>" class="add-employee-link">Add New Employee +</a>
    <form id="search_form" class="search-form">
        <input type="text" name="search_text" class="search_text" placeholder="Search...">
        <button type="submit">Search</button>
    </form>
</div>
<div class="container">
    <h1 class="text-center text-primary"><?php _e('Employee Details'); ?></h1>
</div>

<div id="employee_table">

</div>
<div class="container d-flex justify-content-center align-items-center ">
    <div id="show_paginator">

    </div>
</div>