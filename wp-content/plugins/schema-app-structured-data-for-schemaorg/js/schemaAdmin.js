jQuery(document).ready(function ($) {
    var mediaUploader;

    //// 
    // Upload Publisher Logo Settings Page
    $('#publisher_image_button').click(function (e) {
        e.preventDefault();
        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            }, 
            multiple: false
        });

        // When a file is selected, grab the URL and set it as the text field's value
        mediaUploader.on('select', function () {
            attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#publisher_image').val(attachment.url);
        });
        // Open the uploader dialog
        mediaUploader.open();
    });
	
    // Tab based navigation on settings page
    $(document).on( 'click', '.nav-tab-wrapper a', function() {
        console.log($(this).index());
        $('section').hide();
        $('section').eq($(this).index()).show();
        return false;
    });
    
    // Switch to a specific tab on page load
    function schemaLoadSwitchTab() {
        var tabIndex = 1;                               // Default to Settings tab
        $( "section" ).each(function( index ) {
            if ( $(this)[0].id === schemaData.tab ) {
                tabIndex = index;
            }
        });
        $('section').hide();
        $('section').eq(tabIndex).show();
    }
    
    schemaLoadSwitchTab();
	
});