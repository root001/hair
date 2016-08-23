'use strict';

$(document).ready(function(){
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
    //handles the addition of new user details .i.e. when "add user" button is clicked
    $("#addstylist").click(function(e){    
        e.preventDefault();
        
        //reset all error msgs in case they are set
        changeInnerHTML(['usernameErr', 'firstNameErr', 'lastNameErr', 'mobile1Err', 'mobile2Err', 'emailErr', 'streetErr', 
            'cityErr', 'stateErr', 'countryErr', 'passwordErr', 'passwordConfErr'], "");
        
        var username = $("#username").val();
        var firstName = $("#firstName").val();
        var lastName = $("#lastName").val();
        var mobile1 = $("#mobile1").val();
        var mobile2 = $("#mobile2").val();
        var email = $("#email").val();
        var password = $("#password").val();
        var passwordConf = $("#passwordConf").val();
        var street = $("#street").val();
        var city = $("#city").val();
        var state = $("#state").val();
        var country = $("#country").val();
        
        //ensure all required fields are filled
        if(!username || !firstName || !lastName || !mobile1 || !email || !password || !passwordConf){
            !firstName ? changeInnerHTML('firstNameErr', "required") : "";
            !lastName ? changeInnerHTML('lastNameErr', "required") : "";
            !mobile1 ? changeInnerHTML('mobile1Err', "required") : "";
            !email ? changeInnerHTML('emailErr', "required") : "";
            !username ? changeInnerHTML('usernameErr', "required") : "";
            !password ? changeInnerHTML('passwordErr', "required") : "";
            !passwordConf ? changeInnerHTML('passwordConfErr', "required") : "";
            
            return;
        }
        
        
        var logo = document.getElementById('logo').files;

        //set info to send to server
        var formInfo = new FormData();

        for (var i = 0; i < logo.length; i++) {
            var file = logo[i];

            // Add the file to the request.
            formInfo.append("logo", file);
        }
        
        
        //add other info to the formInfo obj
        formInfo.append("username", username);
        formInfo.append("first_name", firstName);
        formInfo.append("last_name", lastName);
        formInfo.append("email", email);
        formInfo.append("mobile_1", mobile1);
        formInfo.append("mobile_2", mobile2);
        formInfo.append("profession", profession);
        formInfo.append("password", password);
        formInfo.append("passwordConf", passwordConf);
        formInfo.append("street", street);
        formInfo.append("city", city);
        formInfo.append("state", state);
        formInfo.append("country", country);
        
        //display message telling user action is being processed
        $("#fMsgIcon").attr('class', spinnerClass);
        $("#fMsg").html(" Processing...");
        
    });

});	
     ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////