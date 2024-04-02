if (!window.ImshopIoYmlInitialized) {

	

	window.ImshopIoYmlInitialized = true;
}

// On load
setTimeout(function(){
	acritExpImshopIoYmlTriggers();
}, 500);
$(document).ready(function(){
	acritExpImshopIoYmlTriggers();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpImshopIoYmlTriggers();
});
