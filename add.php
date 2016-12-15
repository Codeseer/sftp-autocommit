<link rel="stylesheet" href="https://unpkg.com/purecss@0.6.0/build/pure-min.css">

<?php
  //GET show new site form.
  //POST create new site with SFTP details
  $db = new SQLite3('sites.db');
  $db->query('CREATE TABLE IF NOT EXISTS "sites" (
      "id" integer PRIMARY KEY AUTOINCREMENT NOT NULL,
      "host" varchar(1024),
      "port" varchar(1024),
      "user" varchar(1024),
      "password" varchar(1024),
      "remote_path" varchar(1024)
    );');

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(isset($_GET['id'])) {
      $id = $_GET['id'];
      $query = "SELECT * FROM sites WHERE id=$id LIMIT 1";
      $site = $db->query($query)->fetchArray();
      if($site) {
        $host = $site['host'];
        $port = $site['port'];
        $user = $site['user'];
        $password = $site['password'];
        $remote_path = $site['remote_path'];
      } else {
        $id = 'create';
      }
    }
    include('site_form.php');
  }elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'];
    $port = $_POST['port'];
    $user = $_POST['user'];
    $password = $_POST['password'];
    $remote_path = $_POST['remote_path'];
    $id = $_POST['id'];
    if($id == 'create') {
      $query = "INSERT INTO sites (host,port,user,password,remote_path) VALUES ('$host', '$port', '$user', '$password', '$remote_path');";
    } else {
      $query = "UPDATE sites SET host='$host', port='$port', user='$user', password='$password', remote_path='$remote_path' WHERE id=$id;";
    }
    $results = $db->query($query);
    require_once 'redirect.php';
    redirect('/list.php');
  }
 ?>
