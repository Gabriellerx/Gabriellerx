<?php

declare(strict_types=1);

const DATA_FILE = __DIR__ . '/journees.json';

/**
 * @return array<int, array<string, string>>
 */
function readEntries(): array
{
    if (!file_exists(DATA_FILE)) {
        return [];
    }

    $raw = file_get_contents(DATA_FILE);
    if ($raw === false || $raw === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [];
    }

    return array_values(array_filter($decoded, 'is_array'));
}

/**
 * @param array<int, array<string, string>> $entries
 */
function saveEntries(array $entries): bool
{
    return file_put_contents(
        DATA_FILE,
        json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ) !== false;
}

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

$errors = [];
$successMessage = '';
$entries = readEntries();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = sanitize($_POST['date'] ?? '');
    $humeur = sanitize($_POST['humeur'] ?? '');
    $resume = sanitize($_POST['resume'] ?? '');
    $moment = sanitize($_POST['moment'] ?? '');
    $gratitude = sanitize($_POST['gratitude'] ?? '');
    $demain = sanitize($_POST['demain'] ?? '');

    if ($date === '') {
        $errors[] = 'La date est obligatoire.';
    }

    if ($humeur === '') {
        $errors[] = 'Choisis ton humeur du jour.';
    }

    if ($resume === '') {
        $errors[] = 'Ajoute une description de ta journée.';
    }

    if (empty($errors)) {
        $entries[] = [
            'date' => $date,
            'humeur' => $humeur,
            'resume' => $resume,
            'moment' => $moment,
            'gratitude' => $gratitude,
            'demain' => $demain,
            'created_at' => date('c'),
        ];

        usort(
            $entries,
            static fn(array $a, array $b): int => strcmp($b['date'], $a['date'])
        );

        if (saveEntries($entries)) {
            $successMessage = 'Ta journée a bien été enregistrée 🎉';
            $_POST = [];
        } else {
            $errors[] = 'Impossible d\'enregistrer pour le moment. Réessaie.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Journal de Journée</title>
    <style>
        :root {
            --bg: #f3f4f6;
            --card: #ffffff;
            --text: #1f2937;
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --success: #065f46;
            --danger: #991b1b;
            --muted: #6b7280;
            --border: #e5e7eb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        .container {
            width: min(980px, 92%);
            margin: 2rem auto;
            display: grid;
            gap: 1.5rem;
            grid-template-columns: 1fr;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        }

        h1, h2 { margin-top: 0; }

        .subtitle {
            margin-top: -0.4rem;
            color: var(--muted);
        }

        form { display: grid; gap: 1rem; }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font: inherit;
            background: #fff;
        }

        textarea { min-height: 120px; resize: vertical; }

        button {
            border: none;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.85rem 1rem;
            cursor: pointer;
        }

        button:hover { background: var(--primary-dark); }

        .alert {
            border-radius: 10px;
            padding: 0.75rem 0.9rem;
            margin-bottom: 1rem;
        }

        .alert-success { background: #ecfdf5; color: var(--success); }
        .alert-error { background: #fef2f2; color: var(--danger); }

        .entries {
            display: grid;
            gap: 1rem;
        }

        .entry {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.6rem;
            font-size: 0.95rem;
            color: var(--muted);
        }

        .empty {
            color: var(--muted);
            margin: 0;
        }
    </style>
</head>
<body>
    <main class="container">
        <section class="card">
            <h1>📝 Mon application de journée</h1>
            <p class="subtitle">Décris ta journée, ton humeur et ce que tu veux améliorer demain.</p>

            <?php if ($successMessage !== ''): ?>
                <div class="alert alert-success"><?= $successMessage ?></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <div>
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="<?= sanitize($_POST['date'] ?? date('Y-m-d')) ?>" required>
                </div>

                <div>
                    <label for="humeur">Humeur</label>
                    <select id="humeur" name="humeur" required>
                        <option value="">-- Choisir --</option>
                        <?php
                        $moods = ['Excellent', 'Bien', 'Mitigé', 'Fatigué', 'Difficile'];
                        $selectedMood = sanitize($_POST['humeur'] ?? '');
                        foreach ($moods as $mood):
                            $selected = $selectedMood === $mood ? 'selected' : '';
                            ?>
                            <option value="<?= $mood ?>" <?= $selected ?>><?= $mood ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="resume">Comment s'est passée ta journée ?</label>
                    <textarea id="resume" name="resume" placeholder="Ex: J'ai été productif ce matin, puis j'ai fait une pause sport..." required><?= sanitize($_POST['resume'] ?? '') ?></textarea>
                </div>

                <div>
                    <label for="moment">Ton meilleur moment</label>
                    <input type="text" id="moment" name="moment" value="<?= sanitize($_POST['moment'] ?? '') ?>" placeholder="Ex: Un appel avec un ami">
                </div>

                <div>
                    <label for="gratitude">Ce pour quoi tu es reconnaissant(e)</label>
                    <input type="text" id="gratitude" name="gratitude" value="<?= sanitize($_POST['gratitude'] ?? '') ?>" placeholder="Ex: Ma santé, ma famille">
                </div>

                <div>
                    <label for="demain">Objectif pour demain</label>
                    <input type="text" id="demain" name="demain" value="<?= sanitize($_POST['demain'] ?? '') ?>" placeholder="Ex: Finir mon dossier avant 11h">
                </div>

                <button type="submit">Enregistrer ma journée</button>
            </form>
        </section>

        <section class="card">
            <h2>Historique des journées</h2>
            <?php if (empty($entries)): ?>
                <p class="empty">Aucune journée enregistrée pour le moment.</p>
            <?php else: ?>
                <div class="entries">
                    <?php foreach ($entries as $entry): ?>
                        <article class="entry">
                            <div class="meta">
                                <strong><?= sanitize($entry['date'] ?? '') ?></strong>
                                <span>Humeur: <?= sanitize($entry['humeur'] ?? '') ?></span>
                            </div>
                            <p><strong>Résumé:</strong> <?= nl2br(sanitize($entry['resume'] ?? '')) ?></p>
                            <?php if (($entry['moment'] ?? '') !== ''): ?>
                                <p><strong>Meilleur moment:</strong> <?= sanitize($entry['moment']) ?></p>
                            <?php endif; ?>
                            <?php if (($entry['gratitude'] ?? '') !== ''): ?>
                                <p><strong>Gratitude:</strong> <?= sanitize($entry['gratitude']) ?></p>
                            <?php endif; ?>
                            <?php if (($entry['demain'] ?? '') !== ''): ?>
                                <p><strong>Objectif demain:</strong> <?= sanitize($entry['demain']) ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
