<div class="offer-wrapper">
	<div class="offer-filter">
		<select class="offer-sort" name="sort">
			<option value="0"></option>
			<option value="1">Name A->Z</option>
			<option value="2">Name Z->A</option>
			<option value="3">Cash Back 0->9</option>
			<option value="4">Cash Back 9->0</option>
		</select>
	</div>
	<div class="offer-container">
	<?php foreach ( $offers as $row ) { ?>
		<div class="offer-item" data-index="<?php echo $row['offer_id']; ?>">
			<div class="offer-image">
				<image src="<?php echo $row['image_url']; ?>" />
			</div>
			<div class="offer-name">
				<?php echo $row['name']; ?>
			</div>
			<div class="offer-cash-back">
				$<?php echo $row['cash_back']; ?>
			</div>
		</div>
	<?php } ?>
	</div>
</div>