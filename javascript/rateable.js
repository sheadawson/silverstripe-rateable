(function($){
	$(function(){

		var rateables = $('.rateable-ui');

		rateables.each(function(){
			var self = $(this);
			self.raty({
				readOnly 	: self.hasClass('disabled'),
				score 		: self.data('averagescore'),
				path 		: 'rateable/images/'
			});

			self.find('img').not('.raty-cancel').click(function(){
				var img = $(this);
				var score = img.attr('alt');

				if(self.hasClass('disabled')){
					alert("You have already rated this item");
					return;	
				} 

				$.getJSON(self.data('ratepath') + '?score=' + score, function(data) {
					if(data.status == 'error'){
						alert(data.message);
						return;
					}

					self.raty('set', {
						'readOnly' : true,
						'score' : data.averagescore
					});

					self.addClass('disabled');
					alert(data.message);
				});
			});
		});

	});
})(jQuery);