<?php
session_start();
/*echo '<pre>';
print_r($_SESSION);
echo '</pre>';*/
session_unset();
session_destroy();
echo "done ";
header("Location: index.php"); // ✅ no redirect loop
exit();
//echo "done ";
?>
