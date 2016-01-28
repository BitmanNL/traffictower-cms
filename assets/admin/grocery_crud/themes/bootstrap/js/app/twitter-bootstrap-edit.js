$(document).ready(function(){

    if($('#fancybox-loading')[0]){
        $('#fancybox-loading').remove();
    }

    //  Salva as informações e retorna a listagem inicial
    $('#save-and-go-back-button').click(function(){
        submitCrudForm(jQuery('#crudForm'), true);
    });
    //  Faz as verificações e submete o formulario
    $(document).on('click', '.submit-form', function(){
        submitCrudForm(jQuery('#crudForm'), false);
    });

    $(document).on('click', '.return-to-list', function() {
        jQuery.noConflict();
        CMS.modal({
            title: 'Terug naar lijst',
            message: message_alert_edit_form,
            callBackSubmit: function(){
                window.location = list_url;
            }
        });
        return false;
    });
    
    $('#cancel-button').click(function(){
        if( $(this).hasClass('back-to-list') || confirm( message_alert_edit_form ) )
        {
            window.location = list_url;
        }

        return false;
    });

});

//  Simula o efeito RESET no formulário de inserção de conteudo
function clearForm()
{
    $('#crudForm').find(':input').each(function() {
        switch(this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
            case 'textarea':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });

    /* Clear upload inputs  */
    $('.open-file, .gc-file-upload, .hidden-upload-input').each(function(){
        $(this).val('');
    });

    $('.upload-success-url').hide();
    $('.fileinput-button').fadeIn("normal");
    /* -------------------- */

    $('.remove-all').each(function(){
        $(this).trigger('click');
    });

    $('.chosen-multiple-select, .chosen-select, .ajax-chosen-select').each(function(){
        $(this).trigger("liszt:updated");
    });
}

//  Submete o formulário para inserir os dados no BD
function submitCrudForm( crud_form, save_and_close ){
    crud_form.ajaxSubmit({
        url: validation_url,
        dataType: 'json',
        cache: 'false',
        beforeSend: function(){
            $("#ajax-loading").fadeIn('fast');
        },
        afterSend: function(){
            $("#ajax-loading").fadeOut('fast').removeClass('show');;
        },
        success: function(data){
            $("#ajax-loading").fadeOut('fast');
            if(data.success)
            {
                jQuery('#crudForm').ajaxSubmit({
                    dataType: 'text',
                    cache: 'false',
                    beforeSend: function(){
                        $("#ajax-loading").addClass('show loading');
                    },
                    success: function(result){

                        $("#ajax-loading").fadeOut("slow").removeClass('show');
                        data = $.parseJSON( result );
                        if(data.success)
                        {
                            if(save_and_close)
                            {
                                window.location = data.success_list_url;
                                return true;
                            }
                            CMS.alert(data.success_message, 'success');
                        }
                        else
                        {
                            CMS.alert(message_update_error);
                        }
                    },
                    error: function(){
                        CMS.alert(message_update_error);
                    }
                });
            }
            else
            {
                $('.field_error').each(function(){
                    $(this).removeClass('field_error');
                });

                CMS.alert(data.error_message);

                $.each(data.error_fields, function(index,value){
                    $('input[name='+index+']').addClass('field_error');
                });
            }
        },
        error: function(){
            $("#ajax-loading").fadeOut('fast').removeClass('show');;
            CMS.alert(message_update_error);
        }
    });
    return false;
}