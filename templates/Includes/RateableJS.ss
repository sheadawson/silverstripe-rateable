(function($){
	$(function(){
		var RateableUI = $('#{$RatingHTMLID}');
		RateableUI.raty({
			//cancel 		: <% if UserHasRated %>true<% else %>false<% end_if %>,
			readOnly 	: <% if UserHasRated %>true<% else %>false<% end_if %>,
			score 		: '$AverageScore',
			path 		: 'rateable/images/'
		});

		<% if UserHasRated %>
			RateableUI.addClass('disabled');
		<% end_if %>

		RateableUI.find('img').not('.raty-cancel').click(function(){
			if(RateableUI.hasClass('disabled')){
				alert('You have already rated this item');
				return;	
			} 
			
			var score = $(this).attr('alt');
			$.getJSON('$RatePath/?score=' + score, function(data) {
				if(data.status == 'error'){
					alert(data.message);
					return;
				}

				RateableUI.raty('set', {
					'readOnly' : true,
					'score' : data.averagescore
				});
				RateableUI.addClass('disabled');
				alert(data.message);
			});
		});

	});
})(jQuery);