<?php
require 'conn.php';

// Get NFC ID from the URL (this will be passed when the student badges)
$nfc_id = isset($_GET['nfc_id']) ? htmlspecialchars($_GET['nfc_id']) : null;

// Fetch all students from the users table
$eleves = [];
$sql = $pdo->prepare("SELECT * FROM users");
$sql->execute();
$eleves = $sql->fetchAll(PDO::FETCH_OBJ);

?>

<form method="post" style="margin-bottom:20px;">
  <button type="submit" name="refresh_tables">Refresh</button>
</form>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Interface Admin – NFC Présence</title>
  <style>
    body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
    input, button { padding: 8px 12px; margin: 5px; }
    button { cursor:pointer; }
    table { margin-top: 20px; border-collapse: collapse; width: 100%; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
    #log { margin-top: 10px; color: green; }
  </style>
</head>

<body>
  <h1>Interface Admin – Enregistrement NFC</h1>

  <!-- Button to open NFC Tools -->
  <button onclick="ouvrirNFCTools()">📲 Scanner via NFC Tools</button>

  <script>
    // Function to open NFC Tools app
    function ouvrirNFCTools() {
      window.location.href = 'nfc://scan/'; // Attempt to deep-link
      setTimeout(() => {
        alert("Si NFC Tools ne s'est pas ouvert automatiquement, ouvrez-le manuellement, scannez la carte puis revenez ici.");
      }, 1200); // Delay ≈ 1 second before message
    }
  </script>

  <hr>

  <!-- Add Student Form -->
  <form id="ajoutForm" method="post" action="">
    <label>Nom :
      <input type="text" name="nom" required>
    </label>
    <label>UID NFC :
      <input type="text" name="nfc_id" placeholder="Ex : 04A32C995B3780" required>
    </label>
    <label>Email :
      <input type="email" name="email" required>
    </label>
    <button type="submit" name="ajouter">Ajouter l'élève</button>
  </form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom']);
    $nfc_id = trim($_POST['nfc_id']);
    $email = trim($_POST['email']);

    if ($nom && $nfc_id && $email) {
        // Check if the NFC ID already exists
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE nfc_id = :nfc_id");
        $checkStmt->bindParam(':nfc_id', $nfc_id);
        $checkStmt->execute();
        $exists = $checkStmt->fetchColumn();
    
        if ($exists) {
            echo '<div id="log" style="color:red;">Erreur : cet UID NFC est déjà attribué à un élève.</div>';
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (nom, nfc_id, email, created_at) VALUES (:nom, :nfc_id, :email, NOW())");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':nfc_id', $nfc_id);
            $stmt->bindParam(':email', $email);
            if ($stmt->execute()) {
                echo '<div id="log">Élève ajouté avec succès.</div>';
            } else {
                echo '<div id="log" style="color:red;">Erreur lors de l\'ajout.</div>';
            }
        }
    } else {
        echo '<div id="log" style="color:red;">Tous les champs sont obligatoires.</div>';
    }
}
?>

  <h2>Liste des élèves</h2>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
  <table>
    <thead>
      <tr>
        <th>Nom</th>
        <th>UID</th>
        <th>Mail</th>
        <th>Date du Badgeage</th>
        <th>Présence</th>
      </tr>
    </thead>
    <tbody>
<?php
if (!empty($eleves)) {
  foreach ($eleves as $eleve) {
    // Fetch attendance data for each student
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE nfc_id = :nfc_id ORDER BY timestamp DESC LIMIT 1");
    $stmt->bindParam(':nfc_id', $eleve->nfc_id);
    $stmt->execute();
    $attendance = $stmt->fetch(PDO::FETCH_OBJ);
    $badge_time = $attendance ? $attendance->timestamp : 'Non renseigné';

    // Check if the student is present or absent
    $presence = ($attendance && $attendance->present) ? '✅' : '❌';

    echo "<tr>";
    echo "<td>" . htmlspecialchars($eleve->nom) . "</td>";
    echo "<td>" . htmlspecialchars($eleve->nfc_id) . "</td>";
    echo "<td>" . htmlspecialchars($eleve->email) . "</td>";
    echo "<td>" . htmlspecialchars($badge_time) . "</td>";
    echo "<td style='font-size:1.5em;text-align:center;'>$presence</td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='5' style='text-align:center;'>Aucun élève trouvé.</td></tr>";
}
?>
    </tbody>
  </table>
  </form>

  <div id="log"></div>

</body>
</html>
