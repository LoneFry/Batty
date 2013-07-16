/**
 * Javascript file for 'Batty' issue tracking system
 * File: /^Batty/js/batty.js
 *
 * @category LoneFry
 * @package  Batty
 * @author   LoneFry <dev@lonefry.com>
 * @license  Creative Commons CC-NC-BY-SA
 * @link     http://github.com/LoneFry/Batty
 */

var Batty_XHR;

/**
 * Sends Ajax request to save subscription
 *
 * @return void
 */
function Batty_Subscribe(data) {
    Batty_XHR = window.ActiveXObject?new ActiveXObject("Microsoft.XMLHTTP")
        :new XMLHttpRequest();

    //Set post string
    var post = 'json=' + JSON.stringify(data);

    Batty_XHR.open('POST', '/Batty/subscribe/', true);
    Batty_XHR.onreadystatechange = Batty_Subscribe_;
    Batty_XHR.setRequestHeader("Content-Type", 'application/x-www-form-urlencoded');
    Batty_XHR.setRequestHeader("Content-length", post.length);
    Batty_XHR.send(post);
}

/**
 * Sets response text
 *
 * @return void
 */
function Batty_Subscribe_() {
    if (Batty_XHR.readyState != 4) {
        return;
    }
    if (Batty_XHR.responseText == "1") {
        Batty_Ajax_Message.innerHTML = "Subscription saved.";
    } else {
        Batty_Ajax_Message.innerHTML = "Subscription failed.";
    }
}

/**
 * Checks/Unchecks a list of checkboxes by classname
 *
 * @param object checkBox The check "All" checkbox object
 *
 * @return void
 */
function checkAllClick(checkBox) {
   //The class name of the checkboxes we will check/uncheck
   var className     = checkBox.name.replace('_checkAll', '');

   //Grabs every checkbox of the current class
   var boxes = document.getElementsByClassName(className);

   for (var i = 0; i < boxes.length; i++) {
        boxes[i].checked = checkBox.checked;
   }
}

/**
 * Checks/Unchecks a the "All" checkbox
 *
 * @param object checkBox The checkbox updated
 *
 * @return void
 */
function updateCheckAll(checkBox) {
   //Indicates whether we should check or uncheck the "All" checkbox
   var checkFlag = true;

   if (checkBox.checked) {
       //Grabs every checkbox of the current class
       var boxes = document.getElementsByClassName(checkBox.className);

       //Loops over every checkbox of the current class
       for (var i = 0; i < boxes.length; i++) {
           //"All" is checked, check box
           if (!boxes[i].checked) {
               checkFlag = false;
               break;
           }
       }
   //Unchecks the "All" checkbox
   } else {
       checkFlag = false;
   }
   //Checks/Unchecks the "All" checkbox
   document.getElementsByName(checkBox.className + "_checkAll")[0].checked = checkFlag;
}
