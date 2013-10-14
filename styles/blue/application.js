	$(document).ready( function() {
		$(".pwd").passStrength({
			url: "../op/op.Ajax.php",
			minscore: <?php echo (int) $passwordstrength; ?>,
			onChange: function(data, target) {
				pws = <?php echo (int) $passwordstrength; ?>;
				kids = $('#'+target).children();
				$(kids[1]).html(Math.round(data.strength));
				$(kids[0]).width(data.strength);
				if(data.strength > pws) {
					$(kids[0]).css('background-color', 'green');
				} else {
					$(kids[0]).css('background-color', 'red');
				}
			}
		});
	});

