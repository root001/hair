'use strict';

var spinnerClass = 'fa fa-spinner faa-spin animated';

/*
 * set the appRoot
 * The below line will work for both http, https with or without www
 * @type String
 */
var appRoot = setAppRoot("hair", "");

$(document).ready(function(){
    //for tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    //to search the whole platform
    $("#globeSearch").keyup(function(e){
        alert("Globe Search");
    });
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    //To validate form fields
    $('form').on('change focusout', '.checkField', function(){
        
        //set the id of the span any error will be displayed
        //It's usually the id of the form field plus the string "Err"
        var errSpan = "#"+$(this).attr('id')+"Err";
        
        if($(this).val()){
            $(errSpan).html('');
        }

        else{
            $(errSpan).html('required');
        }
    });
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
   
    
   
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
});



/**
 * Change the class name of elements
 * @param {type} elementId
 * @param {type} newClassName
 * @returns {String}
 */
function changeClassName(elementId, newClassName){
    
    //just change value if it's a single element
    if(typeof(elementId) === "string"){
        $("#"+elementId).attr('class', newClassName);
    }
    
    //loop through if it's an array
    else{
        var i;
    
        for(i in elementId){
            $("#"+elementId[i]).attr('class', newClassName);
        }
    }
    return "";
}


/**
 * Change the innerHTML of elements
 * @param {type} elementId
 * @param {type} newValue
 * @returns {String}
 */
function changeInnerHTML(elementId, newValue){
    //just change value if it's a single element
    if(typeof(elementId) === "string"){
        $("#"+elementId).html(newValue);
    }
    
    //loop through if it's an array
    else{
        var i;
    
        for(i in elementId){
            $("#"+elementId[i]).html(newValue);
        }
    }
    
    
    return "";
}


/**
 * Function to handle the display of messages
 * @param {type} msg
 * @param {type} iconClassName
 * @param {type} color
 * @param {type} time
 * @returns {undefined}
 */
function displayFlashMsg(msg, iconClassName, color, time){
    changeClassName('flashMsgIcon', iconClassName);//set spinner class name
    $("#flashMsg").css('color', color);//change font color
    changeInnerHTML('flashMsg', msg);//set message to display
    $("#flashMsgModal").modal('show');//display modal
    
    //hide the modal after a specified time if time is specified
    if(time){
        setTimeout(function(){$("#flashMsgModal").modal('hide');}, time);
    }
}


/**
 * 
 * @returns {undefined}
 */
function hideFlashMsg(){
    changeClassName('flashMsgIcon', "");//set spinner class name
    $("#flashMsg").css('color', '');//change font color
    changeInnerHTML('flashMsg', "");//set message to display
    $("#flashMsgModal").modal('hide');//hide modal
}


/**
 * Change message being displayed and hide the modal if time is set
 * @param {type} msg
 * @param {type} iconClassName
 * @param {type} color
 * @param {type} time
 * @returns {undefined}
 */
function changeFlashMsgContent(msg, iconClassName, color, time){
    changeClassName('flashMsgIcon', iconClassName);//set spinner class name
    $("#flashMsg").css('color', color);//change font color
    changeInnerHTML('flashMsg', msg);//set message to display
    
    //hide the modal after a specified time if time is specified
    if(time){
        setTimeout(function(){$("#flashMsgModal").modal('hide');}, time);
    }
}


/**
 * To ensure only numbers are allowed as input
 * @param {type} value
 * @param {type} elementId
 * @returns {undefined}
 */
function numOnly(value, elementId){
    $("#"+elementId).val(value.replace(/\D+/g, ""));
}


/**
 * ensure field is properly filled
 * @param {type} value
 * @param {type} errorElementId
 * @returns {undefined}
 * @deprecated v1.0.0
 */
function checkField(value, errorElementId){
    if(value){
        $("#"+errorElementId).html('');
    }
    
    else{
        $("#"+errorElementId).html('required');
    }
}


/**
 * 
 * @param {type} length
 * @returns {String}
 */
function randomString(length){
    var rand = Math.random().toString(36).slice(2).substring(0, length);
    
    return rand;
}


function setAppRoot(devFolderName, prodFolderName){
    var hostname = window.location.hostname;

    /*
     * set the appRoot
     * This will work for both http, https with or without www
     * @type String
     */
    
    //attach trailing slash to both foldernames
    var devFolder = devFolderName+"/";
    var prodFolder = prodFolderName+"/";
    
    var baseURL = hostname === "localhost" ? window.location.origin+"/"+devFolder : window.location.origin+"/"+prodFolder;
    
    return baseURL;
}



function scrollPageToTop(time){
    time = time ? time : 100;//set 100 as default time
    
    //scrolls to top of page
    $('html, body').animate({
        scrollTop: $("html").offset().top
    }, time);
}