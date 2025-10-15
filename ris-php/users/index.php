<?php
/**
 * Users Directory Redirect
 * Redirects old URL structure to new routing system
 */

// Redirect to the new routing structure
header('Location: ../index.php?route=users/list');
exit;
