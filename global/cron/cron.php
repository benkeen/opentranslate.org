<?php

require("../library.php");

// this page is called run hourly
ot_update_project_statistics(10);
ot_send_project_via_ftp(10);

// update the project stats (for projects updated in last 15 mins)
ot_update_all_projects_percent_translated();
ot_update_all_projects_percent_reliable();

