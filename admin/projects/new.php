<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_new.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["label_new_project"];
  require("$g_root_dir/global/header_code.php");
  ?>

  <script type="text/javascript">
  /* <![CDATA[ */
  var rules = new Array();
  rules.push("required,project_name,<?=$LANG['validation_project_name']?>");
  rules.push("required,user_type,<?=$LANG['validation_project_manager_type']?>");
  rules.push("if:user_type=new_user,required,project_leader_first_name,<?=$LANG['validation_project_leader_first_name']?>");
  rules.push("if:user_type=new_user,required,project_leader_last_name,<?=$LANG['validation_project_leader_last_name']?>");
  rules.push("if:user_type=new_user,required,project_leader_email,<?=$LANG['validation_project_leader_email']?>");
  rules.push("if:user_type=existing_user,required,existing_user_id,<?=$LANG['validation_select_project_manager']?>");
  /* ]]> */
  </script>

</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "./"),
                  array($LANG["word_projects"], "index.php"),
                  array($LANG["label_new_project"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_new_project"]?></h1>

  <p><?=$LANG["text_new_project_summary"]?></p>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="return validateFields(this, rules)">

  <table>
  <tr>
    <td width="110"><?=$LANG["label_project_name"]?></td>
    <td><input type="text" name="project_name" value="" style="width: 300px;" /></td>
  </tr>
  <tr>
    <td valign="top"><?=$LANG["label_project_manager"]?></td>
    <td>

      <table>
      <tr>
        <td valign="top" width="120">
          <input type="radio" name="user_type" id="user_type1" value="new_user" checked />
          <label for="user_type1"><?=$LANG["label_new_user"]?></label>
        </td>
        <td>

          <table cellpadding="0" cellspacing="0">
          <tr>
            <td width="90"><?=$LANG["label_first_name"]?></td>
            <td><input type="text" name="project_leader_first_name" value="" /></td>
          </tr>
          <tr>
            <td><?=$LANG["label_last_name"]?></td>
            <td><input type="text" name="project_leader_last_name" value="" /></td>
          </tr>
          <tr>
            <td><?=$LANG["word_email"]?></td>
            <td><input type="text" name="project_leader_email" value="" /></td>
          </tr>
          </table>

        </td>
      </tr>
      <tr>
        <td>
          <input type="radio" name="user_type" id="user_type2" value="existing_user" />
          <label for="user_type2"><?=$LANG["label_existing_project_manager"]?></label>
        </td>
        <td>
          <select name="existing_user_id">
            <option value=""><?=$LANG["label_please_select"]?></option>
            <?php
            foreach ($project_managers as $project_manager)
            {
              $account_id = $project_manager["account_id"];
              $name = $project_manager["first_name"] . " " . $project_manager["last_name"];

              if ($project_manager["account_type"] == "admin")
                $name .= " (admin)";

              echo "<option value='$account_id'>$name</option>\n";
            }
            ?>
          </select>
        </td>
      </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td><?=$LANG["label_project_visibility"]?></td>
    <td>
      <select name="project_visibility">
        <option value="public"><?=$LANG["word_public"]?></option>
        <option value="private"><?=$LANG["word_private"]?></option>
      </select>
    </td>
  </tr>
  <tr>
    <td><?=$LANG["label_origin_language"]?></td>
    <td>
      <select name="origin_language_id">
        <option value=""><?=$LANG["label_please_select"]?></option>
        <?php
        foreach ($languages as $language)
          echo "<option value='{$language['language_id']}'>{$language['language_name']}</option>\n";
        ?>
      </select>
    </td>
  </tr>
  </table>

  <p>
    <input type="submit" name="add_project" value="<?=$LANG['label_create_project']?>" />
  </p>

  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>