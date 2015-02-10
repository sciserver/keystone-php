<?php
  function try_get_linked_user($keystone_id) {
    $conn = new PDO(DB_CONNECTION, DB_LOGIN, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT local_id FROM users WHERE keystone_id = '".$keystone_id."'";
    $stmt = $conn->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $conn = null;
    return $row['local_id'];
  }
  
  function set_linked_user($local_id, $keystone_id) {
    $conn = new PDO(DB_CONNECTION, DB_LOGIN, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "UPDATE users SET keystone_id = '".$keystone_id."' WHERE local_id = '".$local_id."'";
    $conn->exec($sql);
    $conn = null;
  }
  
  function remove_linked_user($local_id) {
    $conn = new PDO(DB_CONNECTION, DB_LOGIN, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "UPDATE users SET keystone_id = NULL WHERE local_id = '".$local_id."'";
    $conn->exec($sql);
    $conn = null;
  }
?>
