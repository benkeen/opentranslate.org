<?php

$folder = dirname(__FILE__);

// opentranslate.org
$g_base_dir = "/Applications/MAMP/htdocs/";
$g_ot_root_url = "http://localhost:8888/opentranslate.org";
$g_ot_root_dir = "$g_base_dir/opentranslate.org";
$g_ot_login_url = "$g_ot_root_url/login.php";
$g_ot_db_hostname = "localhost";
$g_ot_db_name     = "opentranslate";
$g_ot_db_username = "root";
$g_ot_db_password = "root";

$g_root_dir = $g_ot_root_dir;
$g_root_url = $g_ot_root_url;
$g_login_url = "$g_ot_root_url/login.php";

$g_max_nav_pages = 20;
$g_session_timeout = 900; // 15 minutes

$g_default_error_reporting = 2047;
error_reporting($g_default_error_reporting);


// used for all page messages, errors or notifications
$success = "";
$message = "";

$g_PHRASE_SIZE = 6;
$g_SENTENCE_SIZE = 20;
$g_PARAGRAPH_SIZE = 100;
$g_LOCK_TIME = 600; // how long a data translation is locked for once a translator selects it

$g_max_num_project_questions_per_page = 10;
$g_max_num_data_questions_per_page = 10;
$g_max_num_translators_per_page = 10;
$g_google_translator_id = 2; // remember to add to live site!

require_once("$g_root_dir/global/code/account_settings.php");
require_once("$g_root_dir/global/code/accounts.php");
require_once("$g_root_dir/global/code/categories.php");
require_once("$g_root_dir/global/code/data.php");
require_once("$g_root_dir/global/code/data_questions.php");
require_once("$g_root_dir/global/code/errors.php");
require_once("$g_root_dir/global/code/export.php");
require_once("$g_root_dir/global/code/general.php");
require_once("$g_root_dir/global/code/import.php");
require_once("$g_root_dir/global/code/languages.php");
require_once("$g_root_dir/global/code/news.php");
require_once("$g_root_dir/global/code/projects.php");
require_once("$g_root_dir/global/code/project_managers.php");
require_once("$g_root_dir/global/code/questions.php");
require_once("$g_root_dir/global/code/reports.php");
require_once("$g_root_dir/global/code/sessions.php");
require_once("$g_root_dir/global/code/statistics.php");
require_once("$g_root_dir/global/code/translations.php");
require_once("$g_root_dir/global/code/translators.php");
require_once("$g_root_dir/global/code/validation.php");
require_once("$g_root_dir/global/code/versions.php");
require_once("$g_root_dir/global/code/zip.php");
require_once("$g_root_dir/global/code/pclzip.lib.php");

// Google Translate wrapper
require_once("$g_root_dir/global/gTranslate/GTranslate.php");


// import the appropriate language file (stored in cookie + session)
$default_language = "en_ca.php";
require_once("$g_root_dir/global/lang/core/en_ca.php");

$link = ot_db_connect($g_ot_db_hostname, $g_ot_db_username, $g_ot_db_password, $g_ot_db_name);

// for use in each of the pages
$request = array_merge($_POST, $_GET);