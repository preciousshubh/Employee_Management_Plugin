 <div class="container">
     <div class="row">
         <div class="col-12">
             <div class="table-responsive">
                 <table class="table wp-last-table fixed widefat striped">
                     <thead class="thead-dark">
                         <tr>
                             <th class="text-center"><?php _e('Employee ID'); ?></th>
                             <th class="text-center"><?php _e('Employee Name'); ?></th>
                             <th class="text-center"><?php _e('Email ID'); ?></th>
                             <th class="text-center"><?php _e('Position'); ?></th>
                             <th class="text-center"><?php _e('Description'); ?></th>
                             <th class="text-center"><?php _e('Date'); ?></th>
                             <th class="text-center"><?php _e('Photo'); ?></th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php
                            if ($employees) {
                                function entry_date($date_time)
                                {
                                    if (!empty($date_time)) {
                                        $date_time = strtotime($date_time);
                                        $new_date_time = date("m/d/y H:i:sA", $date_time);
                                        return ($new_date_time);
                                    } else {
                                        return 'N/A';
                                    }
                                }
                                function employee_position($position)
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

                                foreach ($employees as $employee) { ?>
                                 <tr>
                                     <td class="text-center"><?php echo esc_html($employee['emp_id']); ?></td>
                                     <td class="text-center"><?php echo esc_html($employee['emp_name']); ?></td>
                                     <td class="text-center"><?php echo esc_html($employee['emp_email']); ?></td>
                                     <td class="text-center"><?php echo esc_html(employee_position($employee['emp_position'])); ?></td>
                                     <td class="text-center"><?php echo esc_html($employee['emp_description']); ?></td>
                                     <td class="text-center"><?php echo esc_html(entry_date($employee['date_time'])); ?></td>
                                     <td class="text-center">
                                         <?php if (!empty($employee['emp_image'])) { ?>
                                             <img src="<?php echo esc_url($employee['emp_image']); ?>" alt="Employee Image" style="width: 100px; height: auto;">
                                         <?php } else { ?>
                                             No Image
                                         <?php } ?>
                                     </td>
                                 </tr>
                             <?php }

                                echo 'total_pages : ' . $total_pages . "<br>";
                            } else { ?>
                             <tr>
                                 <td colspan="6" class="text-center"><?php _e('No data found.'); ?></td>
                             </tr> <?php }
                                    ?>

                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </div>