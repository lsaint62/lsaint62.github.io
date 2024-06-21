<?php
session_start();
include 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: test_connexion.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les réservations pour les annonces de l'utilisateur
$sql = "SELECT r.*, u.username, u.phone, a.destination, a.date_depart, a.date_arrivee
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN annonces a ON r.annonce_id = a.id
        WHERE a.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$reservations = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des réservations</title>
    <link rel="stylesheet" href="style_test.css">
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
                <li><a href="profil.php">Profil</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Vos réservations</h1>
        <ul>
            <?php while ($reservation = $reservations->fetch_assoc()): ?>
                <li>
                    <strong>Expéditeur:</strong> <?php echo htmlspecialchars($reservation['username']); ?><br>
                    <strong>Téléphone:</strong> <?php echo htmlspecialchars($reservation['phone']); ?><br>
                    <strong>Destination:</strong> <?php echo htmlspecialchars($reservation['destination']); ?><br>
                    <strong>Date de départ:</strong> <?php echo htmlspecialchars($reservation['date_depart']); ?><br>
                    <strong>Date d'arrivée:</strong> <?php echo htmlspecialchars($reservation['date_arrivee']); ?><br>
                    <strong>Poids réservé:</strong> <?php echo htmlspecialchars($reservation['kilos']); ?> kg<br>
                    <strong>Statut:</strong> <?php echo htmlspecialchars($reservation['status']); ?><br>
                    <form action="valider_reservation.php" method="post">
                        <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation['id']); ?>">
                        <button type="submit" name="action" value="approve">Approuver</button>
                        <button type="submit" name="action" value="reject">Rejeter</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    </main>

    <footer>
        <p>&copy; 2024 ParticPost</p>
        <li><a href="#">Comment ça marche ?</a></li>
        <li><a href="#">Qui sommes-nous?</a></li>
        <li><a href="#">FAQ</a></li>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
