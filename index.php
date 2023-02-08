<?php
include('./config/config.inc.php');
include('./include/scrapper.php');
// run_scrap_localhost();

header('Content-Type: text/plain; charset=utf-8');
run_scrap_voa();
run_scrap_ebc();
run_scrap_fanabc();