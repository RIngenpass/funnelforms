<?php
/*
Plugin Name: FunnelForms
Description: Erstellt ein mehrstufiges Formular mit Fortschrittsanzeige.
Version: 1.0
Author: Rene Ingenpass
*/

if (!defined('ABSPATH')) exit;

// Module laden
require_once __DIR__ . '/includes/assets.php';
require_once __DIR__ . '/includes/admin-menu.php';
require_once __DIR__ . '/includes/shortcodes.php';
require_once __DIR__ . '/includes/settings-page.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/form-add.php';
require_once __DIR__ . '/includes/form-list.php';
require_once __DIR__ . '/includes/ajax-handler.php';



