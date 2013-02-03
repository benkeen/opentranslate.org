<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_index.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($_SESSION["ot"]["project_name"], "../project.php"),
                  array("News", ""),
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>News</h1>

  <br />
  <?php

  if ($num_unread_news > 0)
    echo "<div>You have <b>$num_unread_news</b> unread news items.</div><br />";

  if (count($news) == 0)
  {
  ?>

  <div class="notify"><span><span><span><span><span><span><span><span>
    There has been no news for this project.
  </span></span></span></span></span></span></span></span></div>

  <?php
  }
  else
  {
  ?>
		<table class="info" width="100%" cellpadding="1" cellspacing="0">
		<tr>
			<th width="50%">Subject</th>
			<th>Status</th>
			<th width="100">Date</th>
			<th width="70">READ</th>
		</tr>

		<?php
		foreach ($news as $news_item)
		{
			$news_id = $news_item["news_id"];
      $status_str = "";

      if (in_array($news_id, $unread_news_ids))
        $status_str = "<span class='green'>Unread</span>";
      else
        $status_str = "<span class='light_grey'>Read</span>";

			// format dates
			$creation_date = ot_get_date("", $news_item["creation_date"], "M jS Y, g:i A");

			echo "<tr>
							<td>{$news_item['subject']}</td>
							<td align='center'>$status_str</td>
							<td>$creation_date</td>
							<td align='center'><a href='details.php?news_id=$news_id'>READ</a></td>
						</tr>";
		}
		?>
		</table>

  <?php
  }
  ?>

  <br />
  <div class="hr"></div>

  <p>
    <a href="../project.php">&lt;&lt; Back to Project</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>