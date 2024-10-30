<?php
function ct_referrer_local_phone_shortcode( $atts, $content = 0 ) {
	return ct_get_current_number(true, true);
}
add_shortcode( 'tracker-local-phone', 'ct_referrer_local_phone_shortcode' );

function ct_referrer_toll_free_phone_shortcode( $atts, $content = 0 ) {
	return ct_get_current_number(true, false);
}
add_shortcode( 'tracker-toll-free-phone', 'ct_referrer_toll_free_phone_shortcode' );

function ct_get_current_number($return = false, $local = false) {
	global $call_tracker;
	$number = $call_tracker->ct_get_current_number($local);
	if($return) {
		return $number;
	} else {
		echo $number;
	}
}

function ct_get_default_local_number() {
	global $call_tracker;
	return $call_tracker->ct_get_local_number();	
}

function ct_get_default_toll_free_number() {
	global $call_tracker;
	return $call_tracker->ct_get_toll_free_number();	
}
function ct_show_referrer_list($popular_referrer_numbers = false, $custom_referrer_numbers = false) {
	global $call_tracker;

	$custom_numbers = $call_tracker->ct_get_custom_referrer_phones();
	$popular_numbers = $call_tracker->ct_get_popular_referrer_phones();

	if($custom_referrer_numbers && !$popular_referrer_numbers) {
		return $custom_numbers;
	} elseif($popular_referrer_numbers && !$custom_referrer_numbers) {
		return $popular_numbers;
	} else {
		return $popular_numbers + $custom_numbers;
	}

}