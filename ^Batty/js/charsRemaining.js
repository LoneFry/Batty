/**
 * charsRemaining
 *
 * Add an overlay which displays a count of characters remaining
 *
 * To specify an input for inclusion add attribute maxlength=""
 *
 * Overlays will be span.js-charsRemaining, suggested styles:
 * span.js-charsRemaining {
 *  font: bold 14pt Georgia, serif;
 *  color: #ccc;
 *  margin: -2px 4px;
 * }
 */
(function () {
	// Prepare event handlers
	var charsRemaining_show = function () {
		// Trim the value for browsers that don't enforce maxLength
		this.value = this.value.substr(0, this.maxLength);

		// Update 'Characters Remaining' overlay
		var o = this.nextSibling;
		o.style.display = 'block';
		o.innerHTML = (this.maxLength - this.value.length);
		o.title = o.innerHTML + ' Characters Remaining';
	};

	var charsRemaining_hide = function () {
		// Trim the value for browsers that don't enforce maxLength
		this.value = this.value.substr(0, this.maxLength);

		// Hide 'Characters Remaining' overlay
		var o = this.nextSibling;
		o.style.display = 'none';
	};

	// Find all specified inputs
	var aInputs = document.querySelectorAll("input[maxlength],textarea[maxlength]");
	var oSpan, oInput, oSpan2;
	for (var i = aInputs.length - 1; i >= 0; i--) {
		oInput = aInputs[i];

		//Wrap the input in a span and overlay another span to house count
		oSpan = document.createElement('span');
		oSpan.style.position = 'relative';
		oSpan.style.display = 'inline-block';
		oInput.parentNode.insertBefore(oSpan, oInput);
		oInput.parentNode.removeChild(oInput);
		oSpan.appendChild(oInput);
		oSpan2 = document.createElement('span');
		oSpan2.className = 'js-charsRemaining';
		oSpan2.style.display = 'none';
		oSpan2.style.position = 'absolute';
		//oSpan2.style.textAlign = 'right';
		oSpan2.style.top = 0;
		oSpan2.style.right = 0;
		oSpan.appendChild(oSpan2);

		oInput.addEventListener('focus', charsRemaining_show);
		oInput.addEventListener('keyup', charsRemaining_show);
		oInput.addEventListener('blur', charsRemaining_hide);
		oInput.addEventListener('change', charsRemaining_hide);
	}
})();
