<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: test_connexion.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Récupérer les réservations faites par l'utilisateur
$sql = "SELECT r.*, a.destination, a.date_depart, a.date_arrivee, u.username AS poseur_username, u.email AS poseur_email, u.phone AS poseur_phone
        FROM reservations r 
        JOIN annonces a ON r.annonce_id = a.id 
        JOIN users u ON a.user_id = u.id
        WHERE r.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$reservations = $stmt->get_result();

// Récupérer les annonces postées par l'utilisateur
$sql = "SELECT * FROM annonces WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$annonces = $stmt->get_result();

// Récupérer les réservations en attente de validation pour les annonces de l'utilisateur
$sql = "SELECT r.*, u.username AS reserver_username, a.destination, a.date_depart, a.date_arrivee 
        FROM reservations r 
        JOIN annonces a ON r.annonce_id = a.id 
        JOIN users u ON r.user_id = u.id 
        WHERE a.user_id = ? AND r.status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$pending_reservations = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil utilisateur</title>
    <link rel="stylesheet" href="style_profil.css">
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
        <?php
        if (isset($_SESSION['reservation_message'])) {
            echo "<p>" . htmlspecialchars($_SESSION['reservation_message']) . "</p>";
            unset($_SESSION['reservation_message']); // Remove the message after displaying it
        }
        ?>

        <h1>Profil de <?php echo htmlspecialchars($user['username']); ?></h1>

        <section>
            <h2>Réservations faites</h2>
            <ul>
                <?php while ($reservation = $reservations->fetch_assoc()): ?>
                    <li>
                        <strong>Destination:</strong> <?php echo htmlspecialchars($reservation['destination']); ?><br>
                        <strong>Date de départ:</strong> <?php echo htmlspecialchars($reservation['date_depart']); ?><br>
                        <strong>Date d'arrivée:</strong> <?php echo htmlspecialchars($reservation['date_arrivee']); ?><br>
                        <strong>Poids réservé:</strong> <?php echo htmlspecialchars($reservation['kilos']); ?> kg<br>
                        <strong>Status:</strong> <?php echo htmlspecialchars($reservation['status']); ?><br>
                        <button onclick="showContactDetails('<?php echo htmlspecialchars(json_encode([
                            'username' => $reservation['poseur_username'],
                            'email' => $reservation['poseur_email'],
                            'phone' => $reservation['poseur_phone']
                        ])); ?>')">Contacter</button>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>

        <section>
            <h2>Annonces postées</h2>
            <ul>
                <?php while ($annonce = $annonces->fetch_assoc()): ?>
                    <li>
                        <strong>Destination:</strong> <?php echo htmlspecialchars($annonce['destination']); ?><br>
                        <strong>Date de départ:</strong> <?php echo htmlspecialchars($annonce['date_depart']); ?><br>
                        <strong>Date d'arrivée:</strong> <?php echo htmlspecialchars($annonce['date_arrivee']); ?><br>
                        <strong>Poids disponible:</strong> <?php echo htmlspecialchars($annonce['poids_disponible']); ?> kg<br>
                        <strong>Prix par kilo:</strong> <?php echo htmlspecialchars($annonce['prix_kilo']); ?> €<br>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>

        <section>
            <h2>Réservations en attente de validation</h2>
            <ul>
                <?php while ($reservation = $pending_reservations->fetch_assoc()): ?>
                    <li>
                        <strong>Annonce vers:</strong> <?php echo htmlspecialchars($reservation['destination']); ?><br>
                        <strong>Date de départ:</strong> <?php echo htmlspecialchars($reservation['date_depart']); ?><br>
                        <strong>Date d'arrivée:</strong> <?php echo htmlspecialchars($reservation['date_arrivee']); ?><br>
                        <strong>Poids réservé:</strong> <?php echo htmlspecialchars($reservation['kilos']); ?> kg<br>
                        <strong>Réservé par:</strong> <?php echo htmlspecialchars($reservation['reserver_username']); ?><br>
                        <form action="validate_reservation.php" method="post">
                            <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation['id']); ?>">
                            <button class="approve" type="submit" name="action" value="approve">Approuver</button>
                            <button class="reject" type="submit" name="action" value="reject">Rejeter</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 ParticPost</p>
        <li><a href="#">Comment ça marche ?</a></li>
        <li><a href="#">Qui sommes-nous?</a></li>
        <li><a href="#">FAQ</a></li>
    </footer>

    <script>
        function showContactDetails(details) {
            const contact = JSON.parse(details);
            alert(`Nom d'utilisateur: ${contact.username}\nEmail: ${contact.email}\nTéléphone: ${contact.phone}`);
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>

