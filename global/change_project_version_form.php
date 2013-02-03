<?php

/* -----------------------------------------------------------------------------------------------*\
  Page:        change_project_version_form.php
  Purpose:     This page outputs a form for changing the current project version. It should be
               included in all (if not MOST) pages for a project. It redirects back to the same
               page, which effectively changes the project version.

               If there's only one version, it doesn't output anything since it's not applicable.

  Assumptions: - get_project_versions() has been called, the results of which are accessible through
               $versions. [..]
               - every page that uses this checks to see if a "change_project_version" POST value
               is set and changes the version accordingly.
\* -----------------------------------------------------------------------------------------------*/

// if there's more than one version, provide the option of changing it
if (isset($versions) && count($versions) > 1) {

// find out if this is a translator account or not. If it is, we only show 
$is_translator_account = ($_SESSION["ot"]["account_type"] == "translator") ? true : false; 

?>

<div style="float: right;">
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="change_project_version" value="1" />

		<select name="version_id">
      <?php
      foreach ($versions as $version)
      {
        $tmp_version_id    = $version["version_id"];
        $tmp_version_label = $version["version_label"];

				if ($is_translator_account)
				{
      		if ($version["is_visible"] != "yes" || $version["may_translate"] != "yes")
      		  continue;
				}

        $selected = "";
        if (isset($_SESSION["ot"]["version_id"]) && $_SESSION["ot"]["version_id"] == $tmp_version_id)
          $selected = " selected";

        echo "<option value='$tmp_version_id'{$selected}>$tmp_version_label</option>\n";
      }
      ?>
    </select><input type="submit" value="<?=$LANG['word_select']?>" />
  </form>
</div>

<?php
}
?>