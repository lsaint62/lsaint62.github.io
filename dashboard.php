<?php
// tableau_de_bord.php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: test_connexion.html');
    exit();
}

// Connexion à la base de données
include 'db.php';

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="style_test.css">
</head>
<body>
    <header>
        <img src="logo.png" alt="Logo du site web" class="logo">
        <nav>
            <ul>
                <li><a href="expedie.php">J'expédie</a></li>
                <li><a href="annonce_form.php">Je transporte</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Bienvenue, <?php echo htmlspecialchars($user['username']); ?></h1>
        <p>Voici votre tableau de bord.</p>
    </main>

    <footer>
        <p>&copy; 2024 ParticPost</p>
        <li><a href="#">Comment ça marche ?</a></li>
        <li><a href="#">Qui sommes-nous?</a></li>
        <li><a href="#">FAQ</a></li>
    </footer>
</body>
</html>
