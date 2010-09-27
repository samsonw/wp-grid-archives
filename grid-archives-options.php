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
          <td> <input type="text" class="small-text" value="<?php echo $options['post_content_max_len']; ?>" id="post_content_max_len" name="post_content_max_len"> characters</td>
        </tr>
    </tbody></table>

    <br/>
    <h3>Summarize Your Month</h3>
    <p>It's useful to share your thoughts and comments about one typical month.</p>
    <p>Input the month summary in the form "year.month##description", one month per line, the description will be displayed beside the month in the archives page:</p>

    <textarea name="monthly_summaries" cols="80" rows="20"><?php echo $options["monthly_summaries"]; ?></textarea>

  </div>
  <p class="submit"><input type="submit" value="Save Changes" class="button-primary" name="submit"></p>
</form>

