<?php
session_start();
include 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: test_connexion.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $origine = $_POST['origine'];
    $destination = $_POST['destination'];
    $date_depart = $_POST['date_depart'];
    $date_arrivee = $_POST['date_arrivee'];
    $poids_disponible = $_POST['poids_disponible'];
    $prix_kilo = $_POST['prix_kilo'];

    // Insérer l'annonce dans la base de données
    $sql = "INSERT INTO annonces (user_id, origine, destination, date_depart, date_arrivee, poids_disponible, prix_kilo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssii', $user_id, $origine, $destination, $date_depart, $date_arrivee, $poids_disponible, $prix_kilo);

    if ($stmt->execute()) {
        echo "Votre annonce a été publiée avec succès.";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier une annonce - Je transporte</title>
    <link rel="stylesheet" href="style_annonce_form.css">
</head>
<body>
    <header>
        <a href="index.php">
            <img src="image.jpg" alt="Logo du site web" class="logo">
        </a>
        <nav>
            <ul>
                <li><a href="expedie.php">J'expédie</a></li>
                <li><a href="annonce_form.php">Je transporte</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="test_connexion.html">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Publier une annonce</h1>
        <form action="annonce_form.php" method="post">
            <label for="origine">Origine:</label>
            <input type="text" id="origine" name="origine" required>

            <label for="destination">Destination:</label>
            <input type="text" id="destination" name="destination" required>

            <label for="date_depart">Date de départ:</label>
            <input type="date" id="date_depart" name="date_depart" required>

            <label for="date_arrivee">Date d'arrivée:</label>
            <input type="date" id="date_arrivee" name="date_arrivee" required>

            <label for="poids_disponible">Poids disponible (kg):</label>
            <input type="number" id="poids_disponible" name="poids_disponible" required>

            <label for="prix_kilo">Prix par kilo (€):</label>
            <input type="number" step="0.01" id="prix_kilo" name="prix_kilo" required>

            <button type="submit">Publier l'annonce</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 ParticPost</p>
        <ul>
            <li><a href="#">Comment ça marche ?</a></li>
            <li><a href="#">Qui sommes-nous?</a></li>
            <li><a href="#">FAQ</a></li>
        </ul>
    </footer>

    <script>
        document.getElementById("hamburger").onclick = function() {
            document.querySelector("nav ul").classList.toggle("active");
        };
    </script>
</body>
</html>
