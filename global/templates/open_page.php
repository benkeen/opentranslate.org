<div id="container">

  <div id="main_logo"> </div>

  <div id="left_nav">
    <?php
    if (isset($_SESSION["ot"]["account_type"]))
    {
      switch ($_SESSION["ot"]["account_type"])
      {
        case "translator":
        	echo "<div id=\"left_nav_title\">{$LANG["word_translator"]}</div>";
          break;
        case "admin":
          echo "<div id=\"left_nav_title\">{$LANG["word_administrator"]}</div>";
          break;
      }
    }
    require("$g_root_dir/global/nav/nav.php");
    ?>
  </div>

  <div id="content">

    <?php
    // if it's included, display the breadcrumb navigation
    if (isset($g_breadcrumb) && !empty($g_breadcrumb))
    {
    	echo "<div id=\"breadcrumb_row\">";

      $crumbs = array();
      foreach ($g_breadcrumb as $crumb)
      {
        $title = $crumb[0];
        $link  = $crumb[1];

        if (!empty($link))
          $crumbs[] = "<a href='$link' class='breadcrumb'>$title</a>";
        else
          $crumbs[] = "<span class='breadcrumb'>$title</span>";
      }

      echo join(" <span class='breadcrumb'>&gt;</span> ", $crumbs);

      echo "</div>";
    }
    ?>
