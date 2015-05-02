<?php
/*
Plugin Name: The Colour Clock
Plugin URI: http://smartfan.pl/
Description: What colour is it when you browse your website? Bring more colour into your website with this ever-changing background. It changes background color of your website with the passage of time (time as a hexadecimal value).
Author: Piotr Pesta
Version: 1.0.1
Author URI: http://smartfan.pl/
License: GPL12
*/

register_uninstall_hook(__FILE__, 'hex_background_clock_uninstall'); //akcja podczas deaktywacji pluginu

// podczas odinstalowania - usuwanie opcji
function hex_background_clock_uninstall() {
	delete_option('widget_hex_background_clock');
}

class hex_background_clock extends WP_Widget {

// konstruktor widgetu
function hex_background_clock() {

	$this->WP_Widget(false, $name = __('The Colour Clock', 'wp_widget_plugin') );

}

function update($new_instance, $old_instance) {
$instance = $old_instance;
// Pola
$instance['title'] = strip_tags($new_instance['title']);
$instance['hidewidget'] = strip_tags($new_instance['hidewidget']);
return $instance;
}

// tworzenie widgetu, back end (form)

function form($instance) {

// nadawanie i łączenie defaultowych wartości
	$defaults = array('hidewidget' => '', 'title' => 'The Colour Clock');
	$instance = wp_parse_args( (array) $instance, $defaults );
?>

<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
	<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
</p>

<p>
<input type="checkbox" id="<?php echo $this->get_field_id('hidewidget'); ?>" name="<?php echo $this->get_field_name('hidewidget'); ?>" value="1" <?php checked($instance['hidewidget'], 1); ?>/>
<label for="<?php echo $this->get_field_id('hidewidget'); ?>">Hide widget (do not show clock, only change background color)?</label>
</p>

<?php

}

// wyswietlanie widgetu, front end (widget)
function widget($args, $instance) {
extract( $args );

// these are the widget options
$title = apply_filters('widget_title', $instance['title']);
$hidewidget = $instance['hidewidget'];
echo $before_widget;

// Check if title is set
if ($title) {
echo $before_title . $title . $after_title;
}

?>

<script type="text/javascript">

function display_c(){
var refresh=250; // Refresh rate in milli seconds
mytime=setTimeout('display_ct()',refresh);
}

function display_ct() {
	var strcount;
	var x = new Date();
	var ms = x.getMilliseconds();
	var s = x.getSeconds();
	var m = x.getMinutes();
	var h = x.getHours();
	var rounded = s + (ms / 999);
	
	red = (Math.round(255 * ((h) / 23)));
	green = (Math.round(255 * ((m) / 59)));
	blue = (Math.round(255 * ((rounded) / 60)));

	redhex = hexifyWithZeroLead(red);
	greenhex = hexifyWithZeroLead(green);
	bluehex = hexifyWithZeroLead(blue);
	
	timeh = zeroFill(h);
	timem = zeroFill(m);
	times = zeroFill(s);
	
	function hexifyWithZeroLead(hexval){
		var rtn = hexval.toString(16);
		return (rtn.length == 1 ? "0" : "") + rtn;
	}
	
	function zeroFill(i) {
    	return (i < 10 ? '0' : '') + i
	}
	
	var hex = "#" + redhex + greenhex + bluehex;
	
	jQuery("body").css("background-color", hex);

<?php

	if ($hidewidget == ''){
		echo "document.getElementById('pp-colour-clock').innerHTML = timeh+' : '+timem+' : '+times+'<br>'+hex;";
	}else {
		echo "document.getElementById('pp-colour-clock');";
}

?>

tt=display_c();
}

</script>

<body onload=display_ct();>
<span id='pp-colour-clock'></span>

<?php

echo $after_widget;
}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("hex_background_clock");'));

add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('hex_background_clock', plugins_url('style-hex-background-clock.css', __FILE__)); //css file
});

?>