const patternsTo = {
	"name": /^[a-zA-Z_ ]+$/,
	'username': /^[a-zA-Z_]+$/,
	"email": /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/,
	"password": /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()-_+=<>?]).{6,28}$/
  };
  function validateAndSubmit(arr_text, arr) {
	let isValid = validate_inputs(arr_text, arr);
	console.log(isValid);
	return isValid;
  }function validate_inputs(arr_text, arr) {
	let f_name = document.getElementById(arr_text[0]).value;
	let l_name = document.getElementById(arr_text[1]).value;
	let userName = document.getElementById(arr_text[2]).value;
	let email_x = document.getElementById(arr[0]).value;
	let password_x = document.getElementById(arr[1]).value;
  
	// Validate input fields
	if (!patternsTo.name.test(f_name) && f_name) {
	  alert(`Error: ${f_name} is Invalid!`);
	  return false;
	}
	if (!patternsTo.name.test(l_name) && l_name) {
	  alert(`Error: ${l_name} is Invalid!`);
	  return false;
	}
	if (!patternsTo.username.test(userName) && userName) {
	  alert(`Error: ${userName} is Invalid! only small letters and '_'`);
	  return false;
	}
	if (!patternsTo.email.test(email_x) && email_x) {
	  alert(`Error: Invalid Email Detected`);
	  return false;
	}
	if (!patternsTo.password.test(password_x) && password_x) {
	  alert(`Error: Password should be at least 6 characters with a capital letter, a small letter, a number, and one special character`);
	  return false;
	}
	return true;
  }
  
  function toggle_scale(id) {
	let form = document.getElementById(id);
	form.classList.toggle("open_close");
	console.log("Hello");
  }
  function parse_education(divID) {
	let data = document.getElementById("input_school_type").value;
	let container_schools = document.getElementById(divID);
	container_schools.innerHTML = "";
	$.ajax({
	  url: "php_modal_connection/return_schools.php",
	  method: "POST",
	  data: { action: "schools_get", data: data },
	  success: function (result) {
		let schools = JSON.parse(result);
		let ul = document.createElement("ul");
		schools.forEach(function (school) {
		  let list = document.createElement("li");
		  list.id = school["school_id"];
		  list.classList.add("schools_from_sql");
		  list.innerHTML = school["school_name"];
		  ul.appendChild(list);
		});
		container_schools.appendChild(ul);
	  },
	});
  }
  
  function parse_interest(divID) {
	let data = document.getElementById("inputing").value;
	let container_schools = document.getElementById(divID);
	container_schools.innerHTML = "";
	$.ajax({
	  url: "php_modal_connection/return_interests.php",
	  method: "POST",
	  data: { action: "interest_get", value: data },
	  success: function (result) {
		try {
		  let interests = JSON.parse(result);
  
		  let ul = document.createElement("ul");
		  interests.forEach(function (interest) {
			let list = document.createElement("li");
			list.id = interest["interest_id"];
			list.classList.add("interest_from_sql");
			list.innerHTML = interest["interest_name"];
			ul.appendChild(list);
		  });
  
		  container_schools.appendChild(ul);
		} catch (error) {
		  console.error("Error parsing JSON:", error);
		  console.log("Response:", result);
		}
	  },
	});
  }
  
  function put_button_in(input_id) {
	let input = $(input_id);
	let data_array = input.data("dataArray");
	let input_div = $("#chosen_interest");
	input_div.html("");
	$.ajax({
	  url: "php_modal_connection/return_chosenInterest.php",
	  method: "POST",
	  data: { action: "interest_new", arrayVal: data_array },
	  success: function (result) {
		let chosen_interests = JSON.parse(result);
		let ul = document.createElement("ul");
		chosen_interests.forEach(function (interest, index) {
		  let button = document.createElement("li");
		  button.innerHTML = interest;
		  button.id = data_array[index];
		  button.classList.add("btn_interest");
		  ul.appendChild(button);
		});
		input_div.append(ul);
	  },
	});
  }
  function get_event(event) {
	let image = URL.createObjectURL(event.target.files[0]);
	let image_container = document.getElementById("preview");
	image_container.src = image;
  }
  $(document).ready(function () {
	$("#container_schools").on("click", "li", function () {
	  let schoolID = $(this).attr("id");
	  let schoolName = $(this).text();
	  $("#input_school_type").val(schoolName);
	  $("#input_school_type").data("data", schoolID);
	  //let attValue = $('#input_school_type').data('data');
	  $("#container_schools").empty();
	});
  });
  $("#inputing").data("dataArray", []);
  $("#input_school_type").data("data", []);
  $(document).ready(function () {
	$("#interest_here").on("click", "li", function () {
	  let interestID = $(this).attr("id");
	  let interestName = $(this).text();
	  $("#inputing").val(interestName);
	  let existingArray = $("#inputing").data("dataArray");
	  if (!existingArray) {
		existingArray = [];
	  }
	  if ($.inArray(interestID, existingArray) == -1) {
		existingArray.push(interestID);
	  }
	  $("#inputing").data("dataArray", existingArray);
	  //let attValue = $('#input_school_type').data('data');
	  $("#interest_here").empty();
	  put_button_in("#inputing");
	});
  });
  $(document).ready(function () {
	$("#chosen_interest").on("click", "li", function () {
	  let interest_id = $(this).attr("id");
	  let dataArr = $("#inputing").data("dataArray");
	  dataArr = $.grep(dataArr, function (data) {
		return data != interest_id;
	  });
	  $("#inputing").data("dataArray", dataArr);
	  put_button_in("#inputing");
	});
  });
  $(document).ready(function () {
	$("#update_del_form").submit(function (e) {
	  // e.preventDefault();
	  let chosenSchool = $("#input_school_type").data("data");
	  let chosenInterest = $("#inputing").data("dataArray");
  
	  let json_chosenSchool = chosenSchool;
	  let json_chosenInterest = chosenInterest.map(function (str) {
		return parseInt(str, 10);
	  });
	  $("#input_school_type").val(json_chosenSchool);
	  $("#inputing").val(json_chosenInterest);
	  $(this).submit();
  
	  $("#input_school_type").data("data", []);
	  $("#inputing").data("dataArray", []);
  
	  $("#chosen_interest").empty();
  
	  this.reset();
  
	  toggle_scale("update_del_form");
	});
  });