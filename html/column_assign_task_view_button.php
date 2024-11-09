<?php
// Check if tasks are assigned
$button .= '
<div class="modal fade" id="viewTaskModal" tabindex="-1" role="dialog" aria-labelledby="assignTaskModalTitle" aria-hidden="">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="viewTaskModalTitle">Assigned  Task  </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           <div class="card mt-3">
                <div class="card-body">
                    <h2 class="card-title">Task Assigned to </h2>
                    <ul class="list-group">
                    <li class="list-group-item">No task to show</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>';
