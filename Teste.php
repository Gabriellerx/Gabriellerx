<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de contact</title>
    <style>
        /* Intégration des classes CSS */
        .contact-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            background-color: #f8f8f8;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-description {
            font-size: 16px;
            margin-bottom: 30px;
            color: #666;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            color: #555;
        }

        .form-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="contact-form">
        <h1 class="form-title">Contactez-nous</h1>
        <p class="form-description">Bienvenue dans le code de gabriel L fsdfsqdgfegezgzesgfdsfgdsfgsdfg !</p>
        
        <form action="/page-traitement-donnees" method="post">
            <div class="form-group">
                <label class="form-label" for="nom">Votre nom :</label>
                <input class="form-input" type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Votre e-mail :</label>
                <input class="form-input" type="email" id="email" name="email" placeholder="Entrez votre e-mail" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="sujet">Sujet :</label>
                <select class="form-select" name="sujet" id="sujet" required>
                    <option value="" disabled selected hidden>Choisissez le sujet</option>
                    <option value="probleme-compte">Problème avec mon compte</option>
                    <option value="question-produit">Question à propos d’un produit</option>
                    <option value="suivi-commande">Suivi de ma commande</option>
                    <option value="autre">Autre...</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="message">Votre message :</label>
                <textarea class="form-textarea" id="message" name="message" placeholder="Entrez votre message" required></textarea>
            </div>
            
            <div class="form-group">
                <button class="form-button" type="submit">Envoyer mon message</button>
            </div>
        </form>
    </div>
</body>
</html>
