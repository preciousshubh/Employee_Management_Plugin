<?php
$button .= '
<div class="modal fade" id="assignTaskModal" tabindex="-1" role="dialog" aria-labelledby="assignTaskModalTitle" aria-hidden="">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="assignTaskModalTitle">Assign Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="taskForm" class="taskform">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="taskName">Task Name</label>
                        <input type="text" class="form-control" name="task_name" id="taskName" placeholder="Enter task name">
                    </div>
                    <div id="taskDescriptionsContainer">
                        <div class="form-group taskDescription">
                            <label for="taskDescription">Task Description</label>
                            <textarea name="task_description[]" class="form-control taskDescription" rows="3" placeholder="Enter task description"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="employee_id" id="employeeId">
                    
                    <button type="submit" class="btn btn-primary" id="task-submit-btn">Assign</button>
                </div>
            </form>
            <div class="modal-footer">
                <h5 class="text-danger" id="error_message"></h5>
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="btn-add-more">Add More +</button>
            </div>
        </div>
    </div>
</div>
';
