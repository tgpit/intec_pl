/**
 *	Hot keys
 */
$.alt = function(Key, Callback) {
	$(document).keydown(function(E) {
		if(E.altKey && !E.ctrlKey && !E.metaKey && !E.shiftKey && E.keyCode == Key.charCodeAt(0)) {
			return Callback.apply(this)
		}
	});
};