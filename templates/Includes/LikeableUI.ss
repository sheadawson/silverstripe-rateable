<div id="$RatingHTMLID" class="$RatingCSSClass" data-disablealert="0" data-ratepath="$RatePath">
	<% loop $RatingOptions %>
		<button type="button" class="rateable-ui-button rateable-ui-button-$Score <% if $Up.UserHasRated %>has-voted<% end_if %>" value="$Score">
			<span class="rateable-ui-button-child rateable-ui-text">
				<span class="js-rateable-numberofratings">$Up.NumberOfRatings</span>
			</span>
			<img src="$ModulePath(sheadawson/silverstripe-rateable)client/images/star-off.png" class="rateable-ui-button-child rateable-ui-image rateable-ui-image-inactive" alt="$Score">
			<img src="$ModulePath(sheadawson/silverstripe-rateable)client/images/star-on.png" class="rateable-ui-button-child rateable-ui-image rateable-ui-image-active" alt="$Score">
		</button>
	<% end_loop %>
</div>