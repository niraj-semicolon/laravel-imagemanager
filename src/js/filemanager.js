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
            //url: '{{ url('filemanager') }}?target=' + target,
            url: 'filemanager?target=' + target,
            //url: 'index.php?route=common/filemanager&user_token=' + getURLVar('user_token') + '&target=' + $element.parent().find('input').attr('id') + '&thumb=' + $element.attr('id'),
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

});