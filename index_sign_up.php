<?php
// index_sign_up.php
session_start();
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kiosque_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}

$error = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $civilite = $_POST['choice'] ?? '';
    $nom = trim($_POST['registerName'] ?? '');
    $email = filter_var($_POST['registerEmail'] ?? '', FILTER_SANITIZE_EMAIL);
    $type_vehicule = $_POST['type_vehicule'] ?? '';
    $immatriculation = trim($_POST['immatriculation'] ?? '');
    $password = trim($_POST['registerPassword'] ?? '');
    $passwordConfirm = trim($_POST['registerPasswordConfirm'] ?? '');
    $conditions = isset($_POST['conditions']);

    if (!$civilite || !$nom || !$email || !$type_vehicule || !$immatriculation || !$password || !$conditions) {
        $error = "Tous les champs obligatoires doivent être remplis.";
    } elseif ($password !== $passwordConfirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, nom, type_vehicule, immatriculation, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$email, $nom, $type_vehicule, $immatriculation, $hashedPassword]);
            $message = "Inscription réussie ! Vous pouvez vous connecter.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Cet email est déjà utilisé.";
            } else {
                $error = "Erreur : " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnergyFuel - Inscription</title>
    <link rel="stylesheet" href="style_sign.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="script_sign_in.js" defer></script>
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
                <li><a href="index_acceuil.php">Accueil</a></li>
                <li><a href="index_service.php">Services</a></li>
                <li><a href="index_about_us.php">about us</a></li>
            </ul>
        </nav>
    </header>
    <div id="authSection" class="container">
        <?php if ($error): ?>
            <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($message): ?>
            <p style="color: green; text-align: center;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form class="auth-form" id="registerForm" method="POST">
            <div class="form-group">
                <label for="civilite">Civilité <span class="required">*</span></label><br>
                <input type="radio" id="civilite_m" name="choice" value="M." required> <label for="civilite_m">M.</label><br>
                <input type="radio" id="civilite_mme" name="choice" value="Mme" required> <label for="civilite_mme">Mme</label>
            </div>
            <div class="form-group">
                <label for="registerName">Nom complet <span class="required">*</span></label>
                <input type="text" id="registerName" name="registerName" placeholder="Votre nom complet" required>
            </div>
            <div class="form-group">
                <label for="registerEmail">Email <span class="required">*</span></label>
                <input type="email" id="registerEmail" name="registerEmail" placeholder="Votre email" required>
            </div>
            <div class="form-group">
                <label for="type_vehicule">Type de véhicule <span class="required">*</span></label>
                <select id="type_vehicule" name="type_vehicule" required>
                    <option value="">Sélectionnez</option>
                    <option value="voiture">Voiture</option>
                    <option value="camion">Camion</option>
                    <option value="moto">Moto</option>
                </select>
            </div>
            <div class="form-group">
                <label for="immatriculation">Numéro d'immatriculation <span class="required">*</span></label>
                <input type="text" id="immatriculation" name="immatriculation" placeholder="AA-123-BB ou 1234-A-56" required>
            </div>
            <div class="form-group">
                <label for="registerPassword">Mot de passe <span class="required">*</span></label>
                <div class="password-wrapper">
                    <input type="password" id="registerPassword" name="registerPassword" placeholder="Créez un mot de passe" required>
                    <i class="fas fa-eye toggle-password" data-target="registerPassword"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="registerPasswordConfirm">Confirmer le mot de passe <span class="required">*</span></label>
                <div class="password-wrapper">
                    <input type="password" id="registerPasswordConfirm" name="registerPasswordConfirm" placeholder="Confirmez votre mot de passe" required>
                    <i class="fas fa-eye toggle-password" data-target="registerPasswordConfirm"></i>
                </div>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="conditions" required>J'accepte les conditions générales d'utilisation <span class="required">*</span>
                </label>
            </div>
            <section>
                <button type="submit" class="btn btn-block" id="registerButton">S'inscrire</button>
            </section>
            <p><a href="index_sign_in.php">Se connecter</a></p>
        </form>
    </div>
    <footer class="minimised">
        <p>&copy; 2025 EnergyFuel. Tous droits réservés.</p>
        <p>Adresse : 5051 Moknine, Monastir</p>
        <p>Téléphone : (+216) 27 312 507 | Email : haythem.idi@ensi-uma.tn</p>
    </footer>
</body>
</html>