function addSmilleIngrediensField(){
jQuery('#entrypoint').append("<p><label for='ingrediens'>Ingrediens </label><input type='text' class='text' name='ingredienser[ingrediens][]' value='' /><input type='text' class='antal' name='ingredienser[antal][]' maxlength='2' value='0' /><select name='ingredienser[mangd][]' class='mangd'><option value='0'>Mängd</option><option selected='true' value='1'>st</option><option value='2'>tsk</option><option value='3'>msk</option><option value='4'>dl</option><option value='5'>l</option></select><a href='#del' onClick='removeThisP(this); return false;'>Delete</a></p>");
return false;
}


function addSmilleGenericField(){
jQuery('#entrypoint').append("<p><label for='product_extra'>Extra info </label><input type='text' class='text' name='product_extra[filed_name][]' value=''/><input type='text' class='text' name='product_extra[filed_value][]' value='' /><a href='#del' onClick='removeThisP(this); return false;'>Delete</a></p>");
return false;
}

function removeThisP(event){
jQuery(event).parent('p').remove();
return false;
}
 jQuery(document).ready(function() {
   // put all your jQuery goodness in here.
 jQuery('#publishing-action #publish').click(function(){
if(jQuery('#title').val().length < 1){
jQuery('#title-prompt-text').hide();
jQuery('#title').val("(no title)");
}

});
});

