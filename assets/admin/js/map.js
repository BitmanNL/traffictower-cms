/**
 * Grocery Crud map extension
 *
 * Voeg aan grocery crud to met de volgende code:
 *
 * $this->javascript_files[] = base_url('assets/admin/js/map.js');
 *
 */
if(typeof(window.LocationChooser) === 'undefined'){
	LocationChooser = {
		init: function(){
			var latField = document.getElementById('field-lat');
			var lonField = document.getElementById('field-lon');
			
			if(typeof(latField) !== 'undefined' && typeof('lonField') !== 'undefined'){
				this.latField = $('#field-lat');
				this.lonField = $('#field-lon');

				// store lat & lon
				if(typeof this.latField.attr('value') !== 'undefined'){
					this.centerLat = this.latField.val();
					this.centerLon = this.lonField.val();
					this.editable = true;
					var width = $("#field-lat").parent().width();
				}
				else
				{
					this.centerLat = this.latField.text();
					this.centerLon = this.lonField.text();
					this.editable = false;
					var width = $("#field-lat").width();
				}
				
				// remember position in table
				var predecessor = $("#lat_field_box").prev();
				// and check width of input element
				
				// hide rows for lat & lon fields
				this.latField.parentsUntil("tbody").css('display', 'none');
				this.lonField.parentsUntil("tbody").css('display', 'none');
				
				// obtain a unique dom id for map div
				this.mapDivId = 'grocery_map';
				
				var html = '';
				html += '<tr><td><label>Locatie:</label></td>';
				html += '<td>';
				if (this.editable){
					html += '	<div class="form_group">';
					html += '		<div class="input-group">';
					html += '			<input type="text" id="' + this.mapDivId + '_zoekveld" class="form-control" placeholder="Zoek adres" />';
					html += '			<div class="input-group-btn"><button class="btn btn-default" type="button" id="' + this.mapDivId + '_button">Ga naar adres</button></div>';
					html += '		</div>';
					html += '	</div>';

				}
				html += ' 	<div id="' + this.mapDivId + '"></div>';
				html += '</td></tr>';

				// add row with map div to table
				predecessor.after(html);
				
				// and store a reference to the dom object so we don't have to query for it all the time
				this.mapDiv = document.getElementById(this.mapDivId);
				if (this.mapDiv !== null){
					this.mapDiv.style.width = '100%';
					this.mapDiv.style.height = '500px';
					
					// add {max-width: inherit} to img for map (bootstrap messes this up!)
					var stylesheet = document.styleSheets[0],
						selector = "#" + this.mapDivId + " img", rule = "{max-width: inherit}";
					if(stylesheet.insertRule) {
						stylesheet.insertRule(selector + rule, stylesheet.cssRules.length);
					}else if(stylesheet.addRule){
						stylesheet.addRule(selector, rule, -1);
					}
					
					// check for availability of google maps object
					if(typeof google === 'object' && typeof google.maps === 'object'){
						LocationChooser.showMap();
					}else{
						$.getScript("http://maps.google.com/maps/api/js?sensor=true&callback=LocationChooser.showMap");
					}
				}
			}
		},
		showMap: function(){
			var that = this;

			if (this.centerLat ===  '' || this.centerLon === ''){
				var centerPoint = new google.maps.LatLng(52.23708314304311, 5.279370250000056);
				
				var mapOptions = {
					zoom: 6,
					center: centerPoint
				};
			}
			else
			{
				var centerPoint = new google.maps.LatLng(this.centerLat, this.centerLon);
				
				var mapOptions = {
					zoom: 16,
					center: centerPoint
				};
			}
			
			this.map = new google.maps.Map(this.mapDiv, mapOptions);

			this.placeMarker(centerPoint);

			if (this.editable){
				// add click event to map
				google.maps.event.addListener(this.map, "click", function(event){
					that.placeMarker(event.latLng);
				});

				// add search handle
				$('#' + this.mapDivId + '_zoekveld').change(function(){
					that.goToAddress(that);
				});
			}
		},
		placeMarker: function(centerPoint){
			var that = this;

			// remove previous marker if it exists
			if (typeof this.marker !== 'undefined')
			{
				this.marker.setMap(null);
			}

			// create marker
			this.marker = new google.maps.Marker({
				position: centerPoint,
				map: this.map,
				draggable:this.editable
			});

			this.centerLat = centerPoint.lat();
			this.centerLon = centerPoint.lng();
			
			that.latField.val(this.centerLat);
			that.lonField.val(this.centerLon);

			if (this.editable){
				// add dragend listener to marker
				google.maps.event.addListener(this.marker, 'dragend', function(event){
					this.centerLat = event.latLng.lat();
					this.centerLon = event.latLng.lng();
					
					that.latField.val(this.centerLat);
					that.lonField.val(this.centerLon);
				});	
			}
		},
		goToAddress: function(that){
			var address = $('#' + this.mapDivId + '_zoekveld').val();

			if (address != ''){
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({address:address}, function(results){
					if (results.length > 0){
						$('#' + this.mapDivId + '_zoekveld').val(results[0].formatted_address);

						that.map.setCenter(results[0].geometry.location);
						that.map.setZoom(16);
						that.placeMarker(results[0].geometry.location);
					}
				});
			}
		}
	};
	
	// initialise map on page load
	$(window).load(function(){
		LocationChooser.init();
	});
}


