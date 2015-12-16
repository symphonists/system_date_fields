(function ($, undefined) {
	'use strict';

	var init = function() {
		$('.js-systemdate-timeago').symphonyTimeAgo({
			max: 48 * 60
		});
	};

	$(init);

})(jQuery);