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
                  array($LANG["word_projects"], "../"),
                  array($_SESSION["ot"]["project_name"], "../project.php"),
                  array("News", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>News</h1>

  <p>
    Any time you wish to inform your project translators with any information, just create a news
    item below. If you click the DETAILS link for a news item, you can see who has read the news.
  </p>

  <?php
  if (count($news) == 0)
  {
  ?>

  <div class="notify"><span><span><span><span><span><span><span><span>
    There is no news for this project. Click the button below to create a new news item.
  </span></span></span></span></span></span></span></span></div>

  <?php
  }
  else
  {
  ?>
		<table class="info" width="100%" cellpadding="1" cellspacing="0">
		<tr>
			<th width="35%">Message Subject</th>
			<th width="100">Status</th>
			<th width="150">Creation Date</th>
			<th width="70">DETAILS</th>
			<th width="70">DELETE</th>
		</tr>

		<?php
		foreach ($news as $news_item)
		{
			$news_id = $news_item["news_id"];
			$status_str = "";
			switch ($news_item["status"])
			{
				case "online":
					$status_str = "<span class='green'>Online</span>";
					break;
				case "draft":
					$status_str = "<span class='orange'>Draft</span>";
					break;
			}

			// format dates
			$creation_date = ot_get_date("", $news_item["creation_date"], "M jS Y, g:i A");

			echo "<tr>
							<td>{$news_item['subject']}</td>
							<td align='center'>$status_str</td>
							<td>$creation_date</td>
							<td align='center'><a href='details.php?news_id=$news_id'>DETAILS</a></td>
							<td align='center'><a href='index.php?delete=$news_id'>DELETE</a></td>
						</tr>";
		}
		?>
		</table>

  <?php
  }
  ?>

  <p>
    <form action="new.php" method="post">
      <input type="submit" value="Add News" />
  	</form>
  </p>

  <div class="hr"></div>
  <p>
    <a href="../project.php">&lt;&lt; Back to Project</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
