<?php

add_action('wp_enqueue_scripts', 'amh_nj_load_script_chapmaster');
function amh_nj_load_script_chapmaster() {
    wp_enqueue_script( "amhnj-chapmaster-script", AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_URL . "assets/js/script.js?ver=" . VERSION, VERSION );
    wp_enqueue_style( "amhnj-chapmaster-style", AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_URL . "assets/css/style.css?ver=" . VERSION, array(), VERSION );
}