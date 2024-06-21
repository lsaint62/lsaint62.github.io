<?php
session_start();
include 'db.php';

// Récupérer les données du formulaire de recherche
$origine = $_GET['origine'] ?? '';
$destination = $_GET['destination'] ?? '';
$date_depart = $_GET['date_depart'] ?? '';
$date_arrivee = $_GET['date_arrivee'] ?? '';

// Préparer la requête SQL avec les filtres
$sql = "SELECT a.*, u.username FROM annonces a JOIN users u ON a.user_id = u.id WHERE 1=1";

if (!empty($origine)) {
    $sql .= " AND a.origine LIKE '%" . $conn->real_escape_string($origine) . "%'";
}

if (!empty($destination)) {
    $sql .= " AND a.destination LIKE '%" . $conn->real_escape_string($destination) . "%'";
}

if (!empty($date_depart)) {
    $sql .= " AND a.date_depart >= '" . $conn->real_escape_string($date_depart) . "'";
}

if (!empty($date_arrivee)) {
    $sql .= " AND a.date_arrivee <= '" . $conn->real_escape_string($date_arrivee) . "'";
}

$sql .= " ORDER BY a.created_at DESC";

$result = $conn->query($sql);

// Afficher les annonces
echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J\'expédie - Transportez vos colis avec des voyageurs</title>
    <link rel="stylesheet" href="style_test1.css">
</head>
<body>
<header>
<a href="test.html">
    <img src="image.jpg" alt="Logo du site web" class="logo">
</a>
<button class="menu-toggle" aria-label="Ouvrir le menu">&#9776;</button>
<nav id="nav-menu">
    <ul>
        <li><a href="expedie.php">J\'expédie</a></li>
        <li><a href="annonce_form.php">Je transporte</a></li>';
        if (isset($_SESSION['user_id'])) {
            echo '<li><a href="profil.php">Profil</a></li>
            <li><a href="logout.php">Déconnexion</a></li>';
        } else {
            echo '<li><a href="test_connexion.html">Connexion</a></li>';
        }
echo '</ul>
</nav>
</header>

<main>
    <h1>Liste des annonces</h1>
    <section class="recherche">
        <h2>Filtrer les annonces</h2>
        <form action="expedie.php" method="get">
            <label for="origine">Origine:</label>
            <input type="text" id="origine" name="origine" placeholder="Entrez votre ville">

            <label for="destination">Destination:</label>
            <input type="text" id="destination" name="destination" placeholder="Entrez votre ville">

            <label for="date_depart">Date de départ:</label>
            <input type="date" id="date_depart" name="date_depart">

            <label for="date_arrivee">Date d\'arrivée:</label>
            <input type="date" id="date_arrivee" name="date_arrivee">

            <button type="submit">Rechercher</button>
        </form>
    </section>

    <section class="annonces">
        <h2>Dernières annonces</h2>
        <ul id="annonces-list">';
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<li>";
                    echo "<a href='annonce.php?id=" . htmlspecialchars($row['id']) . "'>";
                    echo "<strong>" . htmlspecialchars($row['username']) . "</strong> propose " . htmlspecialchars($row['poids_disponible']) . " kg pour " . htmlspecialchars($row['prix_kilo']) . " €/kg";
                    echo " de " . htmlspecialchars($row['origine']) . " à " . htmlspecialchars($row['destination']);
                    echo " du " . htmlspecialchars($row['date_depart']) . " au " . htmlspecialchars($row['date_arrivee']);
                    echo "</a>";
                    echo "</li>";
                }
            } else {
                echo "<li>Aucune annonce trouvée.</li>";
            }
echo '      </ul>
    </section>
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
document.addEventListener("DOMContentLoaded", function() {
    const menuToggle = document.querySelector(".menu-toggle");
    const navMenu = document.getElementById("nav-menu");

    menuToggle.addEventListener("click", function() {
        navMenu.classList.toggle("show");
    });
});
</script>

</body>
</html>';

$conn->close();
?>
