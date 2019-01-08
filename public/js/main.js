// The following code is based off a toggle menu by @Bradcomp
// source: https://gist.github.com/Bradcomp/a9ef2ef322a8e8017443b626208999c1
(function() {
    var burger = document.querySelector('.burger');
    var menu = document.querySelector('#'+burger.dataset.target);
    burger.addEventListener('click', function() {
        burger.classList.toggle('is-active');
        menu.classList.toggle('is-active');
    });
})();

$('#frmContact').on('submit', function( e ) {

	// reset form state
	$('#frmContact input').removeClass('is-danger');
	$('#frmContact textarea').removeClass('is-danger');
	$('#frmContact .error').hide();
	$('#frmContact .message').hide();

	// disable submit button and show wait status indicators
	$('#frmContact button').prop('disabled', true).addClass('is-loading');
	$('body').css('cursor', 'wait');

	var frm = $(e.target);

	$.ajax({
		type: 'POST',
		url: frm.attr('action'),
		data: frm.serialize(),
		success: function( response ){
			$('#frmContact .message.is-success').show();
		},
		error: function(xhr, type){

			// validation errors
			if( xhr.status == 400 ) {
				errors = JSON.parse(xhr.responseText);
				console.log(errors);
				for( field in errors ) {
					if( field == 'captcha' ) {
						$('#frmContact .captcha .error').text(errors[field]).show().css("display", "inline-block");
						continue;
					}
					$('#contact-' + field).addClass('is-danger');
					$('#contact-' + field + ' + .error').text(errors[field]).show().css("display", "inline-block");
				}
				return;
			}

			// attempt to calculate a first name
			var name = $('#contact-name').val();
			var firstName = name;
			if( name.indexOf(' ') >= 0 ) {
				firstName = name.split(' ').slice(0, -1).join(' ');
			}

			// display nice error message
			$('#frmContact .message.is-danger .message-body').text("Sorry " + firstName + ", it seems that an error occurred. Please try again later...");
			$('#frmContact .message.is-danger').show();

		},
		complete: function() {
			// clear wait status indicators
			$('#frmContact button').prop('disabled', false).removeClass('is-loading');
            $('body').css({'cursor': 'auto'})
		}
	});

	return false;

});