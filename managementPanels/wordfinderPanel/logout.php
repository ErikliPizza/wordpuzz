<?php
 session_start();

  echo "Çıkış Yapılıyor.. ";
  unset($_SESSION["useradmin"]); // sessionu kaldır
  header("Location: login.php");
?>