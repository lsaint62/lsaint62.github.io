<?php
// traitement_connexion.php
session_start();
include 'db.php';

// Initialisation de la variable pour le message d'erreur
$message = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Préparer la requête pour vérifier les identifiants
    $sql = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header('Location: profil.php');
            exit();
        } else {
            $message = "Mot de passe incorrect.";
        }
    } else {
        $message = "Utilisateur non trouvé.";
    }

    $stmt->close();
}

$conn->close();
?>

<!-- Insérez le formulaire HTML ici -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Transportez vos colis avec des voyageurs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <a href="test.html">
         <img src="image.jpg" alt="Logo du site web" class="logo">
        </a>
        <nav>
            <ul class="centered">
                <li><a href="expedie.php">J'expédie</a></li>
                <li><a href="annonce_form.php">Je transporte</a></li>
                <li><a href="test_connexion.html">Connexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Connexion</h1>
        <?php if (!empty($message)) : ?>
        <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="traitement_connexion.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
        <p>Vous n'avez pas encore de compte ? <a href="inscription.html">Inscrivez-vous !</a></p>
    </main>

    <footer>
        <p>&copy; 2024 ParticPost</p>
        <ul>
            <li><a href="#">Comment ça marche ?</a></li>
            <li><a href="#">Qui sommes-nous?</a></li>
            <li><a href="#">FAQ</a></li>
        </ul>
    </footer>
</body>
</html>
