'use strict';

jQuery(document).ready(function () {

    /*
     Fullscreen background
     */
    $.backstretch([
        "public/images/backgrounds/2.jpg",
        "public/images/backgrounds/3.jpg",
        "public/images/backgrounds/1.jpg"
    ], {duration: 3000, fade: 750});
});



/**
 * Handles admin log in
 * @param {type} e
 * @returns {undefined}
 */
loginForm.onsubmit = function(e){
    e.preventDefault();
    
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    
    
    if(!email || !password){
        var errMsg = !email ? "Enter your email" : "Enter your password";
        
        document.getElementById('errMsg').innerHTML = errMsg;
        
        return;
    }
    
    document.getElementById('errMsg').innerHTML = "Authenticating......";
    
    $.ajax({
        url: appRoot+"home/login",
        method: "POST",
        data: {email:email, password:password}
    }).done(function(returnedData){
            if(returnedData.status === 1){
                document.getElementById('errMsg').innerHTML = "Authenticated. Redirecting....";
                
                //redirect to dashboard
                window.location.replace(appRoot+"dashboard");
            }
            
            else{
                document.getElementById('errMsg').innerHTML = returnedData.msg;
            }
        });
};
