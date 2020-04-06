console.log('running wp-media script');

jQuery(document).ready(function($){

  var mediaUploader;

  $('#structure_file').click(function(e) {
    clickedButton = jQuery(this);
    e.preventDefault();
    // If the uploader object has already been created, reopen the dialog
      if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: 'Choose or Upload CSV File',
      button: {
      text: 'Choose or Upload CSV File'
    }, multiple: false });

    // When a file is selected, grab the URL and set it as the text field's value
    mediaUploader.on('select', function() {
      attachment = mediaUploader.state().get('selection').first().toJSON();
      //$('#city-coupon-business-logo').val(attachment.url);
      var place = $('#structure_path');
      place.val(attachment.url);
      delete clickedButton;
    });
    // Open the uploader dialog
    mediaUploader.open();

  });

});

/*
var mediaUploader;
var button;
var img_sels = document.querySelectorAll('.equips-img-select');
img_sels.forEach(function (elm) {
  elm.onclick = function () {
    button = this;
    //e.preventDefault();
    if (mediaUploader) {
      medaUploader.open();
      return;
    }
    mediaUploader = wp.media.frames.file_frame = wp.media(
      {
        title: 'Choose Image',
        button: {
          text: 'Choose Image'
        },
        multiple: false
      }
    );
    mediaUploader.addEventListener("select", function () {
      var attachment = mediaUploader.state().get('selection').first().toJSON();
      var place = button.previousElementSibling;
      place.value(attachment.url);
    });
    mediaUploader.open();
  }
});
*/
