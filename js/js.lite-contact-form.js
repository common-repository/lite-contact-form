document.addEventListener('DOMContentLoaded', function() {
	// Submit form
	document.querySelector('.lcf').addEventListener('submit', function() {
		// Remove all messages
		document.querySelector('#lcf-response').removeAttribute('class');
		document.querySelector('#lcf-response').innerText = '';
		// Show spinner
		document.querySelector('.lcf-spinner').style.visibility = 'visible';
		// Validate fields
		document.querySelectorAll('.lcf-validate').forEach(function(field) {
			item = field.querySelector('input, textarea');
			// Field valid
			if(item.value) {
				item.removeAttribute('class');
				field.querySelector('.lcf-tip').innerText = '';
			}
			// Field invalid
			else {
				item.removeAttribute('class');
				item.classList.add('lcf-invalid');
				field.querySelector('.lcf-tip').innerText = lcf.empty_field;
			}
		});
		// Validation errors
		if(document.querySelectorAll('.lcf-invalid').length) {
			// Add error response message
			document.querySelector('#lcf-response').classList.add('lcf-blocked');
			document.querySelector('#lcf-response').innerText = lcf.invalid;
			// Hide spinner
			document.querySelector('.lcf-spinner').style.visibility = 'hidden';
		}
		// Submit form
		else {
			var request = new XMLHttpRequest();
			request.open('POST', lcf.submit, true);
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			if(lcf.nonce) request.setRequestHeader('X-WP-Nonce', lcf.nonce);
			request.responseType = 'json';
			request.onload = function() {
				// Dispatch an submitted event
				document.querySelector('.lcf').dispatchEvent(
					new CustomEvent('lcf_submitted', {
						'bubbles': true,
						'cancelable': true
					})
				);
				// Request success
				if(this.status == 200) {
					// Success
					if(this.response.status == 'success') {
						document.querySelector('.lcf').reset();
					}
					// Error
					else if(this.response.fields) {
						this.response.fields.forEach(function(item) {
							field = document.getElementsByName(item.field)[0];
							field.removeAttribute('class');
							field.classList.add('lcf-invalid');
							field.parentNode.querySelector('.lcf-tip').innerText = item.message;
						});
					}
					document.querySelector('#lcf-response').classList.add('lcf-'+this.response.status);
					document.querySelector('#lcf-response').innerText = this.response.message;
				}
				// Request error
				else {
					// Add error response message
					document.querySelector('#lcf-response').classList.add('lcf-error');
					document.querySelector('#lcf-response').innerText = lcf.error;
				}
				// Hide spinner
				document.querySelector('.lcf-spinner').style.visibility = 'hidden';
			}
			request.send(new URLSearchParams(new FormData(document.querySelector('.lcf'))));
		}
	});
});