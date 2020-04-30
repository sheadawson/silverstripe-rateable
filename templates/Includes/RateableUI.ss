<div id="$RatingHTMLID" class="$RatingCSSClass" data-disablealert="0" data-canchangerating="$canChangeRating" data-ratepath="$RatePath">
	<% loop $RatingOptions %>
		<button type="button" class="rateable-ui-button rateable-ui-button-$Score <% if $IsAverageScore %>is-average-score<% end_if %>" value="$Score">
			<img src="$resourceURL('sheadawson/silverstripe-rateable:client/images/star-on.png')" class="rateable-ui-image rateable-ui-image-active" alt="$Score">
			<img src="$resourceURL('sheadawson/silverstripe-rateable:client/images/star-off.png')" class="rateable-ui-image rateable-ui-image-inactive" alt="$Score">
		</button>
	<% end_loop %>
</div>