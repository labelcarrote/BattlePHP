// ---------------------------------------------------------------------
// goldigger.js
//
// Uses quandl.com's great json API to retrieve the latest gold rate.
//
// ---------------------------------------------------------------------

// LONDON YESIR
var gold_rate_source = "https://www.quandl.com/api/v3/datasets/LBMA/GOLD.json";
// BUNDESBANK JA JA
//var gold_rate_source = "https://www.quandl.com/api/v3/datasets/BUNDESBANK/BBK01_WT5511.jsonn";

$(window).load(function(){
	// Retrieves gold rate from quandl json api, then refresh the rate
	var t0 = performance.now();
	$.ajax({url: gold_rate_source, success: function(result){
		var t1 = performance.now();
		$(".ping").html("- took " + (t1 - t0) + " milliseconds...");
		$(".gold-rate").html(result.dataset.data[0][1]);
		$(".gold-rate-last-update").html(result.dataset.data[0][0]);
 	}});
});