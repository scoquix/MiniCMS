<?php
session_start();
?>
<HTML>
<HEAD>
  <TITLE>Wylogowanie</TITLE>
</HEAD>
<BODY>
<?php
  echo "U�ytkownik " . $_SESSION["login"];
  echo " zosta� wylogowany.";
  session_destroy();
  header("Location: logowanie.php");

?>
</BODY>
</HTML>
