uniquenessUserName = false;
uniquenessEmail = false;
let global_var_index_postion = -1;
let global_var_values = Array(7).fill(null);
function display_welcome() {
  let left_div_parent = document.getElementById("input_div_proponent");
  let welcome = `<a id="stellae" href="../feed/home_page.php">Stellae</a>
					<div class="account_log_in">
						<p id="existing_acc">Already have an aacount?</p>
						<button onclick="window.location.href='../logIn/log_in_page.html'" id="log_in_button">Log In</button>
					</div>`;
  left_div_parent.innerHTML = welcome;
}
let school_array = [
  "Far Eastern University",
  "University of the East",
  "University of Santo Thomas",
  "University of the Philippines",
  "Ateneo de Manila",
  "Centro Escolar University",
  "San Beda University",
  "De La Salle University",
];
let interest_array = [
  "Mathematics",
  "Philosophy",
  "Political Science",
  "Psychology",
  "Biological Science",
  "Contemporary Filipino ",
  "Literature and English",
  "Language Studies",
];
function get_choices_from_db(id) {
  if (id == "schools_available") {
    return school_array;
  } else {
    return interest_array;
  }
}

function change_next_btn(next_btn, display) {
  next_btn.innerHTML = display;
  if (global_var_index_postion != -1 && global_var_index_postion != 7) {
    if (global_var_index_postion == 0){
      var tempArray = global_var_values[global_var_index_postion];
      var store_inputing = document.getElementsByClassName("inputing");
      for (var i = 0; i < store_inputing.length; i++) {
        var element = store_inputing[i];
        element.value = tempArray[i];
      }
    }else{
      var store_inputing = document.getElementsByClassName("inputing")[0];
      store_inputing.value = global_var_values[global_var_index_postion];
    }
  }
}
function remove_selected_school() {
  const schoolsContainer = document.getElementById("schools_available");

  // Get all buttons inside the container
  const schoolButtons =
    schoolsContainer.getElementsByClassName("schools_available");

  // Loop through the buttons and unselect the selected one
  for (const button of schoolButtons) {
    if (
      button.classList.contains("selected") &&
      button.value !== selectedSchool
    ) {
      // Unselect the button
      button.classList.remove("selected");
      break; // Assuming you only want to unselect the first selected button
    }
  }
}

let selectedSchool = "";
let selectedInterests = [];

function school_interest_btns(btn_div_ids) {
  const general_choices_container = document.getElementById(btn_div_ids);
  let choices = get_choices_from_db(btn_div_ids);

  // Get existing buttons if any
  let existingButtons = Array.from(
    general_choices_container.getElementsByClassName(btn_div_ids)
  );

  // Unselect the previously selected school if any
  if (btn_div_ids === "schools_available") {
    const schoolsContainer = document.getElementById("schools_available");

    // Get all buttons inside the container
    const schoolButtons =
      schoolsContainer.getElementsByClassName("schools_available");

    // Loop through the buttons and unselect the selected one
    for (const button of schoolButtons) {
      if (button.classList.contains("selected")) {
        // Unselect the button
        button.classList.remove("selected");
        break; // Assuming you only want to unselect the first selected button
      }
    }
  }

  choices.forEach(function (general) {
    // Check if the button already exists
    let existingButton = existingButtons.find(
      (btn) => btn.innerHTML === general
    );

    let school_html_btn;
    if (existingButton) {
      school_html_btn = existingButton;
    } else {
      school_html_btn = document.createElement("button");
      school_html_btn.className = btn_div_ids;
      school_html_btn.innerHTML = general;
    }

    if (
      (btn_div_ids === "schools_available" && selectedSchool === general) ||
      (btn_div_ids === "interests_available" &&
        selectedInterests.includes(general))
    ) {
      school_html_btn.classList.add("selected");
    }

    existingButtons = existingButtons.filter((btn) => btn !== existingButton);

    school_html_btn.addEventListener("click", function () {
      if (global_var_index_postion == 5 ) {
        if (school_html_btn.classList.contains("selected") || !selectedSchool){
          school_html_btn.classList.toggle("selected");
        }else if(school_html_btn.value == selectedSchool){
          school_html_btn.classList.toggle("selected");
        }
        else{
          remove_selected_school();
          school_html_btn.classList.toggle("selected");
        }
      }else{
        school_html_btn.classList.toggle("selected");
      }

      if (btn_div_ids === "schools_available") {
        selectedSchool = selectedSchool === general ? "" : general;
      } else if (btn_div_ids === "interests_available") {
        if (selectedInterests.includes(general)) {
          selectedInterests = selectedInterests.filter(
            (item) => item !== general
          );
        } else {
          selectedInterests.push(general);
        }
      }

      // console.log("Selected School:", selectedSchool);
      // console.log("Selected Interests:", selectedInterests);
    });

    general_choices_container.appendChild(school_html_btn);
  });

  existingButtons.forEach((btn) => btn.remove());

  if (btn_div_ids === "interests_available") {
    const inputField = document.querySelector(".inputing");

    inputField.addEventListener("keyup", function (event) {
      if (event.key === "Enter") {
        const userInput = inputField.value.trim();
        if (userInput && !selectedInterests.includes(userInput)) {
          selectedInterests.push(userInput);
          interest_array.push(userInput);

          let userInterestBtn = document.createElement("button");
          userInterestBtn.className = "interests_available selected";
          userInterestBtn.innerHTML = userInput;

          userInterestBtn.addEventListener("click", function () {
            userInterestBtn.classList.toggle("selected");
            selectedInterests = selectedInterests.filter(
              (item) => item !== userInput
            );
            // console.log("Selected Interests:", selectedInterests);
          });

          general_choices_container.appendChild(userInterestBtn);

          inputField.value = "";
        }
      }
    });
  } else if (btn_div_ids === "schools_available") {
    const inputField = document.querySelector(".inputing");

    inputField.addEventListener("keyup", function (event) {
      if (event.key === "Enter") {
        remove_selected_school();
        const userInput = inputField.value.trim();
        if (userInput && !school_array.includes(userInput)) {
          selectedSchool = userInput;
          school_array.push(userInput);

          let userSchoolBtn = document.createElement("button");
          userSchoolBtn.className = "schools_available selected";
          userSchoolBtn.innerHTML = userInput;

          userSchoolBtn.addEventListener("click", function () {
            remove_selected_school();
            selectedSchool = "";
            // console.log("Selected School:", selectedSchool);
          });

          general_choices_container.appendChild(userSchoolBtn);

          inputField.value = "";
        }
      }
    });
  }
}

function display_inputs(index) {
  let left_div_parent = document.getElementById("input_div_proponent");
  let next_btn = document.getElementById("go_next_question_btn");

  left_div_parent.innerHTML = "";
  let school_id_div = "schools_available";
  let interest_id_div = "interests_available";
  const list_of_inputs = [
    `<div class="cointain">
								<input class="inputing" type="text" name="" required>
								<p class="placeholder">First Name</p>
                </br>
                <input class="inputing" type="text" name="" required>
								<p class="placeholderLastName">Last Name</p>
							</div>`,
    `<div class="cointain">
								<input class="inputing" type="email" name="" required>
								<p class="placeholder">Email</p>
							</div>`,
    `<div class="cointain">
								<input class="inputing" type="text" name="" required>
								<p class="placeholder">Username</p>
							</div>`,
    `<div class="cointain">
								<input class="inputing" type="password" name="" required>
								<p class="placeholder">Password</p>
							</div>`,
    `<div class="cointain">
								<input class="inputing" type="text" name="" required>
								<p class="placeholder">Education</p>
							</div>`,
    `<div class="cointain">
								<input class="inputing" type="text" name="" required>
								<p class="placeholder">School</p>
								<div id=${school_id_div}>
								</div>
							</div>`,
    `<div class="cointain">
								<input class="inputing" type="text" name="" required>
								<p class="placeholder">Interests </p>
								<div id=${interest_id_div}>
								</div>
							</div>`,
    `<div class="terms_conditions">
								<input id="user_agreed" type="checkbox" name="agreed" required>
								<p>I agree with the Terms and Conditions</p>
							</div>`,
  ];

  if (index == 5) {
    left_div_parent.innerHTML = list_of_inputs[index];
    school_interest_btns(school_id_div);
    change_next_btn(next_btn, "Next &rarrlp;");
  } else if (index == 6) {
    left_div_parent.innerHTML = list_of_inputs[index];
    school_interest_btns(interest_id_div);
    change_next_btn(next_btn, "Next &rarrlp;");
  } else if (index == 7) {
    left_div_parent.innerHTML = list_of_inputs[index];
    change_next_btn(next_btn, "Sign Up");
  } else {
    left_div_parent.innerHTML = list_of_inputs[index];
    change_next_btn(next_btn, "Next &rarrlp;");
  }
}

function active_profile_button(index_position) {
  try {
    if (global_var_index_postion != -1 && global_var_index_postion != 7) {
      if (global_var_index_postion == 0){
        var tempArray = Array();
        var store_inputing = document.getElementsByClassName("inputing");
        var inputArray = Array.from(store_inputing);
        inputArray.forEach(function (name){
          tempArray.push(name.value)
        });
        global_var_values[global_var_index_postion] = tempArray;
      }else{
        var store_value = document.getElementsByClassName("inputing")[0].value;
      if (store_value != "") {
        global_var_values[global_var_index_postion] = store_value;
      }
      }
      
    }
  } catch (TypeError) {
  }
  global_var_index_postion = index_position;
  let parent_div = document.getElementById("questions");
  const children_question = Array.from(parent_div.children);

  children_question.forEach(function (child, get_index_child) {
    if (get_index_child != index_position) {
      child.classList.remove("go_to_here");
    }
  });

  children_question[index_position].classList.toggle("go_to_here");

  let determine_left_display = 0;
  children_question.forEach(function (child, get_index_child) {
    if (child.classList.contains("go_to_here")) {
      determine_left_display++;
    }
  });

  if (determine_left_display == 0) {
    display_welcome();
  } else {
    display_inputs(index_position);
  }
}

function go_next() {
  if (global_var_index_postion == 7) {
    global_var_values[5] = selectedSchool;
    global_var_values[6] = selectedInterests;

    checkInputValidity(global_var_values).then(isValid => {
      if (isValid) {
        submitRegister(global_var_values);
      }
    });
  } else {
    active_profile_button(global_var_index_postion + 1);
  }
}


function checkInputValidity(jsArray) {
  var userAgreedCheckbox = document.getElementById("user_agreed");

  var patterns = {
    "name": /^[a-zA-Z ]+$/,
    "email": /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/,
    "password": /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()-_+=<>?]).{6,28}$/
  };

  if (!patterns.name.test(jsArray[0][0]) || !patterns.name.test(jsArray[0][1])) {
    alert("Name should consist of first name and last name; and no special characters");
    return false;
  }
  if (!patterns.email.test(jsArray[1])) {
    alert("Please enter a valid email");
    return false;
  } else if (!patterns.password.test(jsArray[3])) {
    alert("Password should be at least 6 characters with a capital letter, a small letter, a number, and one special character");
    return false;
  } else if (!jsArray[2] == null) {
    alert("Username must not be null");
    return false;
  } else if (!jsArray[4]) {
    alert("Please enter you highest educational attainment");
    return false;
  } else if (!jsArray[5]) {
    alert("Please enter your school");
    return false;
  } else if (!jsArray[6]) {
    alert("Please enter at least 1 interest");
    return false;
  } else if (!userAgreedCheckbox.checked) {
    alert("Terms and conditions needed to be accepted to proceed");
    return false;
  } else {
    return checkUniqueness(jsArray[2], jsArray[1])
      .then(result => {
        uniquenessEmail = result.uniqueEmail;
        uniquenessUserName = result.uniqueUsername;
        if (!uniquenessEmail) {
          alert("Email already taken!");
          return false;
        } else if (!uniquenessUserName) {
          alert("Username already taken!");
          return false;
        } else {
          return true;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        return false;
      });
  }
}

function checkUniqueness(username, email) {
  return fetch('checkUniqueness.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      username: username,
      email: email
    })
  })
    .then(response => response.json())
    .catch(error => {
      console.error('Error:', error);
      throw error;
    });
}



function submitRegister(jsArray) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'sample.php', true);
  xhr.setRequestHeader('Content-Type', 'application/json');

  xhr.onload = function () {
    if (xhr.status === 200) {
      console.log('Data sent successfully');
      console.log(xhr.responseText);
      if (xhr.responseText == "Record inserted successfully") {
        alert("Registration successful! Please Login");
        window.location.href = '../logIn/log_in_page.html';
      } else {
        alert(xhr.responseText); 
      }
    }
  };
  xhr.send(JSON.stringify({ data: jsArray }));
}
