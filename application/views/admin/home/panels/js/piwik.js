var visits = <?=$visits?>;

$(document).ready(function(){
  var plot2 = $.jqplot ('chart1', [visits], {
      // An axes object holds options for all axes.
      // Allowable axes are xaxis, x2axis, yaxis, y2axis, y3axis, ...
      // Up to 9 y axes are supported.
	    axes:{
	        xaxis:{
	            renderer:$.jqplot.DateAxisRenderer
	        }
	    },
	    animate: true
    });
});