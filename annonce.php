<?php
session_start();
include 'db.php';

// Récupérer l'ID de l'annonce depuis l'URL
$annonce_id = $_GET['id'] ?? null;

if ($annonce_id === null) {
    echo "Annonce non trouvée.";
    exit();
}

// Récupérer les détails de l'annonce
$sql = "SELECT a.*, u.username FROM annonces a JOIN users u ON a.user_id = u.id WHERE a.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $annonce_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Annonce non trouvée.";
    exit();
}

$annonce = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'annonce</title>
    <link rel="stylesheet" href="style_annonce.css">
</head>
<body>
<header>
    <a href="test.html">
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
        <div class="hamburger" id="hamburger">
            &#9776;
        </div>
    </nav>
</header>

<main>
    <h1>Détails de l'annonce</h1>
    <div class="details">
        <p><strong>Utilisateur:</strong> <?php echo htmlspecialchars($annonce['username']); ?></p>
        <p><strong>Origine:</strong> <?php echo htmlspecialchars($annonce['origine']); ?></p>
        <p><strong>Destination:</strong> <?php echo htmlspecialchars($annonce['destination']); ?></p>
        <p><strong>Date de départ:</strong> <?php echo htmlspecialchars($annonce['date_depart']); ?></p>
        <p><strong>Date d'arrivée:</strong> <?php echo htmlspecialchars($annonce['date_arrivee']); ?></p>
        <p><strong>Poids disponible:</strong> <?php echo htmlspecialchars($annonce['poids_disponible']); ?> kg</p>
        <p><strong>Prix par kilo:</strong> <?php echo htmlspecialchars($annonce['prix_kilo']); ?> €</p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="reserver.php" method="post">
                <input type="hidden" name="annonce_id" value="<?php echo $annonce_id; ?>">
                <label for="kilos">Nombre de kilos à réserver:</label>
                <input type="number" id="kilos" name="kilos" min="1" max="<?php echo htmlspecialchars($annonce['poids_disponible']); ?>" required>
                <button type="submit">Réserver</button>
            </form>
        <?php else: ?>
            <p>Vous devez <a href="test_connexion.html">vous connecter</a> ou <a href="inscription.html">vous inscrire</a> pour réserver.</p>
        <?php endif; ?>
    </div>
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

<?php
$conn->close();
?>
