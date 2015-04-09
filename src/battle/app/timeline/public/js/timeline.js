$(window).load(function(){

	// ---- Helpers ----

	// stop event bubbling
	function stop_bubbling(e){
		if (!e) var e = window.event;
		e.cancelBubble = true;
		if (e.stopPropagation) 
			e.stopPropagation();
		e.preventDefault();
	}

	// ---- Watchers ----

	$('.watcher__more_button').on('click', function(e){
		stop_bubbling(e);
		$(this).nextAll(".watcher__form").toggleClass('hidden');
	});
});