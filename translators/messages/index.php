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
                  array("Message Board", ""),
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Message Board</h1>

  <p>
    This page lists all news and correspondence between yourself and the project manager, within Open
    Translate. Highlighted rows indicate a new response to your question.
  </p>

  <h3>News</h3>

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
			<th width="100">Status</th>
			<th>Date</th>
			<th width="70">VIEW</th>
		</tr>

		<?php
		foreach ($news as $news_item)
		{
			$news_id = $news_item["news_id"];
      $status_str = "";
      $css_class = "";
      if (in_array($news_id, $unread_news_ids))
      {
        $status_str = "<span class='green'>Unread</span>";
        $css_class = "highlight";
      }
      else
        $status_str = "<span class='light_grey'>Read</span>";

			$creation_date = ot_get_date("", $news_item["creation_date"], "M jS Y, g:i A");

			echo "<tr class='$css_class'>
							<td>{$news_item['subject']}</td>
							<td align='center'>$status_str</td>
							<td>$creation_date</td>
							<td align='center'><a href='news_item.php?news_id=$news_id'>VIEW</a></td>
						</tr>";
		}
		?>
		</table>

  <?php
  }
  ?>

  <br />
  <h3>General Questions</h3>

  <?php if (count($project_questions) == 0) { ?>

    <div class="notify"><span><span><span><span><span><span><span><span>
      <div style="float:right"><input type="button" value="Have a Question?" onclick="window.location='../contact.php'" /></div>
      You have not contacted the project manager with any questions.
    </span></span></span></span></span></span></span></span></div>

  <?php } else { ?>

    <?php
    // display page navigation
    ot_display_page_nav($num_project_questions, $g_max_num_project_questions_per_page, $current_project_question_page, "", "pq_page");
    ?>

		<table class="info" width="100%" cellpadding="1" cellspacing="0">
		<tr>
			<th>Subject</th>
			<th>Num Responses</th>
			<th width="100">Status</th>
			<th>Date</th>
			<th width="70">VIEW</th>
		</tr>
    <?php
    for ($i=0; $i<count($project_questions); $i++)
    {
      $question_id = $project_questions[$i]["question_id"];
      $subject = $project_questions[$i]["subject"];
      $thread_status = $project_questions[$i]["thread_status"];
			$creation_date = ot_get_date("", $project_questions[$i]["creation_date"], "M jS Y, g:i A");
      $num_responses = $project_questions[$i]["num_responses"];
      $unread_responses = $project_questions[$i]["unread_responses"];

      $status_str = "";
      switch ($thread_status)
      {
        case "new":
          $status_str = "<span class=\"green\">New</span>";
          break;
        case "in_progress":
          $status_str = "<span class=\"orange\">In Progress</span>";
          break;
        case "resolved":
          $status_str = "<span class=\"light_grey\">Resolved</span>";
          break;
        case "defer":
          $status_str = "<span class=\"blue\">Defer</span>";
          break;
      }

      $css_class = "";
      if ($unread_responses > 0)
      {
        $num_responses .= " (<b>$unread_responses</b> new)";
        $css_class = "highlight";
      }

      echo "
        <tr class='$css_class'>
          <td>$subject</td>
          <td align='center'>$num_responses</td>
          <td align='center'>$status_str</td>
    			<td>$creation_date</td>
    			<td align='center'><a href='question_thread.php?question_id=$question_id'>VIEW</a></td>
        </tr>";
    }
    ?>
    </table>

    <p>
      <input type="button" value="Have a Question?" onclick="window.location='../contact.php'" />
    </p>

  <?php } ?>

  <br />
  <h3>Translation Questions</h3>

 <?php if ($num_translation_questions == 0) { ?>

    <div class="notify"><span><span><span><span><span><span><span><span>
      You have not contacted the project manager with any questions concerning an individual data item
      for translation.
    </span></span></span></span></span></span></span></span></div>

  <?php } else { ?>

    <?php
    // display page navigation
    ot_display_page_nav($num_translation_questions, $g_max_num_data_questions_per_page, $current_translation_question_page, "", "tq_page");
    ?>

		<table class="info" width="100%" cellpadding="1" cellspacing="0">
		<tr>
			<th>Data</th>
			<th>Subject</th>
			<th>Num Responses</th>
			<th>Date</th>
			<th width="70">VIEW</th>
		</tr>
    <?php
    for ($i=0; $i<count($translation_questions); $i++)
    {
      $question_id = $translation_questions[$i]["question_id"];
      $data_id = $translation_questions[$i]["data_id"];
      $data_info = ot_get_data($data_id);
      $version_id = $data_info["version_id"];
      $data = $data_info["data"];
      $data_truncated = mb_substr($data, 0, 100) ;
      if (mb_strlen($data) > 100)
        $data_truncated .= "...";

      $subject = $translation_questions[$i]["subject"];
			$creation_date = ot_get_date("", $translation_questions[$i]["creation_date"], "M jS Y, g:i A");
      $num_responses = $translation_questions[$i]["num_responses"];
      $unread_responses = $translation_questions[$i]["unread_responses"];
      $language_id = $translation_questions[$i]["language_id"];

      $css_class = "";
      if ($unread_responses > 0)
      {
        $num_responses .= " (<b>$unread_responses</b> new)";
        $css_class = "highlight";
      }

      echo "
        <tr class='$css_class'>
          <td>$data_truncated</td>
          <td>$subject</td>
          <td align='center'>$num_responses</td>
    			<td>$creation_date</td>
    			<td align='center'><a href='../data/translate.php?data_id=$data_id&target_language_id=$language_id&version_id=$version_id&tab=2'>VIEW</a></td>
        </tr>";
    }
    ?>
    </table>

    <br />

  <?php } ?>

  <div class="hr"></div>

  <p>
    <a href="../project.php">&laquo; Back to Project</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
