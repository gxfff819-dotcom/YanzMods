<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// IMAP extension check
if (!extension_loaded('imap')) {
    die('IMAP extension tidak terinstall. Install dulu: sudo apt-get install php-imap');
}
?>