<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: test_connexion.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $action = $_POST['action']; // 'approve' ou 'reject'
    $status = $action == 'approve' ? 'approved' : 'rejected';

    // Mettre à jour le statut de la réservation
    $sql = "UPDATE reservations SET status = ? WHERE id = ? AND annonce_id IN (SELECT id FROM annonces WHERE user_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $status, $reservation_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $_SESSION['reservation_message'] = "La réservation a été " . ($action == 'approve' ? 'approuvée' : 'rejetée') . ".";
    } else {
        $_SESSION['reservation_message'] = "Erreur: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

// Redirect back to the profile page
header("Location: profil.php");
exit();
?>
