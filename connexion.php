<?php
// connexion.php

session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirection vers la page de réservation si une annonce est spécifiée
            if (isset($_SESSION['redirect_to_annonce_id'])) {
                $annonce_id = $_SESSION['redirect_to_annonce_id'];
                unset($_SESSION['redirect_to_annonce_id']);
                header("Location: annonce.php?id=$annonce_id");
            } else {
                header("Location: expedie.php");
            }
            exit();
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Utilisateur non trouvé.";
    }
    $stmt->close();
}
$conn->close();
?>
