
function parse_education(divID){
    let data = document.getElementById('school_0').value;
    let container_schools = document.getElementById(divID);
    container_schools.innerHTML='';
    $.ajax({url:'php_modal_connection/return_schools.php',
            method: 'POST',
            data:{action: 'schools_get',data: data},
               success: function(result) {
                let schools = JSON.parse(result);
                let ul = document.createElement('ul');
                schools.forEach(function(school){
                    let list = document.createElement('li');
                    list.id = school['school_id'];
                    list.classList.add('schools_from_sql');
                    list.innerHTML = school['school_name'];
                    ul.appendChild(list);
                });
                container_schools.appendChild(ul);
            }
        });
}
$(document).ready(function(){
    $('#container_schools').on('click','li',function(){
        let schoolID = $(this).attr('id');
        let schoolName = $(this).text();
        $('#school_0').val(schoolName);
        $('#school_0').data('data',schoolID);
        //let attValue = $('#input_school_type').data('data');
        $('#container_schools').empty();
    });
});
function parse_interest(divID){
    let data = document.getElementById('inputing').value;
    let container_schools = document.getElementById(divID);
    container_schools.innerHTML='';
    $.ajax({url:'php_modal_connection/return_interests.php',
            method: 'POST',
            data:{action: 'interest_get',value: data},
               success: function(result) {
                try {
                    let interests = JSON.parse(result);
            
                    let ul = document.createElement('ul');
                    interests.forEach(function(interest) {
                        let list = document.createElement('li');
                        list.id = interest['interest_id'];
                        list.classList.add('interest_from_sql');
                        list.innerHTML = interest['interest_name'];
                        ul.appendChild(list);
                    });
            
                    container_schools.appendChild(ul);
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    console.log('Response:', result);
                }
            }
        });
}
function put_button_in(input_id){
    let input = $(input_id);
    let data_array = input.data('dataArray');
    let input_div = $('#chosen_interest');
    input_div.html('');
    $.ajax({
            url: 'php_modal_connection/return_chosenInterest.php',
            method: 'POST',
            data: {action: 'interest_new', arrayVal: data_array},
            success: function(result){
                let chosen_interests = JSON.parse(result);
                let ul = document.createElement('ul');
                chosen_interests.forEach(function(interest,index){
                    let button = document.createElement('li');
                    button.innerHTML = interest;
                    button.id = data_array[index];
                    button.classList.add('btn_interest');
                    ul.appendChild(button);
                });
                input_div.append(ul);
            }
        });
}
$('#inputing').data('dataArray',[]);
$('#school_0').data('data',[]);
$(document).ready(function(){
    $('#interest_here').on('click','li',function(){
        let interestID = $(this).attr('id');
        let interestName = $(this).text();
        $('#inputing').val(interestName);
        let existingArray = $('#inputing').data('dataArray');
        if (!existingArray){
            existingArray=[];
        }
        if($.inArray(interestID,existingArray) == -1){
            existingArray.push(interestID);
        }
        $('#inputing').data('dataArray',existingArray);
        //let attValue = $('#input_school_type').data('data');
        $('#interest_here').empty();
        put_button_in('#inputing');
    });
});
$(document).ready(function(){
    $('#chosen_interest').on('click','li',function(){
        let interest_id = $(this).attr('id');
        let dataArr = $('#inputing').data('dataArray');
        dataArr = $.grep(dataArr,function(data){
            return data != interest_id;
        });
        $('#inputing').data('dataArray',dataArr);
        put_button_in('#inputing');
    });
});

$(document).ready(function(){
    $('#update_del_form').submit(function(e){
        // e.preventDefault();
        let chosenSchool = $('#school_0').data('data');
        let chosenInterest = $('#inputing').data('dataArray');
        
        let json_chosenSchool = chosenSchool;
        let json_chosenInterest = chosenInterest.map(function(str){
            return parseInt(str,10);
        })
        $('#school_0').val(json_chosenSchool);
        $('#inputing').val(json_chosenInterest);
        console.log($('#school_0').val());
        console.log($('#inputing').val());
        $(this).submit();

        $('#school_0').data('data', []);
        $('#inputing').data('dataArray', []);
        
        $('#chosen_interest').empty();

        this.reset();
    });
});

function toggle_scale(id){
	let form = document.getElementById(id);
	form.classList.toggle('open_close');
	console.log("Hello");
}	
function parse_education(divID){
	let data = document.getElementById('input_school_type').value;
	let container_schools = document.getElementById(divID);
	container_schools.innerHTML='';
	$.ajax({url:'php_modal_connection/return_schools.php',
			method: 'POST',
			data:{action: 'schools_get',data: data},
	           success: function(result) {
	           	let schools = JSON.parse(result);
	          	let ul = document.createElement('ul');
	           	schools.forEach(function(school){
	           		let list = document.createElement('li');
	           		list.id = school['school_id'];
	           		list.classList.add('schools_from_sql');
	           		list.innerHTML = school['school_name'];
	           		ul.appendChild(list);
	           	});
	           	container_schools.appendChild(ul);
	        }
		});
}

function parse_interest(divID){
	let data = document.getElementById('inputing').value;
	let container_schools = document.getElementById(divID);
	container_schools.innerHTML='';
	$.ajax({url:'php_modal_connection/return_interests.php',
			method: 'POST',
			data:{action: 'interest_get',value: data},
	           success: function(result) {
				try {
					let interests = JSON.parse(result);
			
					let ul = document.createElement('ul');
					interests.forEach(function(interest) {
						let list = document.createElement('li');
						list.id = interest['interest_id'];
						list.classList.add('interest_from_sql');
						list.innerHTML = interest['interest_name'];
						ul.appendChild(list);
					});
			
					container_schools.appendChild(ul);
				} catch (error) {
					console.error('Error parsing JSON:', error);
					console.log('Response:', result);
				}
	        }
		});
}
function put_button_in(input_id){
	let input = $(input_id);
	let data_array = input.data('dataArray');
	let input_div = $('#chosen_interest');
	input_div.html('');
	$.ajax({
			url: 'php_modal_connection/return_chosenInterest.php',
			method: 'POST',
			data: {action: 'interest_new', arrayVal: data_array},
			success: function(result){
				let chosen_interests = JSON.parse(result);
				let ul = document.createElement('ul');
				chosen_interests.forEach(function(interest,index){
					let button = document.createElement('li');
					button.innerHTML = interest;
					button.id = data_array[index];
					button.classList.add('btn_interest');
					ul.appendChild(button);
				});
				input_div.append(ul);
			}
		});
}
function get_event(event){
	let image = URL.createObjectURL(event.target.files[0]);
	let image_container = document.getElementById('preview');
	image_container.src = image;
}	
$(document).ready(function(){
	$('#container_schools').on('click','li',function(){
		let schoolID = $(this).attr('id');
		let schoolName = $(this).text();
		$('#input_school_type').val(schoolName);
		$('#input_school_type').data('data',schoolID);
		//let attValue = $('#input_school_type').data('data');
		$('#container_schools').empty();
	});
});
$('#inputing').data('dataArray',[]);
$('#input_school_type').data('data',[]);
$(document).ready(function(){
	$('#interest_here').on('click','li',function(){
		let interestID = $(this).attr('id');
		let interestName = $(this).text();
		$('#inputing').val(interestName);
		let existingArray = $('#inputing').data('dataArray');
		if (!existingArray){
			existingArray=[];
		}
		if($.inArray(interestID,existingArray) == -1){
			existingArray.push(interestID);
		}
		$('#inputing').data('dataArray',existingArray);
		//let attValue = $('#input_school_type').data('data');
		$('#interest_here').empty();
		put_button_in('#inputing');
	});
});
$(document).ready(function(){
	$('#chosen_interest').on('click','li',function(){
		let interest_id = $(this).attr('id');
		let dataArr = $('#inputing').data('dataArray');
		dataArr = $.grep(dataArr,function(data){
			return data != interest_id;
		});
		$('#inputing').data('dataArray',dataArr);
		put_button_in('#inputing');
	});
});
$(document).ready(function(){
	$('#update_del_form').submit(function(e){
		// e.preventDefault();
		let chosenSchool = $('#input_school_type').data('data');
		let chosenInterest = $('#inputing').data('dataArray');
		
		let json_chosenSchool = chosenSchool;
		let json_chosenInterest = chosenInterest.map(function(str){
			return parseInt(str,10);
		})
		$('#input_school_type').val(json_chosenSchool);
		$('#inputing').val(json_chosenInterest);
		$(this).submit();

		$('#input_school_type').data('data', []);
		$('#inputing').data('dataArray', []);
		
		$('#chosen_interest').empty();

		this.reset();
		
		toggle_scale('update_del_form');
	});
});