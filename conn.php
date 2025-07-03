<?php

$host = 'ep-steep-hill-ab9cwdl8-pooler.eu-west-2.aws.neon.tech';
$db   = 'Virtuotech-NFC';
$user = 'neondb_owner';
$pass = 'npg_3SWeJ0hzGsFT';
$sslmode = 'require';
$options = 'options=endpoint=ep-steep-hill-ab9cwdl8';

$dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=$sslmode;$options";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);

  echo "<h2>✅ Connection to PostgreSQL successful!</h2>";

  // Optional test
  $stmt = $pdo->query("SELECT NOW()");
  $time = $stmt->fetchColumn();
  echo "<p>🕒 Server time: $time</p>";

} catch (PDOException $e) {
  echo "<h2>❌ Connection failed:</h2>";
  echo "<pre>" . $e->getMessage() . "</pre>";
  exit;
}

// function showTables($pdo) {
//   $sql = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
//   $tables = $sql->fetchAll(PDO::FETCH_OBJ);
//   echo "<h3>Tables in database:</h3><ul>";
//   foreach ($tables as $table) {
//     echo "<li><strong>{$table->table_name}</strong><ul>";
//     // Fetch columns and data types for the current table
//     $cols = $pdo->query("
//       SELECT column_name, data_type 
//       FROM information_schema.columns 
//       WHERE table_name = '{$table->table_name}' AND table_schema = 'public'
//       ORDER BY ordinal_position
//     ")->fetchAll(PDO::FETCH_ASSOC);
//     if (count($cols) > 0) {
//       echo "<li><em>Columns:</em><ul>";
//       foreach ($cols as $col) {
//         echo "<li>{$col['column_name']} <small>({$col['data_type']})</small></li>";
//       }
//       echo "</ul></li>";
//     } else {
//       echo "<li><em>No columns found</em></li>";
//     }
//     // Fetch all rows from the current table
//     $rows = $pdo->query("SELECT * FROM \"{$table->table_name}\"")->fetchAll(PDO::FETCH_ASSOC);
//     if (count($rows) > 0) {
//       foreach ($rows as $row) {
//         echo "<li><pre>" . htmlspecialchars(print_r($row, true)) . "</pre></li>";
//       }
//     } else {
//       echo "<li><em>No data</em></li>";
//     }
//     echo "</ul></li>";
//   }
//   echo "</ul>";
// }

// // Handle refresh or initial load
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['refresh_tables'])) {
//   showTables($pdo);
// } else {
//   showTables($pdo);
// }

// Example insert code (commented out)
/*
// $nfc_id = '1234567890ABCDEF';
// $nom = 'Doe';
// $prenom = 'John';
// $email = 'john.doe@example.com';
// $telephone = '0123456789';
// $is_active = true;
// $webauthn_key = 'sample_webauthn_key';
// $created_at = date('Y-m-d H:i:s');

// $sql = "INSERT INTO \"users\" (nfc_id, nom, prenom, email, telephone, is_active, webauthn_key, created_at)
//     VALUES (:nfc_id, :nom, :prenom, :email, :telephone, :is_active, :webauthn_key, :created_at)";
// $stmt = $pdo->prepare($sql);
// $stmt->execute([
//     ':nfc_id' => $nfc_id,
//     ':nom' => $nom,
//     ':prenom' => $prenom,
//     ':email' => $email,
//     ':telephone' => $telephone,
//     ':is_active' => $is_active,
//     ':webauthn_key' => $webauthn_key,
//     ':created_at' => $created_at
// ]);

// echo "<p>✅ User inserted successfully.</p>";
*/
?>
