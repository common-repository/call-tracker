<?php 
/**
* Call Tracker Decorator enter the html code
*/
class CallTrackerDecorator {

	public function __construct() {
	}

	public static function show_statistics_section() {
		global $call_tracker;
		self::show_top_section('Call Tracker Statistics');
		$items = $call_tracker->ct_get_domain_statistcs_counter();
		?>
		<div class="ct-section">
			<?php 
			if(!empty($items)) :
				foreach($items as $key => $item) :
					?>
					<div class="domain-entry">
						<span class="domain-name"><?php echo $key; ?></span>
						<span class="domain-count"><?php echo $item; ?></span>
						<div class="ct-cl">&nbsp;</div>
					</div>

					<?php 
				endforeach;
				?>
				<div class="cl">&nbsp;</div>
				<a class="button" href="<?php echo add_query_arg('remove-statistics', true); ?>">Clean Statistics</a>
				<?php
			else :
				?>
				<div class="ct-cl">&nbsp;</div>
				<h3>Domains not found.</h3>
				<?php 
			endif;
			?>
		</div>
		<?php
		self::show_bottom_section();
	}

	public static function show_banners() {
		global $call_tracker;
		?>
		<div class="ct-banners">
			<a target="_blank" href="http://www.hitcrowd.com"><img src="<?php echo $call_tracker->ct_get_plugins_url() ?>/css/images/hit-crowd-banner.png" alt="" /></a>
			<a href="<?php echo home_url('/wp-admin/admin.php?page=video') ?>"><img src="<?php echo $call_tracker->ct_get_plugins_url() ?>/css/images/video-button.png" alt="" /></a>

		</div>
		<?php
	}

	public static function show_number_section() {
		self::show_top_section();
		global $call_tracker;
		$referrer_list = $call_tracker->ct_get_referrer_list();		
		?>
		<div class="ct-section">
			<?php 
			if(!empty($_GET['success'])) :
				?>
				<div class="ct-success-message">
					<p>Your updates have been saved</p>
				</div>	
				<?php 
			endif;
			?>

			<form action="" method="POST">
				<div class="ct-inner-border-section ct-default-numbers">
					<h3>Default Numbers</h3>
					<label>Toll Free</label>
					<input type="text" class="ct-field ct-field-top" name="ct-toll-free-number" value="<?php echo $call_tracker->ct_get_toll_free_number(); ?>" /><br />

					<label>Local</label>
					<input type="text" class="ct-field ct-field-top" name="ct-local-number" value="<?php echo $call_tracker->ct_get_local_number(); ?>" />

					<p><?php echo apply_filters('ct_refferer_links_text', 'The above number will be displayed across the site when a referring number below does not have a match or a user visits the website <a href="'. site_url()  .'">' . site_url() .'</a> directly.') ?></p>
				</div>
				<div class="ct-inner-border-section">
					<h3>Popular Referrer Links</h3>
					<div class="ct-select-section">
						<div class="ct-left">
							<label>Referrer Link:</label>
						</div>
						<div class="ct-right">
							<label>Phone Number:</label>
						</div>
						<div class="ct-cl">&nbsp;</div>

						<div class="ct-entries-section" data-max="<?php echo count($referrer_list); ?>">
							<?php 
							$items = $call_tracker->ct_get_popular_referrer_phones();

							if(!empty($items)) {
								foreach($items as $key => $item) {
									self::show_popular_referrer_link_entry($key, $item);
								}
							} else {
								self::show_popular_referrer_link_entry();
							}

							?>
							<span class="ct-add-new-plus<?php if(count($items) == count($referrer_list)) echo ' ct-hidden'; ?>">+</span><a class="ct-add-entry<?php if(count($items) ==  count($referrer_list)) echo ' ct-hidden'; ?>" href="#">Add new Entry</a>
						</div>
					</div>
				</div>
				<div class="ct-inner-section">
					<h3>Custom Referrer Domains</h3>
					<div class="ct-select-section">

						<div class="ct-left">
							<label>Referrer Domain:</label>
						</div>
						<div class="ct-right">
							<label>Phone Number:</label>
						</div>
						<div class="ct-cl">&nbsp;</div>

						<div class="ct-entries-section">
							<?php 
							$items = $call_tracker->ct_get_custom_referrer_phones();
							if(!empty($items)) {
								foreach($items as $key => $item) {
									self::show_custom_referrer_link_entry($key, $item);
								}
							} else {
								self::show_custom_referrer_link_entry();
							}
							?>
							<span class="ct-add-new-plus">+</span><a class="ct-add-entry" href="#">Add new Entry</a>
						</div>
					</div>
				</div>

				<input type="submit" name="submit" class="button button-primary" value="Save Changes">
				<input name="ct-add-numbers" value="yes" type="hidden" />
			</form>
		</div>
		<div class="ct-cl">&nbsp;</div>
		<?php
		self::show_bottom_section();
	}

	public static function show_top_section($title = 'Call Tracker General Settings') {
		?>
		<div class="ct-container">
			<div class="wrap">
				<div class="ct-icon">&nbsp;</div>
				<h2><?php echo $title; ?></h2>
			</div>
			<div class="ct-cl">&nbsp;</div>
		<?php
		self::show_banners();
	}

	public static function show_bottom_section() {
		?>
		</div>
		<?php
	}	


	public static function show_help_section() {
		self::show_top_section('Call Tracker Configure Section');
		?>
		<div class="ct-section">
			<ol>
				<li>
					<p><strong><?php _e('How to list phone number using a shortcode', 'ct'); ?></strong></p>
					<p><?php _e('You can list Toll Free phone number by using the <kbd>[tracker-toll-free-phone]</kbd> shortcode.', 'ct'); ?></p>
					<p><?php _e('You can list Local phone number by using the <kbd>[tracker-local-phone]</kbd> shortcode.', 'ct'); ?></p>
				</li>
				<li>
					<p><strong><?php _e('How to list phone number via function (for deeper theme integration)', 'ct'); ?></strong></p>
					<p><?php _e('You can get the phone numbers by usgin the <kbd>ct_referrer_local_phone_shortcode([$popular_referrer_numbers = true], [$custom_referrer_numbers = true])</kbd> or <kbd>ct_referrer_toll_free_phone_shortcode([$popular_referrer_numbers = true], [$custom_referrer_numbers = true])</kbd> functions', 'ct'); ?></p>
					<p><?php _e('The functions return an associative array with the numbers. The keys are the referrers&apos; name and the values are the phone numbers.', 'ct'); ?></p>
					<p><?php _e('Example usage:', 'ct'); ?></p>
					<div class="pre">
						<?php _e('&lt;?php $numbers = ct_get_current_number(true, false); ?&gt;', 'ct'); ?>
					</div>
				</li>
				<li>
					<p><strong><?php _e('Custom referrer links best practices', 'ct'); ?></strong></p>
					<p><?php _e('When entering custom referrers it is advised to use just the domain. Appending any path will only match the page this corresponds to, as a generic domain will match all pages.', 'ct'); ?></p>
				</li>
				<li>
					<p><?php _e('<strong>Google AdWords Auto Tagging needs to be enabled in order to track ad referrals properly. Otherwise those will be handled as organic Google referrals.</strong>', 'ct'); ?></p>
					<p><?php _e('By default auto-tagging is enabled for Google AdWords. To double check that you need to go to My Account &raquo; Preferences and check the value for the &quot;Tracking&quot; option.', 'ct'); ?></p>
				</li>
			</ol>
			<div class="ct-left-indent">
				<hr>
				<p><?php _e('Have a question email') ?> <a href="mailto:support@hitcrowd.com">support@hitcrowd.com</a></p>
			</div>
		</div>
		<?php
		self::show_bottom_section();
	}

	private static function enter_add_referrer_field($value = '') {
		?>
		<div class="ct-entry-section">
			<input type="text" class="ct-field ct-large" name="referrer_list[]" value="<?php echo $value ?>" />
			<a href="#" class="ct-remove-entry">x</a>
		</div>
		<?php
	}

	private static function show_popular_referrer_link_entry($option_selected = false, $input_value = false) {
		global $call_tracker;
		$items = $call_tracker->ct_get_referrer_list();		

		?>
		<div class="ct-entry-section">
			<select class="ct-select-menu" name="popular_referrer_links[]">
				<option val="">Seleact A referrer</option>
				<?php
				if(!empty($items)) {
					foreach($items as $item) :
						?>
						<option <?php if($option_selected && $option_selected == $item) echo 'selected="selected"' ?> val="<?php echo $item; ?>"><?php echo $item; ?></option>
						<?php
					endforeach;
				}
				?>
			</select>
			<input type="text" class="ct-field ct-small" name="popular_referrer_phones[]" value="<?php echo $input_value ?>" />
			<a href="#" class="ct-remove-entry">x</a>
		</div>

		<?php
	}

	private static function show_custom_referrer_link_entry($first_field_value = false, $second_field_value = false) {
		?>
		<div class="ct-entry-section">
			<input type="text" class="ct-field ct-check-for-duplicate" name="custom_referrer_links[]" value="<?php echo $first_field_value ?>" />
			<input type="text" class="ct-field ct-small" name="custom_referrer_phones[]" value="<?php echo $second_field_value ?>" />
			<a href="#" class="ct-remove-entry">x</a>
		</div>		
		<?php
	}

	static function show_video_section() {
		self::show_top_section('Call Tracker Marketing Video');
		?>
		<div class="ct-video">
			<h3>Learn why integrating a trackable number is great for your digital marketing.</h3>
			<iframe src="http://player.vimeo.com/video/70213096" width="600" height="340" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
		</div>
		<?php		
	}
}
?>