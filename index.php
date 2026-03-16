<?php
require 'init.php';

$message = '';
$messageClass = '';

// Přidání zájmu
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);

    if ($name === '') {
        $message = "Pole nesmí být prázdné.";
        $messageClass = 'error';
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) FROM interests WHERE LOWER(name) = LOWER(?)");
        $stmt->execute([$name]);

        if ($stmt->fetchColumn() > 0) {
            $message = "Zájem už existuje.";
            $messageClass = 'error';
        } else {
            $stmt = $db->prepare("INSERT INTO interests (name) VALUES (?)");
            $stmt->execute([$name]);
            $message = "Zájem byl přidán.";
            $messageClass = 'success';
        }
    }
}

// Mazání
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM interests WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $message = "Zájem byl smazán.";
    $messageClass = 'success';
}

// Editace
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $newName = trim($_POST['name']);


    $stmt = $db->prepare("SELECT name FROM interests WHERE id = ?");
    $stmt->execute([$id]);
    $originalName = $stmt->fetchColumn();

    if ($newName === $originalName) {
        $message = "Zájem je stejný.";
        $messageClass = 'error';
    } elseif ($newName === '') {
        $message = "Nová hodnota nesmí být prázdná.";
        $messageClass = 'error';
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) FROM interests WHERE LOWER(name) = LOWER(?) AND id != ?");
        $stmt->execute([$newName, $id]);

        if ($stmt->fetchColumn() > 0) {
            $message = "Takový zájem už existuje.";
            $messageClass = 'error';
        } else {
            $stmt = $db->prepare("UPDATE interests SET name = ? WHERE id = ?");
            $stmt->execute([$newName, $id]);
            $message = "Zájem byl upraven.";
            $messageClass = 'success';
        }
    }
}

// Načtení seznamu zájmů
$interests = $db->query("SELECT * FROM interests ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<title>Zájmy</title>
<link rel="stylesheet" href="style.css">
<style>
/* Malá úprava pro inline editaci */
input.edit-input {
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 6px 10px;
    width: 150px;
}
button.save-btn {
    background-color: #16a34a; /* zelená pro uložit */
}
button.edit-btn {
    background-color: #1d4ed8; /* modrá pro upravit */
}
</style>
</head>
<body>

<div class="container">
<h2>Správa zájmů</h2>

<?php if ($message): ?>
    <div class="message <?= $messageClass ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<h3>Přidat nový zájem</h3>
<form method="POST">
    <input type="text" name="name" required>
    <button type="submit" name="add">Přidat</button>
</form>

<h3>Seznam zájmů</h3>

<?php if (count($interests) === 0): ?>
    <p>Zatím žádný zájem nebyl přidán.</p>
<?php else: ?>
<ul>
<?php foreach ($interests as $interest): ?>
    <li>
        <span><?= htmlspecialchars($interest['name']) ?></span>

        <div>
            <a href="?delete=<?= $interest['id'] ?>" onclick="return confirm('Opravdu smazat tento zájem?');">
                <button type="button" style="background:#dc2626;">Smazat</button>
            </a>

            <form method="POST" style="display:inline;" class="edit-form">
                <input type="hidden" name="id" value="<?= $interest['id'] ?>">
                <input type="text" name="name" value="<?= htmlspecialchars($interest['name']) ?>" class="edit-input" readonly>
                <button type="button" class="edit-btn">Upravit</button>
                <button type="submit" name="update" class="save-btn" style="display:none;">Uložit</button>
            </form>
        </div>
    </li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

</div>

<script>
// JavaScript pro odemčení editace
document.querySelectorAll('.edit-form').forEach(form => {
    const editBtn = form.querySelector('.edit-btn');
    const saveBtn = form.querySelector('.save-btn');
    const input = form.querySelector('.edit-input');

    editBtn.addEventListener('click', () => {
        input.removeAttribute('readonly');       // odemknout input
        input.focus();
        editBtn.style.display = 'none';         // skrýt tlačítko "Upravit"
        saveBtn.style.display = 'inline-block'; // zobrazit tlačítko "Uložit"
    });
});
</script>

</body>
</html>