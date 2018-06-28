$('document').ready(function() {
	$('#mscaptcha-refresh').click(function (e) {
		e.preventDefault();
		$('.mscaptcha-loader').show();
		$('.img-mscaptcha').load(function () {
			$('.mscaptcha-loader').hide();
		}).attr('src', ET.webPath + '/mscaptcha?' + (Math.random() * 5));
	});
});
