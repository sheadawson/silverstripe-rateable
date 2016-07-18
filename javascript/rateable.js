(function($){
	var $rateable = $('.rateable-ui');
	var $ratingButtons = $rateable.find('button');

	$ratingButtons.hover(
		function() {
			$(this).prevAll().addClass('is-hover');
			$(this).addClass('is-hover');
		}, function() {
			$(this).prevAll().removeClass('is-hover');
			$(this).removeClass('is-hover');
	  	}
	);
	$ratingButtons.focus(function() {
		$(this).prevAll().addClass('is-focused');
		$(this).addClass('is-focused');
	});
	$ratingButtons.blur(function() {
		$(this).siblings().removeClass('is-focused');
		$(this).removeClass('is-focused');
	});

	function alertMessageIfNotDisabled(message) {
		if (message && $rateable.data('disablealert') == 0) {
			alert(message);
		}
	}

	$ratingButtons.click(function(e) {
		e.preventDefault();
		if($rateable.hasClass('is-waiting-on-ajax')){
			return;	
		}

		var $self = $(this);
		var score = $(this).val();

		$rateable.addClass('is-waiting-on-ajax');
		$.getJSON(
			$rateable.data('ratepath') + '?score=' + score
		)
		.fail(function(jqxhr, textStatus, error) {
			$rateable.html(textStatus);
			$rateable.trigger('ratingError', data);
		})
		.done(function(data) {
			if(data.status == 'error'){
				alertMessageIfNotDisabled(data.message);
				return;
			}

			if (typeof data.isremovingrating !== 'undefined' && data.isremovingrating == 1) {
				// Force consistency when passing to 'ratingChange'
				data.isremovingrating = 1;

				$self.siblings().removeClass('has-voted');
				$self.removeClass('has-voted');
				$rateable.removeClass('has-voted');
			} else {
				// Force consistency when passing to 'ratingChange'
				data.isremovingrating = 0;

				$self.nextAll().removeClass('has-voted');
				$self.prevAll().addClass('has-voted');
				$self.addClass('has-voted');
				$rateable.addClass('has-voted');
			}

			if ($ratingButtons.length > 1)
			{
				$ratingButtons.each(function(i) {
					var val = $(this).val();
					if (val <= data.averagescore) {
						$(this).addClass('is-average-score');
					} else {
						$(this).removeClass('is-average-score');
					}
				});
			}
			else
			{
				$ratingButtons.removeClass('is-average-score');
			}

			$('.js-rateable-numberofratings').html(data.numberofratings);
			alertMessageIfNotDisabled(data.message);

			$rateable.trigger('ratingChange', data);
		})
		.always(function(data) {
			$rateable.removeClass('is-waiting-on-ajax');
			$rateable.trigger('ratingFinished', data);
		});
	});

	$rateable.trigger('ratingInit');
})(jQuery);