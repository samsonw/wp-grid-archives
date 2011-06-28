jQuery(document).ready(function() {
  jQuery('#load_resources_only_in_grid_archives_page').click(function() {
    if(jQuery(this).is(':checked')){
      jQuery('#grid_archives_page_names').removeAttr('disabled');
    }else{
      jQuery('#grid_archives_page_names').attr('disabled', 'disabled');
    }
  });
});
