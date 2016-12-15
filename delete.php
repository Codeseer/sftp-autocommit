<?php
  require_once 'redirect.php';
  $db = new SQLite3('sites.db');
  $id = $_GET['id'];
  $query = "DELETE FROM sites WHERE id=$id";
  $results = $db->query($query);
  redirect("/list.php");
 ?>
