<?php
// inscription.php

session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $username, $email, $phone, $password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['username'] = $username;
        header("Location: expedie.php");
        exit;
    } else {
        $error = "Erreur lors de l'inscription : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
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
                <li><a href="#">Je transporte</a></li>
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
        <h1>Inscription</h1>
        <?php if (!empty($error)): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="inscription.php" method="post">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="phone">Téléphone:</label>
            <input type="text" id="phone" name="phone" required>
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
            <label for="confirm_password">Confirmez le mot de passe:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <button type="submit">Inscription</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 ParticPost</p>
    </footer>
</body>
</html>
