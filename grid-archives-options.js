jQuery(document).ready(function($) {
  $('input[name=style_format]:radio').click(function() {
    if($(this).val() === 'compact'){
      $('.compact_settings').fadeIn('slow');
    }else{
      $('.compact_settings').fadeOut('slow');
    }
  });
  $('#load_resources_only_in_grid_archives_page').click(function() {
    if($(this).is(':checked')){
      $('#grid_archives_page_names').removeAttr('disabled');
    }else{
      $('#grid_archives_page_names').attr('disabled', 'disabled');
    }
  });
  $('#featured_image').click(function(){
    if(!$(this).is(':checked')){
      $('#post_content_max_len').removeAttr('disabled');
    }else{
      $('#post_content_max_len').attr('disabled', 'disabled');
    } 
  });
  $('input[name=group_by]:radio').click(function(){
    if($(this).val() === 'y'){
      $('.compact_settings').fadeOut('slow');
    }else{
      $('.compact_settings').fadeIn('slow');
    } 
  });
});
