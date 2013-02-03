<?php
session_start();
header("Cache-control: private");

require("global/library.php");

// if this is a translator logging out, update their total stats
if (isset($_SESSION["ot"]) && isset($_SESSION["ot"]["account_type"]) &&
   $_SESSION["ot"]["account_type"] == "translator" && isset($_SESSION["ot"]["account_id"]))
  ot_update_total_translators_stats($_SESSION["ot"]["account_id"]);

// empty sessions
session_unset();

// redirect to site index page
header("location: $g_root_url/");
