'use strict';

$(document).ready(function(){
    //checkDocumentVisibility(checkLogin);//check document visibility in order to confirm user's log in status
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //ensure only number is entered in fields
    $("#mobile1, #mobile2, #mobile1Edit, #mobile2Edit").change(function(){
        $(this).val($(this).val().replace(/\D+/g, ""));
    });
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    //handles the addition of new user details .i.e. when "add user" button is clicked
    $("#addStylistSubmit").click(function(e){    
        e.preventDefault();
        
        //reset all error msgs in case they are set
        changeInnerHTML(['usernameErr', 'firstNameErr', 'lastNameErr', 'mobile1Err', 'mobile2Err', 'emailErr','workdayErr','fromtimeErr','totimeErr','aboutErr','streetErr', 'cityErr', 'stateErr', 'countryErr', 'passwordErr', 'passwordConfErr'], "");
        
        var username = $("#username").val();
        var firstName = $("#firstName").val();
        var lastName = $("#lastName").val();
        var mobile1 = $("#mobile1").val();
        var mobile2 = $("#mobile2").val();
        var email = $("#email").val();
        var about = $("#about").val();
		var workday = $("#workday").val();
        var fromtime = $("#fromtime").val();
        var totime = $("#totime").val();
        var password = $("#password").val();
        var passwordConf = $("#passwordConf").val();
        var street = $("#street").val();
        var city = $("#city").val();
        var state = $("#state").val();
        var country = $("#country").val();
        
        //ensure all required fields are filled
        if(!username || !firstName || !lastName || !mobile1 || !email || !workday || !password || !passwordConf){
            !firstName ? changeInnerHTML('firstNameErr', "required") : "";
            !lastName ? changeInnerHTML('lastNameErr', "required") : "";
            !mobile1 ? changeInnerHTML('mobile1Err', "required") : "";
            !email ? changeInnerHTML('emailErr', "required") : "";
            !username ? changeInnerHTML('usernameErr', "required") : "";
            !workday ? changeInnerHTML('professionErr', "required") : "";
            !password ? changeInnerHTML('passwordErr', "required") : "";
            !passwordConf ? changeInnerHTML('passwordConfErr', "required") : "";
            
			!password == passwordConf ? changeInnerHTML('passwordConfErr', "not matching") : "";
			
            return;
        }
        
        var logo = document.getElementById('logo').files;
	//	var portfolio = document.getElementById('portfolio').files;
		var profile = document.getElementById('profile').files;

        //set info to send to server
        var formInfo = new FormData();
//		var newArray = [];

        for (var i = 0; i < logo.length; i++) {
            var file = logo[i];

            // Add the file to the request.
            formInfo.append("logo", file);
        }
		
		for (var i = 0; i < profile.length; i++) {
            var file = profile[i];

            // Add the file to the request.
            formInfo.append("profile", file);
        }

	/*	for (var i = 0; i < portfolio.length; i++) {
		//	alert(portfolio[i]);  
            var file = portfolio[i];
            // Add the file to the request.
            newArray.push(file);
			var formInfo1 = $.map(newArray, function(value, index) {
				return [value];
			});
			formInfo.append("portfolio",formInfo1);
		//		alert(formInfo);
			
        }**/
        			
        //add other info to the formInfo obj
        formInfo.append("username", username);
        formInfo.append("first_name", firstName);
        formInfo.append("last_name", lastName);
        formInfo.append("email", email);
        formInfo.append("mobile_1", mobile1);
        formInfo.append("mobile_2", mobile2);
        formInfo.append("about", about);
        formInfo.append("password", password);
        formInfo.append("passwordConf", passwordConf);
		formInfo.append("work_day", workday);
        formInfo.append("from_time", fromtime);
        formInfo.append("to_time", totime);
        formInfo.append("street", street);
        formInfo.append("city", city);
        formInfo.append("state", state);
        formInfo.append("country", country);
        
	//	alert(formInfo);
	//	alert(newArray);
		
			
        //display message telling user action is being processed
        $("#fMsgIcon").attr('class', spinnerClass);
        $("#fMsg").html(" Processing...");
        
        //make ajax request if all is well
        $.ajax({
            method: "POST",
            url: appRoot+"views/workin/Stylist.php",
            data: formInfo,
            cache: false,
            processData: false,
            contentType: false
        }).done(function(returnedData){
			alert(returnedData);
                $("#fMsgIcon").removeClass();//remove spinner
                
                if(returnedData.status === 1){
                    $("#fMsg").css('color', 'green').html("Account created");
                    
                    //reset the form and close the modal
                    document.getElementById("addNewStylistForm").reset();
					
                    //reset the form and close the modal
                    setTimeout(function(){
                        $("#fMsg").html("");
                        $("#addNewStylistModal").modal('hide');
                    }, 2000);
                    
                    //reset all error msgs in case they are set
                    changeInnerHTML(['usernameErr', 'firstNameErr', 'lastNameErr', 'mobile1Err', 'mobile2Err', 'emailErr', 'streetErr', 
                        'cityErr', 'stateErr', 'countryErr', 'aboutErr', 'passwordErr', 'passwordConfErr'], "");
                                      
                }
                
                else{
                    //display error message returned
                    $("#fMsg").css('color', 'red').html(returnedData.msg);

                    //display individual error messages if applied
                    $("#usernameErr").html(returnedData.username);
                    $("#firstNameErr").html(returnedData.first_name);
                    $("#lastNameErr").html(returnedData.last_name);
                    $("#emailErr").html(returnedData.email);
                    $("#mobile1Err").html(returnedData.mobile_1);
                    $("#mobile2Err").html(returnedData.mobile_2);
                    $("#aboutErr").html(returnedData.about);
                    $("#passwordErr").html(returnedData.password);
                    $("#passwordConfErr").html(returnedData.passwordConf);
                    $("#cityErr").html(returnedData.city);
                    $("#stateErr").html(returnedData.state);
                    $("#countryErr").html(returnedData.country);
                    $("#logoErr").html(returnedData.logo);
                }
            }).fail(function(){
				alert('no data seen');
                if(!navigator.onLine){
                    $("#fMsg").css('color', 'red').text("Network error! Pls check your network connection");
                }
                
                else{
                    $("#fMsg").css('color', 'red').text("Unable to process your reuest at this time");
                }
            });
    });
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    //handles the updating of customer details update
    $("#editCustSubmit").click(function(e){
        e.preventDefault();
        
        //reset all error msgs in case they are set
        changeInnerHTML(['titleEditErr', 'firstNameEditErr', 'lastNameEditErr', 'mobile1EditErr', 'mobile2EditErr', 'emailEditErr', 
            'genderEditErr', 'addressEditErr', 'cityEditErr', 'stateEditErr', 'countryEditErr', 'membershipIdEditErr'], "");
        
        var title = $("#titleEdit").val();
        var firstName = $("#firstNameEdit").val();
        var lastName = $("#lastNameEdit").val();
        var otherName = $("#otherNameEdit").val();
        var mobile1 = $("#mobile1Edit").val();
        var mobile2 = $("#mobile2Edit").val();
        var email = $("#emailEdit").val();
        var gender = $("#genderEdit").val();
        var membershipId = $("#membershipIdEdit").val();
        var address = $("#addressEdit").val();
        var city = $("#cityEdit").val();
        var state = $("#stateEdit").val();
        var country = $("#countryEdit").val();
        var custId = $("#custId").val();
        
        //ensure all required fields are filled
        if(!firstName || !lastName || !mobile1 || !email || !gender || !address || !city || !state || !country || !membershipId){
            !firstName ? changeInnerHTML('firstNameEditErr', "required") : "";
            !lastName ? changeInnerHTML('lastNameEditErr', "required") : "";
            !mobile1 ? changeInnerHTML('mobile1EditErr', "required") : "";
            !email ? changeInnerHTML('emailEditErr', "required") : "";
            !gender ? changeInnerHTML('genderEditErr', "required") : "";
            !membershipId ? changeInnerHTML('membershipIdEditErr', 'required') : "";
            !address ? changeInnerHTML('addressEditErr', "required") : "";
            !city ? changeInnerHTML('cityEditErr', 'required') : "";
            !state ? changeInnerHTML('stateEditErr', 'required') : "";
            !country ? changeInnerHTML('countryEditErr', 'required') : "";
            
            return;
        }
        
        if(!custId){
            $("#fMsgEdit").text("An error occured while trying to update customer's details");
            return;
        }
        
        //display message telling user action is being processed
        $("#fMsgEditIcon").attr('class', spinnerClass);
        $("#fMsgEdit").text(" Updating details...");
        
        //make ajax request if all is well
        $.ajax({
            method: "POST",
            url: appRoot+"customers/update",
            data: {title:title, firstName:firstName, lastName:lastName, otherName:otherName, mobile1:mobile1, mobile2:mobile2, 
                email:email, gender:gender, address:address, custId:custId, city:city, state:state, country:country,
                membershipId:membershipId},
            success: function(returnedData){
                $("#fMsgEditIcon").removeClass();//remove spinner
                
                if(returnedData.status === 1){
                    $("#fMsgEdit").css('color', 'green').text(returnedData.msg);
                    
                    //reset the form and close the modal
                    setTimeout(function(){
                        $("#fMsgEdit").text("");
                        $("#editCustModal").modal('hide');
                    }, 2000);
                    
                    //reset all error msgs in case they are set
                    changeInnerHTML(['titleEditErr', 'firstNameEditErr', 'lastNameEditErr', 'mobile1EditErr', 'mobile2EditErr', 
                        'emailEditErr', 'genderEditErr', 'addressEditErr', 'cityEditErr', 'stateEditErr', 'countryEditErr',
                        'membershipIdEditErr'], "");
                    
                    //refresh customer list table
                    lau_();
					
					//call function to send SMS to user
					//sendSMS(msg, numbers) in "main.js"
					var msgToSendAsSMS = "Your details on our server was successfully modified. Check your email for more info. Thank you.";
					
					sendSMS(msgToSendAsSMS, mobile1);
                    
                }
                
                else{
                    //display error message returned
                    $("#fMsgEdit").css('color', 'red').html(returnedData.msg);

                    //display individual error messages if applied
                    $("#titleEditErr").html(returnedData.title);
                    $("#firstNameEditErr").html(returnedData.firstName);
                    $("#lastNameEditErr").html(returnedData.lastName);
                    $("#otherNameEditErr").html(returnedData.otherName);
                    $("#mobile1EditErr").html(returnedData.mobile1);
                    $("#mobile2EditErr").html(returnedData.mobile2);
                    $("#emailEditErr").html(returnedData.email);
                    $("#genderEditErr").html(returnedData.gender);
                    $("#membershipIdEditErr").html(returnedData.membershipId);
                    $("#addressEditErr").html(returnedData.address);
                    $("#cityEditErr").html(returnedData.city);
                    $("#stateEditErr").html(returnedData.state);
                    $("#countryEditErr").html(returnedData.country);
                }
            },
            
            error: function(){
                if(!navigator.onLine){
                    $("#fMsgEdit").css('color', 'red').text("Network error! Please check your network connection");
                }
            }
        });
    });
    

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //searching through a customer's transactions history
    $("#searchCustTrans").keyup(function(){
       var value = $("#searchCustTrans").val();
        var custId = $("#curDisplayedCustId").html();

        if(value){//search only if there is at least a char in input
            $.ajax({
                type: "get",
                url: appRoot+"search/custtranssearch",
                data: {v:value, custId:custId},
                success: function(returnedData){
                    if(returnedData.status === 1){
                        $("#custTransTable").html(returnedData.custTransTable);
                    }
                }
            });
        }

        else{
            vup_();
        } 
    });
    
    
    
    /*
     * When the close button is clicked on the div showing the list of a projects created by a user
     */
    $("#closeUserProjectList").click(function(){
        $("#userProjectList").addClass('hidden');//hide the div
        $("#allUsersDiv").removeClass('hidden');
        
        //scroll page to top
        scrollPageToTop();
    });




/*
***************************************************************************************************************************************
***************************************************************************************************************************************
***************************************************************************************************************************************
***************************************************************************************************************************************
***************************************************************************************************************************************
*/


/**
 * To show modal to edit customer details
 * @param {type} custId
 * @returns {undefined}
 */
function editCust(custId){
    //show modal, get customer info and populate the form with it
    $("#editCustModal").modal('show');
    $("#fMsgEditIcon").attr('class', spinnerClass);
    $("#fMsgEdit").text("Fetching details...");
    
    $.ajax({
        type: "post",
        url: appRoot+"customers/getcustbio",
        data: {custId:custId},
        success: function(returnedData){
            if(returnedData.status === 1){
                $("#titleEdit").val(returnedData.title);
                $("#membershipIdEdit").val(returnedData.membershipId);
                $("#firstNameEdit").val(returnedData.firstName);
                $("#lastNameEdit").val(returnedData.lastName);
                $("#otherNameEdit").val(returnedData.otherName);
                $("#mobile1Edit").val(returnedData.mobile1);
                $("#mobile2Edit").val(returnedData.mobile2);
                $("#emailEdit").val(returnedData.email);
                $("#genderEdit").val(returnedData.gender);
                $("#addressEdit").val(returnedData.address);
                $("#cityEdit").val(returnedData.city);
                $("#stateEdit").val(returnedData.state);
                $("#countryEdit").val(returnedData.country);
                $("#custId").val(custId);
                
                $("#fMsgEdit").text("");
                $("#fMsgEditIcon").removeClass();
            }
            
            else{
                $("#fMsgEdit").text("Error fetching customer details");
                $("#fMsgEditIcon").removeClass();
            }
        }
    });
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * lau_ = "Load all users"
 * @returns {undefined}
 */
function lau_(url){
    var orderBy = $("#userListSortBy").val().split("-")[0];
    var orderFormat = $("#userListSortBy").val().split("-")[1];
    var limit = $("#userListPerPage").val();
    
    $.ajax({
        method:'get',
        url: url ? url : appRoot+"users/lau_/",
        data: {orderBy:orderBy, orderFormat:orderFormat, limit:limit},
        
        success: function(returnedData){
            hideFlashMsg();
			
            $("#allUsers").html(returnedData.usersTable);
        },
        
        error: function(){
            
        }
    });
}


});