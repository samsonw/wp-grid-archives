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
          <th scope="row">Post Date</th>
          <td><label for="post_date_not_display"><input type="checkbox" value="1" id="post_date_not_display" name="post_date_not_display" <?php if($options['post_date_not_display']) echo 'checked="checked"';?> > Do not display post date</label></td>
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

