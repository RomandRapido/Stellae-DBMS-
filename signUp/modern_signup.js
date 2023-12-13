const patterns = {
    "name": /^[a-zA-Z_ ]+$/,
    'username': /^[a-z_]+$/,
    "email": /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/,
    "password": /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()-_+=<>?]).{6,28}$/
};
function validate_inputs(arr_text,arr){
    arr_text.forEach(function(value){
        let input_txt = document.getElementById(value);
        if(!patterns.name.test(input_txt.value)){
            alert(`Error: Number Detected on ${input_txt.name}`);
            return false;
        }
    });
    let userName = document.getElementById(arr_text[2]);
    if(!patterns.username.test(userName.value)){
        alert(`Error: Invalid Username Detected`);
        return false;
    }

    let email_txt  =document.getElementById(arr[0]);
    if(!patterns.email.test(email_txt.value)){
        alert(`Error: Invalid Email Detected`);
        return false;
    }
    let password_txt  =document.getElementById(arr[1]);
    if(!patterns.password.test(password_txt.value)){
        alert(`Error: Password should be at least 6 characters with a capital letter, a small letter, a number, and one special character`);
        return false;
    }
    $.ajax({
        url:'return_uniqueness.php',
        type:'POST',
        data:{action: 'return_bool',data: [arr_text[2],email_txt.value]},
        success: function(response){
            if(response.finding1 == true && response.finding2 == true){
                return true;
            }else{
                if(response.finding1 == false && response.finding2 ==false){
                    alert('Error: Email and Username is Taken');
                    return false;
                }else if (response.finding1 == true && response.finding2 == false){
                    alert('Error: Username is Taken');
                    return false;
                }else if (response.finding1 == false && response.finding2 == true){
                    alert('Error: Email is Taken');
                    return false;
                }
            }
        }
    });
}
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
