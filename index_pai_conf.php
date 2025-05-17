<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index_sign_in.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnergyFuel - Station Service</title>
    <script src="script_time.js" defer></script>
    <link rel="stylesheet" href="style_pai_conf.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <li>
                    <a href="panier.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge" id="cart-badge">0</span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>
    <!--confirmation de paiment-->
    <div id="confirmationSection" class="container hidden">
        <div class="section-title">
            <h1>Merci pour votre achat !</h1>
            <p>Votre transaction a été complétée avec succès</p>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <p style="font-size: 1.2rem; margin-bottom: 1rem;">Un reçu a été envoyé à votre adresse email.</p>
            <section>
                <a href="index_service.php" class="btn" >Retour aux service</a>
            </section>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 EnergyFuel. Tous droits réservés.</p>
        <p>Adresse : 5051 Moknine, Monastir</p>
        <p>Téléphone : (+216) 27 312 507 | Email : haythem.idi@ensi-uma.tn</p>
    </footer>
    </body>
</html>