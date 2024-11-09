jQuery(document).ready(function ($) {
  // Function to load employee data
  function loadEmployeeData(page = 1, search_text = "") {
    console.log("Loading data for page: " + page);
    $.ajax({
      url: add_new_employee.ajaxurl,
      type: "POST",
      data: {
        action: "get_all_employee_details",
        page: page,
        search_text: search_text,
        nonce: add_new_employee.nonce,
      },
      success: function (response) {
        console.log("AJAX response for page " + page + ":", response);

        if (response.success) {
          $("#employee_table").html(response.data.html);

          if (response.data.total_pages > 1) {
            updatePagination(response.data.total_pages, page);
          } else {
            $("#show_paginator").html(""); // Hide pagination if only one page
          }
        } else {
          $("#employee_table").html("<p>No employees found.</p>");
          $("#show_paginator").html(""); // Hide pagination if no results are found
        }
      },
      error: function (jqxhr, status, error) {
        console.error("AJAX Error:", jqxhr, status, error);
      },
    });
  }

  // Function to update pagination
  function updatePagination(totalPages, currentPage) {
    console.log(
      "Updating pagination: totalPages=" +
        totalPages +
        ", currentPage=" +
        currentPage
    );
    // Initialize or update bootpag
    $("#show_paginator")
      .bootpag({
        total: totalPages,
        page: currentPage,
        maxVisible: 3,
      })
      .off("page") // Unbind any previous page event handlers to prevent multiple bindings
      .on("page", function (e, number) {
        e.preventDefault();
        var search_text = $(".search_text").val();
        console.log("Page event triggered: " + number);
        // Check if the clicked page is different from the current page to avoid redundant requests
        if (number !== currentPage) {
          console.log("Loading data for page: " + number);
          loadEmployeeData(number, search_text);
        }
      });
  }

  // Load initial employee data
  loadEmployeeData(1, "");

  // Handle search form submission
  $("#search_form").on("keyup", function (e) {
    e.preventDefault();
    var search_text = $(".search_text").val();
    loadEmployeeData(1, search_text);
  });

  // Handle form submission for adding a new employee
  $("#form_data").on("submit", function (e) {
    e.preventDefault();
    var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    var input = document.createElement("input");
    input.name = "timezone";
    input.type = "hidden";
    input.value = timezone;
    $(this).append(input);
    var form_data = new FormData(this);
    form_data.append("action", "add_new_employee");
    form_data.append("nonce", add_new_employee.nonce);
    $.ajax({
      url: "admin-ajax.php",
      type: "POST",
      data: form_data,
      processData: false,
      contentType: false,
      success: function (response) {
        console.log("Response:", response); // Log the response for debugging
        if (response.success) {
          $("#submit_message").html("<h5>" + response.data.message + "</h5>");
          $("#form_data")[0].reset();
          var redirectUrl =
            "http://localhost/wordpress/wp-admin/admin.php?page=employee-details";
          window.location.href = redirectUrl;
        } else {
          $("#submit_message").html("<h5>" + response.data.message + "</h5>");
        }
      },
      error: function (xhr, status, error) {
        console.log("AJAX Error:", xhr, status, error);
        $("#submit_message").html(
          "<h5>An error occurred. Please try again.</h5>"
        );
      },
    });
  });

  $("#btn-upload-image").on("click", function (event) {
    event.preventDefault();
    //alert("c");
    //media object
    var mediaUploader = wp.media({
      title: "Upload Photo",
      multiple: false,
    });
    mediaUploader.open();
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      //console.log(attachment);
      $("#emp_image").val(attachment.url);
    });
  });
});

jQuery(".btn-quick-edit").click(function () {
  if (jQuery(".button-cancel").length > 0) {
    jQuery(".button-cancel").trigger("click");
  }

  var emp_id = jQuery(this).parents("tr").find(".emp-id").text();
  var emp_name = jQuery(this).parents("tr").find(".data_emp_name").text();
  var emp_email = jQuery(this).parents("tr").find(".data_emp_email").text();
  var emp_position = jQuery(this)
    .parents("tr")
    .find(".emp_position")
    .data("emp-position");
  var emp_description = jQuery(this)
    .parents("tr")
    .find(".data_emp_description")
    .text();
  console.log(emp_name);
  console.log(emp_description);
  console.log(emp_position);
  let editHtml =
    `
<td colspan="9">
<table>
<tr>
<td colspan="2">
<legend class="inline-edit-legend">Quick Edit</legend>
</td>
</tr>
<tr>
<td><label for="employee_name">Employee Name</label>
</td>
<td><input type="text"  id="employee_name" name="employee_name" value="` +
    emp_name +
    `">
</td>
</tr>
<tr>
<td><label for="email">Email ID</label>
</td>
<td><input type="email"  id="email" name="email" value="` +
    emp_email +
    `">
</td>
</tr>
<tr>
<td><label for="position">Position</label>
</td>
<td><select  name="position" id="position">
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
</td>
</tr>
<tr>
<td><label for="description">Description</label>
</td>
<td><textarea  id="description" name="description" rows="3" >` +
    emp_description +
    `</textarea>
</td>
</tr>
<tr>
<td><button type="button" class="button button-quick-edit-save button-primary save">Update</button>
</td>
<td><button type="button" class="button button-cancel cancel">Cancel</button>
</td>
</tr>
</table>
</td>
  `;
  let existingRow = jQuery(this).parents("tr").html();
  jQuery(this).parents("tr").html(editHtml);

  jQuery(".button-cancel").click(function () {
    jQuery(this).parents("tr").html(existingRow);
    existingRow = " ";
  });
});
