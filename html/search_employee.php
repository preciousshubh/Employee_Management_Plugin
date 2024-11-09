<?php
if (!isset($_POST['nonce'])) {
    echo 0;
    wp_die();
} else {
    echo 1;
    wp_die();
}
