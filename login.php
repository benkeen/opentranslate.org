<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_login.php");
?>
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>

  <script type="text/javascript">
  /* <![CDATA[ */
  var rules = [];
  rules.push("required,email,<?=$LANG['validation_login_no_email']?>");
  rules.push("required,password,<?=$LANG['validation_login_no_password']?>");
  /* ]]> */
  </script>

</head>
<body onload="document.login_form.email.focus()">

<?php
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Please Log In</h1>

  <p>
    All administrators, project managers and translators can use the form below to log in. You
    will be redirected to the appropriate page.
  </p>

  <form name="login_form" method="post" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return validateFields(this, rules)">

    <table cellpadding="1" cellspacing="0" summary="login table">
    <tr>
      <td width="90" class="medium_grey">Email</td>
      <td><input type="text" name="email" style="width:200px" value="<?=$page['email']?>" /></td>
    </tr>
    <tr>
      <td class="medium_grey">Password</td>
      <td>
        <table cellspacing="0" cellpadding="0" width="100%">
        <tr>
          <td><input type="password" name="password" style="width:100%" value="<?=$page['password']?>" /></td>
          <td width="63" align="right"><input type="submit" name="log_in" value="Log In" /></td>
        </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td> </td>
      <td></td>
    </tr>
    </table>

    <br />

    <?php
    if (isset($_GET["message"]))
    {
      echo "<div class='notify'><span><span><span><span><span><span><span><span>";
      switch ($_GET["message"])
      {
        case "session_timeout":
          echo "<p>Sorry, your session appears to have timed out. Please re-login in.</p>";
          break;
        case "permanent_error":
          echo "<p>There was a problem with your last request. The administrator has been notified. Please log in and try again.</p>";
          break;
      }
      echo "</span></span></span></span></span></span></span></span></div>";
    }
    ?>

    <?php ot_display_message($success, $message); ?>

  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>