;(function () {
	'use strict';
	
	var ready = function(fn) {
	    if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
	        fn();
	    } else {
	        document.addEventListener('DOMContentLoaded', fn);
	    }
	};
	
	ready(function() {
		var rateable = document.querySelector('.rateable-ui');
		var ratingButtons = rateable.querySelectorAll('button');
		
		if (typeof(rateable) == 'undefined' || rateable == null) {
			return;
		}
		
		function alertMessageIfNotDisabled(message) {
			if (message && rateable.getAttribute('data-disablealert') == 0) {
				alert(message);
			}
		}
		
		function prevAll(element) {
		    var result = [];
		    while (element = element.previousElementSibling)
		        result.push(element);
		    return result;
		}

		function nextAll(element) {
		    var result = [];
		    while (element = element.nextElementSibling)
		        result.push(element);
		    return result;
		}
		
		function siblings(elem) {
			var siblings = [];
			var sibling = elem.parentNode.firstChild;
			while (sibling) {
				if (sibling.nodeType === 1 && sibling !== elem) {
					siblings.push(sibling);
				}
				sibling = sibling.nextSibling
			}
			return siblings;
		};


		Array.prototype.forEach.call(ratingButtons, function (button, index) {
			
			button.addEventListener("mouseenter", function( event ) {
				var siblings = prevAll(event.target);
				siblings.forEach(function(item){
					item.classList.add('is-hover');
				});
				event.target.classList.add('is-hover');
			}, false);
			button.addEventListener("mouseleave", function( event ) {
				var siblings = prevAll(event.target);
				siblings.forEach(function(item){
					item.classList.remove('is-hover');
				});
				event.target.classList.remove('is-hover');
			}, false);
			
			button.addEventListener("focus", function( event ) {
				var siblings = prevAll(event.target);
				siblings.forEach(function(item){
					item.classList.add('is-focused');
				});
				event.target.classList.add('is-focused');
			}, false);
			button.addEventListener("blur", function( event ) {
				var siblings = prevAll(event.target);
				siblings.forEach(function(item){
					item.classList.remove('is-focused');
				});
				event.target.classList.remove('is-focused');
			}, false);
			
			button.addEventListener("click", function( event ) {
				event.preventDefault();
				
				if (rateable.classList.contains('is-waiting-on-ajax')) {
					return;	
				}
				
				rateable.classList.add('is-waiting-on-ajax');
				
				var self = this;
				var score = self.value;
				
				var request = new XMLHttpRequest();
				request.open('GET', rateable.getAttribute('data-ratepath') + '?score=' + score, true);

				request.onload = function() {
					if (this.status >= 200 && this.status < 400) {
						var data = JSON.parse(this.response);
						
						if(data.status == 'error'){
							alertMessageIfNotDisabled(data.message);
							return;
						}

						if (typeof data.isremovingrating !== 'undefined' && data.isremovingrating == 1) {
							// Force consistency when passing to 'ratingChange'
							data.isremovingrating = 1;

							var siblings = siblings(self);
							siblings.forEach(function(item){
								item.classList.remove('has-voted');
							});
							self.classList.remove('has-voted');
							rateable.classList.remove('has-voted');
						} else {
							// Force consistency when passing to 'ratingChange'
							data.isremovingrating = 0;

							var nextSiblings = nextAll(self);
							nextSiblings.forEach(function(item){
								item.classList.remove('has-voted');
							});
							var prevSiblings = prevAll(self);
							prevSiblings.forEach(function(item){
								item.classList.add('has-voted');
							});
							self.classList.add('has-voted');
							rateable.classList.add('has-voted');
						}

						if (ratingButtons.length > 1)
						{
							Array.prototype.forEach.call(ratingButtons, function (button, index) {
								var val = button.value;
								if (val <= data.averagescore) {
									button.classList.add('is-average-score');
								} else {
									button.classList.remove('is-average-score');
								}
							});
						}
						else
						{
							Array.prototype.forEach.call(ratingButtons, function (button, index) {
								button.classList.remove('is-average-score');
							});
						}
						
						var counterElement = document.querySelector('.js-rateable-numberofratings');
						if (typeof(counterElement) != 'undefined' && counterElement != null) {
							counterElement.innerHTML = data.numberofratings;
						}

						alertMessageIfNotDisabled(data.message);

						rateable.dispatchEvent(new CustomEvent("ratingChange", self.response));
					
					} else {
						alertMessageIfNotDisabled(this.statusText);
						return;
					}
				  
					rateable.classList.remove('is-waiting-on-ajax');
					rateable.dispatchEvent(new CustomEvent("ratingFinished", self.response));
				};

				request.onerror = function() {
					rateable.innerHTML = self.statusText;
					rateable.dispatchEvent(new CustomEvent("ratingError", self.response));

					rateable.classList.remove('is-waiting-on-ajax');
					rateable.dispatchEvent(new CustomEvent("ratingFinished", self.response));
				};

				request.send();

			}, false);
		});
		
		rateable.dispatchEvent(new CustomEvent("ratingInit"));
		
	});
	
}());
