<?php
/**
 * Logout - Redirects to index (no login required)
 */

// Auto-redirect to index page - no logout needed
header('Location: index.php');
exit;
