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
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array("Message Board", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Message Board</h1>

  <br />

  <div>
    This page lists all questions sent from your project translators. Highlighted rows
    indicate a new question or response.
  </div>

  <br />
  <h3>General Questions</h3>

  <?php if (count($project_questions) == 0) { ?>

    <div class="notify"><span><span><span><span><span><span><span><span>
      Nobody has contacted you with any questions.
    </span></span></span></span></span></span></span></span></div>

  <?php } else { ?>

    <?php
    // display page navigation
    ot_display_page_nav($num_project_questions, $g_max_num_project_questions_per_page, $current_project_question_page, "", "pq_page");
    ?>

		<table class="info" width="100%" cellpadding="1" cellspacing="0">
		<tr>
			<th width="130">Translator</th>
			<th>Subject</th>
			<th>Num Responses</th>
			<th width="100">Status</th>
			<th width="150">Date</th>
			<th width="70">VIEW</th>
		</tr>
    <?php
    $accounts = array();
    for ($i=0; $i<count($project_questions); $i++)
    {
      $question_id = $project_questions[$i]["question_id"];
      $translator_id = $project_questions[$i]["account_id"];
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
      else if ($num_responses == 0 && $project_questions[$i]["status"] == "unread")
        $css_class = "highlight";

      // if we haven't already asked the database for information on this account, do so now
      if (!array_key_exists($account_id, $accounts))
        $accounts[$translator_id] = ot_get_account($translator_id);

      $translator_link = "<a href=\"../translators/edit.php?translator_id=$translator_id\">{$accounts[$translator_id]['first_name']} {$accounts[$translator_id]['last_name']}</a>";

      echo "
        <tr class='$css_class'>
          <td>$translator_link</td>
          <td>$subject</td>
          <td align='center'>$num_responses</td>
          <td align='center'>$status_str</td>
    			<td>$creation_date</td>
    			<td align='center'><a href='question_thread.php?question_id=$question_id'>VIEW</a></td>
        </tr>";
    }
    ?>
    </table>

  <?php } ?>

  <br />
  <h3>Translation Questions</h3>

  <?php if (count($translator_questions) == 0) { ?>

    <div class="notify"><span><span><span><span><span><span><span><span>
      Nobody has contacted you with any questions.
    </span></span></span></span></span></span></span></span></div>

  <?php } else { ?>

		<table class="info" width="100%" cellpadding="1" cellspacing="0">
		<tr>
			<th width="130">Translator</th>
			<th>Subject</th>
			<th>Num Responses</th>
			<th width="150">Date</th>
			<th width="70">VIEW</th>
		</tr>
    <?php
    $accounts = array();
    for ($i=0; $i<count($translator_questions); $i++)
    {
      $question_id = $translator_questions[$i]["question_id"];
      $data_id = $translator_questions[$i]["data_id"];
      $data_info = ot_get_data($data_id);
      $version_id = $data_info["version_id"];
      $translator_id = $translator_questions[$i]["account_id"];
      $subject = $translator_questions[$i]["subject"];
			$creation_date = ot_get_date("", $translator_questions[$i]["creation_date"], "M jS Y, g:i A");
      $num_responses = $translator_questions[$i]["num_responses"];
      $unread_responses = $translator_questions[$i]["unread_responses"];

      // we highlight the line as NEW if either there are new unread responses, or if the
      // question ITSELF is new
      $css_class = "";
      if ($unread_responses > 0)
      {
        $num_responses .= " (<b>$unread_responses</b> new)";
        $css_class = "highlight";
      }
      else if ($num_responses == 0 && $translator_questions[$i]["status"] == "unread")
        $css_class = "highlight";

      // if we haven't already asked the database for information on this account, do so now
      if (!array_key_exists($account_id, $accounts))
        $accounts[$translator_id] = ot_get_account($translator_id);

      $translator_link = "<a href=\"../translators/edit.php?translator_id=$translator_id\">{$accounts[$translator_id]['first_name']} {$accounts[$translator_id]['last_name']}</a>";

      echo "
        <tr class='$css_class'>
          <td>$translator_link</td>
          <td>$subject</td>
          <td align='center'>$num_responses</td>
    			<td>$creation_date</td>
    			<td align='center'><a href='../data/edit_data.php?data_id=$data_id&version_id=$version_id&tab=3'>VIEW</a></td>
        </tr>";
    }
    ?>
    </table>

    <br />

  <?php } ?>

  <div class="hr"></div>

  <p>
    <a href="../project.php">&lt;&lt; Back to Project</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
