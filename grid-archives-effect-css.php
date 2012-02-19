<?php
header("Content-type: text/css; charset: UTF-8");

require_once('../../../wp-load.php');
$options = get_option('grid_archives_options');

if ($options['post_hovered_highlight']) {
    echo <<< CSS
#grid_archives ul li div.ga_post_main:hover {
  -moz-box-shadow: 4px 3px 5px #111;
  -webkit-box-shadow: 4px 3px 5px #111;
  box-shadow: 4px 3px 5px #111;
}
CSS;
}

if ($options['monthly_summary_hovered_rotate']) {
    echo <<< CSS
#grid_archives ul li.ga_year_month .ga_monthly_summary:hover {
  -moz-transform: rotate(-2deg);
  -webkit-transform: rotate(-2deg);
  -o-transform: rotate(-2deg);
  transform: rotate(-2deg);
}
CSS;
}
?>
