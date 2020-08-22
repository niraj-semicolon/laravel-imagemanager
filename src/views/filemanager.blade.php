<div id="filemanager" class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">{{ $data['heading_title'] }}</h4>
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-sm-5"><a href="{{ $data['parent'] }}" data-toggle="tooltip" title="Parent" id="button-parent" class="btn btn-default"><i class="fa fa-level-up"></i></a> <a href="{{ $data['refresh'] }}" data-toggle="tooltip" title="Refresh" id="button-refresh" class="btn btn-default"><i class="fa fa-refresh"></i></a>
          <button type="button" data-toggle="tooltip" title="Upload" id="button-upload" class="btn btn-primary"><i class="fa fa-upload"></i></button>
          <button type="button" data-toggle="tooltip" title="Folder" id="button-folder" class="btn btn-default"><i class="fa fa-folder"></i></button>
          <button type="button" data-toggle="tooltip" title="Delete" id="button-delete" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
        </div>
        <div class="col-sm-7">
          <div class="input-group">
            <input type="text" name="search" value="{{ $data['filter_name'] }}" placeholder="Search.." class="form-control">
            <span class="input-group-btn">
            <button type="button" data-toggle="tooltip" title="Search" id="button-search" class="btn btn-primary"><i class="fa fa-search"></i></button>
            </span></div>
        </div>
      </div>
      <hr />
      <div class="row" id="filemanager-data"></div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
@if($data['target'] != '')
$("body").on('click', '#filemanager-data a.thumbnail', function(e) {
	e.preventDefault();
	//set scr
	$('#{{ $data['target'] }}').parent().find('div.image-manager-image-div').find('img').attr('src', $(this).find('img').attr('src'));
	//make <a visible
	$('#{{ $data['target'] }}').parent().find('div.image-manager-image-div').show();
	//hide <button
	$('#{{ $data['target'] }}').parent().find('.image-manager-button').hide();
	
	$('#{{ $data['target'] }}').val($(this).parent().find('input').val());

	$('#modal-image').modal('hide');
});
@endif

$('a.directory').on('click', function(e) {
	e.preventDefault();

	$('#modal-image').load($(this).attr('href'));
});

$('#button-parent').on('click', function(e) {
	e.preventDefault();

	$('#modal-image').load($(this).attr('href'));
});

$('#button-refresh').on('click', function(e) {
	e.preventDefault();

	$('#modal-image').load($(this).attr('href'));
});

$('input[name=\'search\']').on('keydown', function(e) {
	if (e.which == 13) {
		$('#button-search').trigger('click');
	}
});

$('#button-search').on('click', function(e) {
	var url = '{{ url('filemanager?directory='.$data['directory']) }}'

	var filter_name = $('input[name=\'search\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}


	@if($data['target'] != '')
	url += '&target=' + '{{ $data["target"] }}';
	@endif

	$('#modal-image').load(url);
});
//--></script>
<script type="text/javascript"><!--
$('#button-upload').on('click', function() {
	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file[]" value="" multiple="multiple" /></form>');

	$('#form-upload input[name=\'file[]\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file[]\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: '{{ url('filemanager/upload?directory='.$data['directory']) }}',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				beforeSend: function() {
					$('#button-upload i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
					$('#button-upload').prop('disabled', true);
				},
				complete: function() {
					$('#button-upload i').replaceWith('<i class="fa fa-upload"></i>');
					$('#button-upload').prop('disabled', false);
				},
				success: function(json) {
					if (json['error']) {
						alert(json['error']);
					}

					if (json['success']) {
						alert(json['success']);

						$('#button-refresh').trigger('click');
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});

$('#button-folder').popover({
	html: true,
	placement: 'bottom',
	trigger: 'click',
	title: 'Folder Name',
	content: function() {
		html  = '<div class="input-group">';
		html += '  <input type="text" name="folder" value="" placeholder="Folder Name" class="form-control">';
		html += '  <span class="input-group-btn"><button type="button" title="New Folder" id="button-create" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></span>';
		html += '</div>';

		return html;
	}
});

$('#button-folder').on('shown.bs.popover', function() {
	$('#button-create').on('click', function() {
		$.ajax({
			url: '{{ url('filemanager/folder?directory='.$data['directory']) }}',
			type: 'post',
			dataType: 'json',
			data: 'folder=' + encodeURIComponent($('input[name=\'folder\']').val()),
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			beforeSend: function() {
				$('#button-create').prop('disabled', true);
			},
			complete: function() {
				$('#button-create').prop('disabled', false);
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
					alert(json['success']);
					//remove popover
        			$('#button-folder').popover('dispose');
					$('#button-refresh').trigger('click');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
});

$('#modal-image #button-delete').on('click', function(e) {
	
	if (confirm('Are you sure?')) {
		$.ajax({
			url: '{{ url('filemanager/delete') }}',
			type: 'post',
			dataType: 'json',
			data: $('input[name^=\'path\']:checked'),
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			beforeSend: function() {
				$('#button-delete').prop('disabled', true);
			},
			complete: function() {
				$('#button-delete').prop('disabled', false);
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
					alert(json['success']);

					$('#button-refresh').trigger('click');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
});

//pagination
$( document ).ready(function() {
    
	//when page loads, load the first page
	var target='';
	@if($data['target'] != '')
	target = '&target=' + '{{ $data["target"] }}';
	@endif

	var page = 1;
	var enable_pagination = true;
	loadMoreData(page);

	$( ".modal-body" ).scroll(function(e) {

		e.preventDefault();
		console.log("scrollTop:" + $(".modal-body").scrollTop()+"height:" + $(".modal-body").height()+"window height:" + $(window).height());
		//enable_pagination
		if( ( $(".modal-body").scrollTop() >= $(".modal-body").height() - 10) && enable_pagination) {
	        page++;
	        loadMoreData(page);
	    }

	});

	function loadMoreData(page){
		enable_pagination = false; //make flag false, so that page doesnt keep on calling this function while previous result is being loaded
		console.log(page);
		$.ajax({
			url: "{{ url('filemanager/pagination') }}?page="+ page + target,
			type: "get",
			success: function(html) {
				//no response from pagination, means no more records so do not let pagination
				if(html=='') { 
					enable_pagination = false;
				}else{
					enable_pagination = true;
					$("#filemanager-data").append(html);
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
});
</script>