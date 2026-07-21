/**
 * Portfolio plugin frontend JavaScript.
 *
 * @package Prtf
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		$('.prtf-load-more-btn').on('click', function(e) {
			e.preventDefault();

			var $btn = $(this);
			var $grid = $btn.closest('.prtf-portfolio-grid');
			var $items = $grid.find('.prtf-grid-items');

			var currentPage = parseInt($grid.attr('data-page'), 10);
			var maxPages = parseInt($btn.attr('data-max-pages'), 10);
			var type = $grid.attr('data-type');
			var count = $grid.attr('data-count');

			if (currentPage >= maxPages) {
				return;
			}

			$btn.prop('disabled', true).addClass('loading');

			$.ajax({
				url: prtfAjax.ajax_url,
				type: 'POST',
				data: {
					action: 'prtf_load_more',
					nonce: prtfAjax.nonce,
					page: currentPage + 1,
					count: count,
					type: type
				},
				success: function(response) {
					if (response.success && response.data.html) {
						$items.append(response.data.html);
						$grid.attr('data-page', currentPage + 1);

						if (currentPage + 1 >= response.data.max_pages) {
							$btn.remove();
						} else {
							$btn.prop('disabled', false).removeClass('loading');
						}
					} else {
						$btn.remove();
					}
				},
				error: function() {
					$btn.prop('disabled', false).removeClass('loading');
					alert('An error occurred. Please try again.');
				}
			});
		});
	});

})(jQuery);
