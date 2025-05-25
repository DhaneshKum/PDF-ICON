// jQuery(document).ready(function($){
//     $('.upload_pdf_button').click(function(e) {
//         e.preventDefault();
//         var button = $(this);
//         var custom_uploader = wp.media({
//             title: 'Select PDF',
//             button: {
//                 text: 'Use this PDF'
//             },
//             multiple: false
//         })
//         .on('select', function() {
//             var attachment = custom_uploader.state().get('selection').first().toJSON();
//             button.closest('.options_group').find('input#_product_pdf_url').val(attachment.url);
//         })
//         .open();
//     });
// });
