<?php
    // Autoload app
    require_once('app/classes/autoload.php');

    // Base URL
    $url = ( isset( $_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $app_name = _("Nate's simple Form Builder (PHP / JS)");
    $app_desc = _('');
