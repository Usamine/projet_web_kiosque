<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index_sign_in.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - EnergyFuel</title>
    <link rel="stylesheet" href="style_panier.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="script_panier.js" defer></script>
    <script src="script_time.js" defer></script>
</head>
<body>
    <header>
    <div class="header-top">
            <div class="current-time" id="currentTime"></div>
        </div>
        <div class="logo">Energy<span>Fuel</span></div>
        <nav>
            <ul>
                <li><a href="index_acceuil.php" id="navHome">Accueil</a></li>
                <li><a href="index_service.php" id="navServices">Services</a></li>
                <li><a href="index_about_us.php" id="navAccount">about us</a></li>
                <li>
                    <div class="user-dropdown">
                        <div class="user-icon <?php echo isset($_SESSION['email']) ? 'connected' : 'disconnected'; ?>">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="dropdown-content">
                            <?php if (isset($_SESSION['email'])): ?>
                                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
                            <?php else: ?>
                                <a href="index_sign_in.php"><i class="fas fa-sign-in-alt"></i> Connecter</a>
                                <a href="index_sign_up.php"><i class="fas fa-user-plus"></i> Inscrivez-vous</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    <div id="authSection" class="container">
        <h2>Votre Panier</h2>
        <p id="emptyMessage" style="display: none;">Votre panier est vide.</p>
        <div id="cartSection" style="display: none;">
            <table id="cartTable" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <!-- Rows will be dynamically generated -->
            </table>
            <p><strong>Total :</strong> <span id="total">0.00 €</span></p>
            <button id="clearCart">Vider le panier</button>
            <a href="index_paiement.php" id="payButton" style="display: inline-block; margin-left: 10px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Payer</a>
        </div>
    </div>
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <!-- Column 1 -->
                <div class="footer-column">
                    <h3>À propos d'EnergyFuel</h3>
                    <p>EnergyFuel est votre partenaire de confiance pour tous vos besoins automobiles, offrant des services de qualité supérieure depuis plus de 15 ans.</p>
                </div>
                
                <!-- Column 2 -->
                <div class="footer-column">
                    <h3>Nos services</h3>
                    <ul class="footer-links">
                        <li><a href="index_lavage.php">Lavage Auto</a></li>
                        <li><a href="index_produit.php">Produits</a></li>
                        <li><a href="index_carburant.php">Carburant</a></li>
                    </ul>
                </div>
                
                <!-- Column 3 -->
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Avenue des Énergies, 75001 Paris</li>
                        <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                        <li><i class="fas fa-envelope"></i> contact@energyfuel.com</li>
                        <li><i class="fas fa-clock"></i> Lun-Sam: 8h-20h | Dim: 9h-18h</li>
                    </ul>
                </div>
                
                <!-- Column 4 -->
                <div class="footer-column">
                    <h3>Suivez-nous</h3>
                    <ul class="social-links">
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                    </ul>
                    <div style="margin-top: 20px;">
                        <a href="#" class="btn">Contactez-nous</a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 EnergyFuel. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>