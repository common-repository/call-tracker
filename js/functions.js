jQuery(function($) {
	var ct_error_popular_link = false;
	var ct_error_custom_link = false;
	$('.ct-add-entry').click(function(e) {
		e.preventDefault();
		var $ct_entries_section = $(this).closest('.ct-entries-section');
		var $clone_text = $ct_entries_section.find('.ct-entry-section:first').clone();

		$clone_text.find('input').attr('value', '');
		$clone_text.find('select').removeAttr('selected');

		$ct_entries_section.find('.ct-entry-section:last').after($clone_text);
		ct_hide_show_add_entry_button();
	});



	$('.ct-section').on('click', '.ct-remove-entry', function(e) {
		e.preventDefault();
		$(this).parent().remove();
		ct_hide_show_add_entry_button();
	});

	$('.ct-section').submit(function() {
		ct_check_for_duplicate_popular_referrer_links();
		if(ct_error_popular_link) {
			alert('Please don\'t duplicate the Popular Referrer Link')
			return false;
		} else {
			if(ct_error_custom_link) {
				alert('Please don\'t duplicate the Custom Referrer Domain')
				return false;
			}
		}
	});

	function ct_hide_show_add_entry_button() {
		$('.ct-entries-section').each(function() {
			var max_items = $(this).data('max');
			var $entry_button = $(this).find('.ct-add-entry');
			var $entry_plus_button = $(this).find('.ct-add-new-plus');
			if(max_items > 0) {
				if($(this).find('.ct-entry-section:last').index() + 1 >= max_items) {
					$entry_button.addClass('ct-hidden');
					$entry_plus_button.addClass('ct-hidden');
				} else {
					$entry_button.removeClass('ct-hidden')
					$entry_plus_button.removeClass('ct-hidden');
				}
			}
		});
	}


	function ct_check_for_duplicate_popular_referrer_links() {
		ct_error_popular_link = false;
		ct_error_custom_link = false;

		var referrer_links = new Array();
		var referrer_domains = new Array();
		
		$('.ct-select-menu').each(function() {
			var $selected = $(this).find(':selected');
			if($selected.length  && $selected.val() != '') {
				if(referrer_links[$selected.val()] != undefined) {
					ct_error_popular_link = true;
					return;
				} 
				referrer_links[$selected.val()] = $selected.val();
			}
		});

		$('.ct-check-for-duplicate').each(function() {

			if($(this).val() != '') {

				if(referrer_domains[$(this).val()] != undefined) {
					ct_error_custom_link = true;
					return;
				}

				referrer_domains[$(this).val()] = $(this).val();
			}
		});
	}

});