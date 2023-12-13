let global_var_index_postion = -1;

function display_welcome(){
	let left_div_parent = document.getElementById('input_div_proponent');
	let welcome = `<p id="welcome_new_user">Welcome New User to Stellae!</p>
					<div class="account_log_in">
						<p id="existing_acc">Already have an aacount?</p>
						<button id="log_in_button">Log In</button>
					</div>`;
	left_div_parent.innerHTML = welcome;
}
function get_choices_from_db(id){
	let school_array = ['Far Eastern University',
						'University of the East',
						'University of Santo Thomas',
						'University of the Philippines',
						'Ateneo de Manila',
						'Centro Escolar University',
						'San Beda University',
						'De La Salle University'
						];
	let interest_array = ['Mathematics',
						'Philosophy',
						'Political Science',
						'Psychology',
						'Biological Science',
						'Contemporary Filipino ',
						'Literature and English',
						'Language Studies'
						];
	if (id == 'schools_available'){
		return school_array;
	}else{
		return interest_array;
	}

}

function change_next_btn(next_btn,display){
	next_btn.innerHTML = display;
}

function school_interest_btns(btn_div_ids){
	const general_choices_container = document.getElementById(btn_div_ids);
	general_choices_container.innerHTML = '';
	let choices = get_choices_from_db(btn_div_ids);

	choices.forEach(function(general){
		let school_html_btn = document.createElement('button');
		school_html_btn.className = btn_div_ids;
		school_html_btn.innerHTML = general;
		general_choices_container.appendChild(school_html_btn);
	});
}

function display_inputs(index){
	let left_div_parent = document.getElementById('input_div_proponent');
	let next_btn = document.getElementById('go_next_question_btn');

	left_div_parent.innerHTML = '';
	let school_id_div = 'schools_available';
	let interest_id_div = 'interests_available';
	const list_of_inputs = [`<div class="cointain">
								<input class="inputing" type="text" name="" required>
								<p class="placeholder">Full Name</p>
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
							</div>`];
	
	if (index == 5){
		left_div_parent.innerHTML = list_of_inputs[index];
		school_interest_btns(school_id_div);
		change_next_btn(next_btn,'Next &rarrlp;');
	}else if (index == 6){
		left_div_parent.innerHTML = list_of_inputs[index];
		school_interest_btns(interest_id_div);
		change_next_btn(next_btn,'Next &rarrlp;');
	}else if (index == 7){
		left_div_parent.innerHTML = list_of_inputs[index];
		change_next_btn(next_btn,'Sign Up');
	}else{
		left_div_parent.innerHTML = list_of_inputs[index];
		change_next_btn(next_btn,'Next &rarrlp;');
	}
}

function active_profile_button(index_position){
	global_var_index_postion = index_position;
	let parent_div = document.getElementById('questions');
	const children_question = Array.from(parent_div.children);

	children_question.forEach(function(child,get_index_child){
		if (get_index_child != index_position){
			child.classList.remove('go_to_here');
		}
	});

	children_question[index_position].classList.toggle('go_to_here');

	let determine_left_display = 0;
	children_question.forEach(function(child,get_index_child){
		if (child.classList.contains('go_to_here')){
			determine_left_display++;
		}
	});

	if (determine_left_display == 0){
		display_welcome();
	}
	else{
		display_inputs(index_position);
	}
}

function go_next(){
	if (global_var_index_postion == 7){
		console.log('Congratulations User you have been registered!')
	}else{
		active_profile_button(global_var_index_postion+1);
	}
}
