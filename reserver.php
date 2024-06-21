<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: test_connexion.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $annonce_id = $_POST['annonce_id'];
    $user_id = $_SESSION['user_id'];
    $kilos = $_POST['kilos'];

    // Insérer la réservation avec statut 'pending'
    $sql = "INSERT INTO reservations (user_id, annonce_id, kilos, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $user_id, $annonce_id, $kilos);

    if ($stmt->execute()) {
        $_SESSION['reservation_message'] = "Votre réservation a été enregistrée et est en attente de validation.";
    } else {
        $_SESSION['reservation_message'] = "Erreur: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: profil.php");
    exit();
}

?>
