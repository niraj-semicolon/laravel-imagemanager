$(document).ready(function() {
    //popover on image/thumb
    $(document).on('click', 'div.image-manager-image-div', function(e) {
        var $element = $(this);

        var $popover = $element.data('bs.popover'); // element has bs popover?

        e.preventDefault();

        // destroy all image popovers
        $('div.image-manager-image-div').popover('dispose');
        // remove flickering (do not re-add popover when clicking for removal)
        if ($popover) {
            return;
        }

        $element.popover({
            html: true,
            placement: 'right',
            trigger: 'manual',
            container: $(this).parent(),

            content: function() {
                return '<button type="button" class="btn btn-primary popover-button-image"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
            }
        });

        $element.popover('show');
    
    });

    //load filemanager in modal
    $("body").on('click', '.image-manager-button,.popover-button-image', function(e) {
            
        var $element = $(this);
        var $icon   = $element.find('> i');
        $('#modal-image').remove();
            
        //determine target
        var target = $element.closest('div.image-manager-main-div').find('input').attr('id');

        $.ajax({
            url: 'filemanager?target=' + target,
            dataType: 'html',
            beforeSend: function() {
                $element.prop('disabled', true);
                if ($icon.length) {
                    $icon.attr('class', 'fa fa-circle-o-notch fa-spin');
                }
            },
            complete: function() {
                $element.prop('disabled', false);

                if ($icon.length) {
                    $icon.attr('class', 'fa fa-pencil');
                }
            },
            success: function(html) {
                $('body').append('<div id="modal-image" class="modal">' + html + '</div>');

                $('#modal-image').modal('show');
            }
        });

        //remove popover
        $('div.image-manager-image-div').popover('dispose');
    });

    //clear thumbnail from page
    $("body").on('click', '#button-clear', function(e) {
       
        var $element = $(this);

        //remove scr
        $element.closest('div.image-manager-main-div').find('div.image-manager-image-div').find('img').attr('src', '');
        //make div invisible

        $element.closest('div.image-manager-main-div').find('div.image-manager-image-div').hide();

        //show <button
        $element.closest('div.image-manager-main-div').find('.image-manager-button').show();

        $element.closest('div.image-manager-main-div').find('input').val('');

        $('div.image-manager-image-div').popover('dispose');

    });

});