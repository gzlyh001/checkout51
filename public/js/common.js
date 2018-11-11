$(document).ready(function(){
	$('.offer-sort').change(function(e){
		e.preventDefault();
		
		var sort = $(this).val();
		var url = '/api/getOffers.html?sort=' + sort;
		
		$.ajax({
			url: url,
			dataType: 'json',
			success: function(data) { freshOffers(data); }
		});
	});
});

function freshOffers(data){
	if ( data.offers ) {
		var html = '';
		$.each(data.offers, function(){
			html += '<div class="offer-item" data-index="' + this.offer_id + '">';
			html += '<div class="offer-image"><image src="' + this.image_url + '" /></div>';
			html += '<div class="offer-name">' + this.name + '</div>';
			html += '<div class="offer-cash-back">$' + this.cash_back + '</div>';
			html += '</div>';
		});
		$('.offer-container').html(html);
	}
}