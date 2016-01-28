var CMS = {};

(function($, undefined) {

	CMS.modal = function(data) {

		// overwrite default data
		var default_data = {
			title: 'Default titel',
			message: 'Default message',
			cancelButton: 'Annuleren',
			submitButton: 'OK',
			callBackSubmit: function(){},
			callBackCancel: function(){}
		};
		data = $.extend({}, default_data, data);

		// remove old alert boxes
		$('#cms_modal').remove();

		// create modal box
		var modal = $('<div>').attr({id: 'cms_modal'}).addClass('modal fade');
		var modalDialog = $('<div>').addClass('modal-dialog');
		var modalContent = $('<div>').addClass('modal-content');
		var modalHeader = $('<div>').addClass('modal-header');
		var modalBody = $('<div>').addClass('modal-body');
		var modalFooter = $('<div>').addClass('modal-footer');
		var buttonClose = $('<button>').attr({type: 'button', 'data-dismiss': 'modal', 'aria-hidden': 'true'}).addClass('close').html('&times;');
		var modalTitle = $('<h3>').addClass('modal-title').text(data.title);
		var modalMessage = $('<p>').addClass('modal-message').html(data.message);

		if (data.cancelButton !== false && data.cancelButton !== null)
		{
			var buttonCancel = $('<button>').addClass('btn btn-default modal-cancel').attr({'data-dismiss': 'modal', 'aria-hidden': 'true'}).text(data.cancelButton).unbind().click(data.callBackCancel);
		}
		if (data.submitButton !== false && data.submitButton !== null)
		{
			var buttonOk = $('<button>').addClass('btn btn-danger modal-ok').attr({'data-dismiss': 'modal'}).text(data.submitButton).unbind().click(data.callBackSubmit);
		}

		modalHeader.append(buttonClose).append(modalTitle);
		modalBody.append(modalMessage);

		if (typeof(buttonCancel) !== 'undefined')
		{
			modalFooter.append(buttonCancel);
		}
		if (typeof(buttonOk) !== 'undefined')
		{
			modalFooter.append(buttonOk);
		}

		modalContent.append(modalHeader).append(modalBody);
		modalDialog.append(modalContent);
		modal.append(modalDialog);
		if (typeof(buttonCancel) !== 'undefined' || typeof(buttonOk) !== 'undefined')
		{
			modalContent.append(modalFooter);
		}

		$('body').prepend(modal);

		// show modal
		modal.modal('show');

	};

	CMS.alertBase = function(){

		// create base container for alert boxes
		var alertBase = $('<div>').attr({id: 'cms_alert'}).addClass('container');
		var alertContainer = $('<div>').addClass('container row').css({position: 'fixed', top: '65px', 'z-index': 1000});

		alertBase.append(alertContainer);
		$('body').prepend(alertBase);

	};

	CMS.alert = function(message, type, timeout) {

		// if not present use default
		if(typeof(type) === 'undefined') {
			type = 'danger';
		}

		if(typeof(timeout) === 'undefined') {
			timeout = 4000;
		}

		// create alert box
		var alertBox = $('<div>').addClass('alert alert-block alert-'+type);
		var button = $('<button>').addClass('close').attr({type: 'button', 'data-dismiss': 'alert'}).html('&times;');

		alertBox.append(button).append(message);
		$('#cms_alert .container').append(alertBox);

		// show alert
		alertBox.hide().fadeIn(200);

		// hide alert automatically
		setTimeout(function(){ alertBox.fadeOut(); }, timeout);

	};

	// add base to tbody
	CMS.alertBase();

	CMS.popover = function() {

		$.each($('.cms-popover'), function(i, item){
			var title = $(item).attr('data-popover-title');
			var content = $(item).attr('data-popover-content');

			$(item).popover({
				title: title,
				trigger: 'manual',
				html: true,
				content: content
			});

			$(item).click(function(){
				$('.popover').hide();

				if($(item).hasClass('cms-popover-selected')){
					$(item).removeClass('cms-popover-selected');
					$('.cms-popover').removeClass('cms-popover-selected');
				}else{
					$('.cms-popover').removeClass('cms-popover-selected');
					$(item).addClass('cms-popover-selected');
					$(item).popover('show');
				}
			});

		});

	};

	CMS.popover();

	CMS.fileManager = function(sourceId, data) {
		if($('#'+sourceId).length == 1){
			// valid source id

			// get tag name
			var tagName = $('#'+sourceId).prop('tagName');

			// overwrite default data
			var default_data = {
				extensions: '', // comma separated list of extensions,
				dimensions: [], // specific width, height
				format: '', // square
				buttonAddImage: 'Kies afbeelding',
				buttonDeleteImage: 'Verwijder afbeelding',
				loadingText: 'Laden...',
				previewClass: 'file-manager-image-preview',
				path: ''
			};
			data = $.extend({}, default_data, data);

			// add container after input
			var fileManagerContainer = $('<div>').addClass('fileManager-'+sourceId);

			if(tagName == 'INPUT')
			{
				var buttonAddImage = $('<button>').css({'margin-right': '6px'}).attr({type: 'button'}).addClass('btn btn-info btn-add-image-'+sourceId).html(data.buttonAddImage);
				buttonAddImage.click(function(){
					$('#message_'+sourceId).remove();

					top.FileManager.prototype.manualBrowse(
						sourceId,
						data,
						function() {
							CMS.fileManagerInsertImage(
								sourceId,
								data,
								tagName
							);
						},
						true,
						true
					);
				});

				fileManagerContainer.append(buttonAddImage);
			}

			$('#'+sourceId).after(fileManagerContainer);

			if(tagName == 'INPUT')
			{
				// create hidden type and replace old one
				var inputHidden = $('<input>').attr({type: 'hidden'});
				$.each($('#'+sourceId)[0].attributes, function(i, attribute){
					if(attribute.name != 'type'){
						inputHidden.attr(attribute.name, attribute.value);
					}
				});
				inputHidden.addClass('file-input');
				$('#'+sourceId).remove();
				fileManagerContainer.before(inputHidden);
			}
			else
			{
				$('#'+sourceId).hide();
			}


			// add image on load page
			CMS.fileManagerInsertImage(sourceId, data, tagName);

		}

	};

	CMS.fileManagerInsertImage = function(sourceId, data, tagName) {
		if(tagName == 'INPUT')
		{
			var image = $('#'+sourceId).val();
		}
		else
		{
			var image = $('#'+sourceId).text();
			$('#'+sourceId).remove();
		}

		// cleanup
		$('#'+sourceId+'_container').remove();
		$('#btn_'+sourceId+'_remove').remove();
		$('#message_'+sourceId+'_container').remove();

		// if image is chosen
		if(image !== ''){
			var container = $('<div>').attr({id: sourceId+'_container'}).addClass('file-image');

			// image
			var image = $('<img>').attr({
				src: image,
				'class': 'img-polaroid'
			}).addClass(data.previewClass);
			var imageContainer = $('<div>').append(image).css({'margin': '6px 0'});

			if(tagName == 'INPUT')
			{
				var containerMessage = $('<span>').attr({id: 'message_'+sourceId+'_container'}).html(data.loadingText).addClass('file-message');

				// delete button
				var buttonDeleteImage = $('<button>').css({'margin-right': '6px'}).attr({type: 'button', id: 'btn_'+sourceId+'_remove'}).addClass('btn btn-danger btn-delete-image').text(data.buttonDeleteImage).click(function(){
					CMS.fileManagerCleanup(sourceId);
				});

				$('.btn-add-image-'+sourceId).after(containerMessage);
				containerMessage.after(container);
			}
			else
			{
				$('.fileManager-'+sourceId).append(container);
			}

			// insert image
			image.load(function(){
				container.append(imageContainer);

				if(tagName == 'INPUT')
				{
					$(containerMessage).before(buttonDeleteImage);

					var valid = true;
					var errorMessage = "";

					// restrictions
					if(data.format === 'square')
					{
						if(image.width() !== image.height()){
							valid = false;
							errorMessage = "Afbeelding moet vierkant zijn. Probeer een nieuwe afbeelding te kiezen.";
						}
					}
					if(data.dimensions.length == 2)
					{
						var width = parseInt(data.dimensions[0], 10);
						var height = parseInt(data.dimensions[1], 10);

						if(width > 0 && height > 0){
							// w and h
							if(width != image.width() || height != image.height()){
								valid = false;
								errorMessage = "Afbeelding niet het juiste formaat. Toegestaan: "+width+"px breed, "+height+"px hoog.";
							}
						}else if(width > 0 && height == 0){
							// only w
							if(width != image.width()){
								valid = false;
								errorMessage = "Afbeelding niet het juiste formaat. Toegestaan: "+width+"px breed.";
							}
						}else if(width == 0 && height > 0){
							// only h
							if(height != image.height()){
								valid = false;
								errorMessage = "Afbeelding niet het juiste formaat. Toegestaan: "+height+"px hoog.";
							}
						}
					}

					if(valid){
						containerMessage.empty();
					}else{
						containerMessage.html('<span class="text-error">'+errorMessage+'</span>');
						$('#'+sourceId+'_container').remove();
						$('#btn_'+sourceId+'_remove').remove();
						$('#'+sourceId).val('');
					}
				}
			});
		}
	};

	CMS.fileManagerCleanup = function(sourceId) {
		if(typeof(sourceId) == 'undefined')
		{
			// all
			$('.file-image').remove();
			$('.file-message').remove();
			$('.file-btn-remove').remove();
			$('.file-input').val('');
			$('.file-input').change();
		}
		else
		{
			// single
			$('#message_'+sourceId+'_container').remove();
			$('#'+sourceId+'_container').remove();
			$('#btn_'+sourceId+'_remove').remove();
			$('#'+sourceId).val('');
			$('#'+sourceId).change();
		}
	};

	CMS.help = function(item) {
		var title = '';
		if (typeof $(item).data('title') !== 'undefined') {
			title = $(item).data('title');
		}

		var description = 'Nog geen data-description ingesteld!';
		if (typeof $(item).data('description') !== 'undefined') {
			description = $(item).data('description');
		}

		$(item).popover({
			title: title,
			trigger: 'hover',
			html: true,
			content: description
		});
	};

	// Add help to existing classes
	$.each($('.help'), function(i, item){
		CMS.help(item);
	});

	$('body').delegate('.help', 'mouseover', function() {
		CMS.help(this);
	});

}(jQuery));
