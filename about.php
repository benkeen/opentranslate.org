<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("global/library.php");
?>
<html>
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>About</h1>

  <p>
    Open Translate provides a free service for open source projects, non-profit groups and
    NGOs. It provides a simple interface for managing translations of any text
    document, allowing project managers to input text, specify which languages the text should be
    translated to, create separate user accounts for translators, export the translated data in
    a variety of formats - and all other aspects of the translation process.
  </p>

  <p>
    The age-old problem with having anything translated is that you doesn't always know when a translation
    is <em>good</em> - not unless you speak the language it's being translated into. With projects that
    require translations into many languages the likelihood that you speak every one becomes increasingly
    unrealistic: just how many languages do you speak?
  </p>

  <p>
    Open Translate solves this problem. It employs a review-based system to authenticate each translation.
    Each translation must reach a "review threshold" before it's approved; multiple translators must review
    each translation and mark it as approved before the translation is completed and closed.
  </p>

  <p>
    Projects can be either public or private. Open Source projects would generally be set to public: anyone
    could sign up to help translate (whether or not a person may join the project is either through a configuration
    setting: automatic or explicit approval by a Project Manager). Companies which have their own in-house
    translators would set their projects to "private": the content wouldn't be visible to anyone but themselves,
    and only their own translators would be able to work on them.
  </p>

  <p>
    Behind the scenes, statistics are compiled for each translator. This will be valuable for companies
    needing to gauge the experience and quality of a translator's work. For example, if a company set up a
    new project and needed translations from English to German, they could review a list of all English-German translators
    and contact the people with the highest translation reliability, number of translations and reviews.
    Translation reliability is calculated by reviews made by the translator's peers.
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
