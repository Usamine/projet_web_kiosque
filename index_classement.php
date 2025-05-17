<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnergyFuel - Station Service</title>
    <link rel="stylesheet" href="style_classement.css">
</head>
<body>
    <header>
        <div class="logo">Energy<span>Fuel</span></div>
        <nav>
            <ul>
                <li><a href="index_acceuil.php" id="navHome">Accueil</a></li>
                <li><a href="index_service.php" id="navServices">Services</a></li>
                <li><a href="index_classement.php" id="navLeaderboard">Classement</a></li>
                <li><a href="index_about_us.php" id="navAccount">about us</a></li>
                <li>
                    <div class="user-dropdown">
                        <div class="user-icon">
                            <i class="fas fa-user"></i>
                            <!-- Display username if logged in -->
                            <?php if (isset($_SESSION['email'])): ?>
                                <?php echo htmlspecialchars($_SESSION['email']); ?>
                            <?php else: ?>
                                😊😊
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-content">
                            <?php if (isset($_SESSION['email'])): ?>
                                <a href="logout.php">Se déconnecter</a>
                            <?php else: ?>
                                <a href="index_sign_in.php">Connecter</a>
                                <a href="index_sign_up.php">Inscrivez-vous</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    <!-- classement -->
    <div id="leaderboardSection" class="container hidden">
        <div class="section-title">
            <h1>Classement des Clients Fidèles</h1>
            <p>Découvrez nos clients les plus fidèles</p>
        </div>
        
        <div class="leaderboard">
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Client</th>
                        <th>Points de fidélité</th>
                        <th>Statut</th>
                        <th>Réductions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="rank-1">1</td>
                        <td>Raed Bouchnibe</td>
                        <td>1250</td>
                        <td>Or</td>
                        <td>15%</td>
                    </tr>
                    <tr>
                        <td class="rank-2">2</td>
                        <td>Zied Jaziri</td>
                        <td>980</td>
                        <td>Argent</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td class="rank-3">3</td>
                        <td>Fewzi Binzarti</td>
                        <td>820</td>
                        <td>Bronz</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Trizor Mbuto</td>
                        <td>750</td>
                        <td>--</td>
                        <td>7%</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Zouhair Dhewedi</td>
                        <td>680</td>
                        <td>--</td>
                        <td>7%</td>
                    </tr> 
                </tbody>
            </table>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 EnergyFuel. Tous droits réservés.</p>
        <p>Adresse : 5051 Moknine,  Monastir</p>
        <p>Téléphone : (+216) 27 312 507 | Email : haythem.idi@ensi-uma.tn</p>
    </footer>
    </body>
</html>