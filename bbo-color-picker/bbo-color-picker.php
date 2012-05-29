<?php
/*
Plugin Name: bbo-color-picker	
Plugin URI: http://thisoneisgreen.com/
Description: Lägger till de extra postfuntionalitet
Version: 0.0.1
Author: Robin Westerlundh
Author URI: http://thisoneisgreen.com/
*/
?>
<?php
add_action('admin_init','bbo_color_picker');			 
function bbo_color_picker($post) {
 				add_meta_box('bbo_color_setup', 'Pick a color', 'bbo_color_setup', 'post', 'normal', 'high');
}
 
		function bbo_color_setup($post, $metabox) {
			$content =  get_post_meta( $post->ID,'bbo_color_field', false );
			?>
            <script>
var bbo_color_field;
function getMousePos(canvas, evt){
    // get canvas position
    var obj = canvas;
    var top = 0;
    var left = 0;
    while (obj.tagName != 'BODY') {
        top += obj.offsetTop;
        left += obj.offsetLeft;
        obj = obj.offsetParent;
    }
 
    // return relative mouse position
    var mouseX = evt.clientX - left + window.pageXOffset;
    var mouseY = evt.clientY - top + window.pageYOffset;
    return {
        x: mouseX,
        y: mouseY
    };
}
 
function drawColorSquare(canvas, color, imageObj){
    var colorSquareSize = 100;
    var padding = 10;
    var context = canvas.getContext("2d");
    context.beginPath();
    context.fillStyle = color;
    var squareX = (canvas.width - colorSquareSize + imageObj.width) / 2;
    var squareY = (canvas.height - colorSquareSize) / 2;
    context.fillRect(squareX, squareY, colorSquareSize, colorSquareSize);
    context.strokeRect(squareX, squareY, colorSquareSize, colorSquareSize);
}
 
function init(imageObj){
	bbo_color_field = document.getElementsByName("bbo_color_field");
    var padding = 10;
    var canvas = document.getElementById("myCanvas");
    var context = canvas.getContext("2d");
    var mouseDown = false;
 
    context.strokeStyle = "#444";
    context.lineWidth = 2;
 
    canvas.addEventListener("mousedown", function(){
        mouseDown = true;
    }, false);
 
    canvas.addEventListener("mouseup", function(){
        mouseDown = false;
    }, false);
 
    canvas.addEventListener("mousemove", function(evt){
        var mousePos = getMousePos(canvas, evt);
        var color = undefined;
 
        if (mouseDown &&
        mousePos !== null &&
        mousePos.x > padding &&
        mousePos.x < padding + imageObj.width &&
        mousePos.y > padding &&
        mousePos.y < padding + imageObj.height) {
            /*
             * color picker image is 256x256 and is offset by 10px
             * from top and bottom
             */
            var imageData = context.getImageData(padding, padding, imageObj.width, imageObj.width);
            var data = imageData.data;
            var x = mousePos.x - padding;
            var y = mousePos.y - padding;
            var red = data[((imageObj.width * y) + x) * 4];
            var green = data[((imageObj.width * y) + x) * 4 + 1];
            var blue = data[((imageObj.width * y) + x) * 4 + 2];
			var decColor = red + 256 * green + 65536 * blue;
			if(!color) {
				 color = "<?php echo $content[0];  ?>";
			}
            color = "rgb(" + red + "," + green + "," + blue + ")";
        }
 
        if (color) {
            drawColorSquare(canvas, color, imageObj);
			bbo_color_field[0].value = color;
			//bbo_color_field[0].value = "#"+decColor.toString(16);
        }
    }, false);
 
    context.drawImage(imageObj, padding, padding);
	var oldColor = "<?php echo $content[0];  ?>";
	if(oldColor){
	}else{
		oldColor = "white";
	}	
    drawColorSquare(canvas, oldColor, imageObj);
}


window.onload = function(){
    var imageObj = new Image();
    imageObj.onload = function(){
        init(this);
    };
    imageObj.src = "/wp-content/plugins/bbo-color-picker/color_picker.png";
};
</script>
        <canvas id="myCanvas" width="578" height="276"></canvas>
        <br />
        <?php echo bbo_color_hex( $post->ID); ?>
            <input type="hidden" id="bbo_color_field" name="bbo_color_field" value="<?php echo $content[0];  ?>" /> 
            <?php
		}
		
		

/* Do something with the data entered */
add_action( 'save_post', 'bbo_tinymce_save_postdata' );

/* When the post is saved, saves our custom data */
function bbo_tinymce_save_postdata( $post_id ) {

	// verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // Check permissions
  if ( ( isset ( $_POST['post_type'] ) ) && ( 'page' == $_POST['post_type'] )  ) {
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}		
  }
	else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

  // OK, we're authenticated: we need to find and save the data
		if ( isset ($_POST['bbo_color_field']) ) {
			update_post_meta( $post_id, 'bbo_color_field', $_POST['bbo_color_field'] );
		}
}

function bbo_color_hex($postid){
		$content =  get_post_meta($postid,'bbo_color_field', false );
		$string =   $content[0];
		$pattern = '/rgb\((\d+),\s*(\d+),\s*(\d+)\)/e';
		$replacement = '"#" . dechex(\\1) . dechex(\\2) . dechex(\\3)';
		return preg_replace($pattern, $replacement, $string);
}

function bbo_color_rgb($postid){
		$content =  get_post_meta($postid,'bbo_color_field', false );
		return $content;
}

?>
