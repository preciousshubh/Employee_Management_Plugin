jQuery(document).ready(function ($) {
  console.log("JS loaded successfully");

  //add more button
  $(document).on("click", "#btn-add-more", function (e) {
    e.preventDefault();
    var remove_btn =
      '<button type="button" class="btn btn-secondary remove-btn">-</button>';
    const add_entry = $(".taskDescription:first").clone();
    console.log(add_entry);
    add_entry.find("textarea").val("");
    add_entry.append(remove_btn);
    $("#taskDescriptionsContainer").append(add_entry);
  });

  //to remove added textarea field for input
  $(document).on("click", ".remove-btn", function (e) {
    e.preventDefault();
    // alert("remove");
    $(this).closest(".taskDescription").remove();
  });

  $(".open-modal-btn").on("click", function () {
    var emp_id = $(this).data("emp-id");
    var emp_name = $(this).data("emp-name");
    $("#assignTaskModalTitle").text("Assign Task To " + emp_name);
    $("#employeeId").val(emp_id);
  });

  $(".view-modal-btn").on("click", function () {
    // alert("view model");
    var task_name = $(this).data("task-name");
    var emp_name = $(this).data("emp-name");
    var task_descriptions = $(this).data("task-description");

    let params = new URLSearchParams(task_descriptions);
    let taskDescriptions = params.getAll("task_description[]");

    $("#viewTaskModalTitle").text("Assigned Task To " + emp_name);
    $(".card-title").text(task_name);
    $(".list-group").empty();

    // Loop through the task descriptions and append them to the list
    taskDescriptions.forEach(function (description) {
      console.log(description);
      $(".list-group").append(
        '<li class="list-group-item">' + description + "</li>"
      );
    });
  });

  // //task form  submit
  // $(document).on("click", "#task-submit-btn", function (e) {
  //   e.preventDefault();
  //   var form_id = "#taskForm";
  //   var task_Data = $("form").serialize();
  //   console.log(task_Data);
  //   $.ajax({
  //     url: "admin-ajax.php",
  //     type: "POST",
  //     data: {
  //       action: "add_task",
  //       formData: task_Data,
  //       nonce: add_new_employee.nonce,
  //     },
  //     success: function (response) {
  //       if (response.success) {
  //         $(form_id).closest(".modal").modal("hide");
  //         $("#taskForm").trigger("reset");
  //         alert("Task assigned successfully!");
  //         window.location.href =
  //           "http://localhost/wordpress/wp-admin/admin.php?page=employees-list";
  //         //load_table();
  //       } else {
  //         $("#error_message").empty().append(response.data.message);
  //         $("#taskForm").trigger("reset");
  //       }
  //     },
  //     error: function (xhr, status, error) {
  //       console.error("AJAX error:", status, error);
  //       alert("An error occurred. Please try again.");
  //     },
  //   });
  // });

  // task form  submit
  $(document).on("click", "#task-submit-btn", function (e) {
    e.preventDefault();
    var emp_id = $("#employeeId").val();
    var task_name = $("#taskName").val();
    var task_description = $("textarea").serialize();
    var form_id = $(".taskform").attr("id");
    console.log(emp_id);
    console.log(task_name);
    console.log(task_description);
    console.log(form_id);
    $.ajax({
      url: "admin-ajax.php",
      type: "POST",
      data: {
        action: "add_task",
        task_name: task_name,
        employee_id: emp_id,
        task_description: task_description,
        nonce: add_new_employee.nonce,
      },
      success: function (response) {
        if (response.success) {
          $("#taskForm").closest(".modal").modal("hide");
          $("#taskForm").trigger("reset");
          alert("Task assigned successfully!");
          window.location.href =
            "http://localhost/wordpress/wp-admin/admin.php?page=employees-list";
          //   //load_table();
        } else {
          //   $("#error_message").empty().append(response.data.message);
          $("#taskForm").trigger("reset");
          alert("not assigned");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX error:", status, error);
        alert("An error occurred. Please try again.");
      },
    });
  });
});
