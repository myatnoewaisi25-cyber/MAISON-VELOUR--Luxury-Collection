<?php  
// ═══════════════════════════════════════  
//  MAISON VELOUR — Logout Handler  
// ═══════════════════════════════════════  
session_start();  
  
// Clear all session data  
$_SESSION = [];  
  
// Delete "Remember me" cookie if it exists  
if (isset($_COOKIE['mv_remember'])) {  
    setcookie('mv_remember', '', time() - 3600, '/');  
}  
  
// Destroy the session  
session_destroy();  
  
// Redirect to login page  
header('Location: login.php');  
exit;  
?>