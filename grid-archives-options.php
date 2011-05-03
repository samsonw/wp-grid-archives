<div class="wrap">
  <h2>Grid Archives Settings</h2>

  <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" name="grid_archives">
    <?php wp_nonce_field('grid-archives-nonce'); ?>

    <h3>Usage</h3>
    <p>Create an archive page for Grid Archives, then insert the code "[grid_archives]" in the content.</p>

    <br/>
    <h3>Display Style</h3>
    <p>Tweak the display style of the archives page.</p>

    <table class="form-table">
      <tbody><tr>
          <th><label for="post_title_max_len">Post Title Maximum Length</label></th>
          <td><input type="text" class="small-text" value="<?php echo $options['post_title_max_len']; ?>" id="post_title_max_len" name="post_title_max_len"> characters</td>
        </tr>
        <tr>
          <th><label for="post_content_max_len">Post Content Maximum Length</label></th>
          <td><input type="text" class="small-text" value="<?php echo $options['post_content_max_len']; ?>" id="post_content_max_len" name="post_content_max_len"> characters</td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="post_date_not_display">Post Date</label></th>
          <td><input type="checkbox" value="1" id="post_date_not_display" name="post_date_not_display" <?php if($options['post_date_not_display']) echo 'checked="checked"';?> > Do not display post date</td>
        </tr>
        <tr>
          <th scope="row">Post Date Format</th>
          <td>
            <fieldset><legend class="screen-reader-text"><span>Post Date Format</span></legend>
              <label title="F j, Y"><input type="radio" value="F j, Y" name="post_date_format" <?php if($options['post_date_format'] === 'F j, Y') echo 'checked="checked"';?> > <?php echo date("F j, Y") ;?></label><br>
              <label title="j M Y"><input type="radio" value="j M Y" name="post_date_format" <?php if($options['post_date_format'] === 'j M Y') echo 'checked="checked"';?> > <?php echo date("j M Y") ;?></label><br>
              <label title="Y/m/d"><input type="radio" value="Y/m/d" name="post_date_format" <?php if($options['post_date_format'] === 'Y/m/d') echo 'checked="checked"';?> > <?php echo date("Y/m/d") ;?></label><br>
              <label title="m/d/Y"><input type="radio" value="m/d/Y" name="post_date_format" <?php if($options['post_date_format'] === 'm/d/Y') echo 'checked="checked"';?> > <?php echo date("m/d/Y") ;?></label><br>
              <label title="d/m/Y"><input type="radio" value="d/m/Y" name="post_date_format" <?php if($options['post_date_format'] === 'd/m/Y') echo 'checked="checked"';?> > <?php echo date("d/m/Y") ;?></label><br>
              <label><input type="radio" value="custom" name="post_date_format" <?php if($options['post_date_format'] === 'custom') echo 'checked="checked"';?> > Custom: </label><input type="text" class="small-text" value="<?php echo $options['post_date_format_custom']; ?>" name="post_date_format_custom"> <?php echo date($options['post_date_format_custom']); ?>
          	<p><a target="_blank" href="http://php.net/manual/en/function.date.php">Documentation on date formatting</a>. Click “Save Changes” to update sample output.</p>
            </fieldset>
          </td>
        </tr>
        <tr>
          <th scope="row">Month Date Format</th>
          <td>
            <fieldset><legend class="screen-reader-text"><span>Month Date Format</span></legend>
              <label title="Y.m"><input type="radio" value="Y.m" name="month_date_format" <?php if($options['month_date_format'] === 'Y.m') echo 'checked="checked"';?> > <?php echo date("Y.m") ;?></label><br>
              <label title="m.Y"><input type="radio" value="m.Y" name="month_date_format" <?php if($options['month_date_format'] === 'm.Y') echo 'checked="checked"';?> > <?php echo date("m.Y") ;?></label><br>
              <label title="M Y"><input type="radio" value="M Y" name="month_date_format" <?php if($options['month_date_format'] === 'M Y') echo 'checked="checked"';?> > <?php echo date("M Y") ;?></label><br>
              <label title="Y M"><input type="radio" value="Y M" name="month_date_format" <?php if($options['month_date_format'] === 'Y M') echo 'checked="checked"';?> > <?php echo date("Y M") ;?></label><br>
              <label title="Y/m"><input type="radio" value="Y/m" name="month_date_format" <?php if($options['month_date_format'] === 'Y/m') echo 'checked="checked"';?> > <?php echo date("Y/m") ;?></label><br>
              <label title="m/Y"><input type="radio" value="m/Y" name="month_date_format" <?php if($options['month_date_format'] === 'm/Y') echo 'checked="checked"';?> > <?php echo date("m/Y") ;?></label><br>
              <label><input type="radio" value="custom" name="month_date_format" <?php if($options['month_date_format'] === 'custom') echo 'checked="checked"';?> > Custom: </label><input type="text" class="small-text" value="<?php echo $options['month_date_format_custom']; ?>" name="month_date_format_custom"> <?php echo date($options['month_date_format_custom']); ?>
          	<p><a target="_blank" href="http://php.net/manual/en/function.date.php">Documentation on date formatting</a>. Click “Save Changes” to update sample output.</p>
            </fieldset>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="post_hovered_highlight">Highlight Post Being Hovered:</label></th>
          <td><input type="checkbox" value="1" id="post_hovered_highlight" name="post_hovered_highlight" <?php if($options['post_hovered_highlight']) echo 'checked="checked"';?> > Enable</td>
        </tr>
        <tr>
          <th scope="row"><label for="monthly_summary_hovered_rotate">Rotate Monthly Summary Being Hovered:</label></th>
          <td><input type="checkbox" value="1" id="monthly_summary_hovered_rotate" name="monthly_summary_hovered_rotate" <?php if($options['monthly_summary_hovered_rotate']) echo 'checked="checked"';?> > Enable</td>
        </tr>
        <tr>
          <th><label for="custom_css_styles">Custom CSS Styles</label></th>
          <td><textarea id="custom_css_styles" name="custom_css_styles" cols="60" rows="5"><?php echo $options['custom_css_styles']; ?></textarea><br/>
            <span class="description">These custom CSS styles will overwrite the Grid Archives default CSS styles.</span><br/>
            <span class="description">For example, to custom the box size and its background color, you can use custom css like:</span><br/>
            <span class="description">#grid_archives ul li div.ga_post_main {background-color: #0D0D0D; width: 150px; height: 150px;}</span><br/>
            <span class="description">[You can also copy all the Grid Archives default css from "Plugin Editor" here and make any modifications you like, these modifications won't be lost when you update the plugin.]</span>
          </td>
        </tr>
    </tbody></table>

    <br/>
    <h3>Summarize Your Month</h3>
    <p>It's useful to share your thoughts and comments about one typical month.</p>
    
    <p>Default monthly summary if not explicitly specified:</p>
    <table class="form-table">
      <tbody><tr>
        <th><label for="default_monthly_summary">Default monthly summary:</label></th>
        <td><input type="text" class="regular-text" value="<?php echo $options['default_monthly_summary']; ?>" id="default_monthly_summary" name="default_monthly_summary"> (leave it blank to show nothing)</td>
      </tr>
    </tbody></table>
    
    <br/>
    <p>Input the month summary in the form "year.month##description", one month per line, the description will be displayed beside the month in the archives page:</p>

    <textarea name="monthly_summaries" cols="80" rows="20"><?php echo $options["monthly_summaries"]; ?></textarea>
    
  </div>
  <p class="submit"><input type="submit" value="Save Changes" class="button-primary" name="submit"></p>
</form>

