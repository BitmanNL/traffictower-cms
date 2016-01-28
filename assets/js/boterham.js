"use strict";

$(document).ready(function() {

	var boterham = $('<input>').attr({
		type: 'hidden',
		name: 'boterham'
	}).val(1);

	$('form').append(boterham);

});