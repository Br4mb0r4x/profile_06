<?php

$db = new PDO("sqlite:profile.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

# Přidání zájmu
if(isset($_POST['add'])){

    $name = trim($_POST['name']);

    if($name != ""){

        $check = $db->prepare("SELECT COUNT(*) FROM interests WHERE name = ?");
        $check->execute([$name]);

        if($check->fetchColumn() == 0){
            $stmt = $db->prepare("INSERT INTO interests (name) VALUES (?)");
            $stmt->execute([$name]);
        }
        else{
            echo "Zájem už existuje.<br>";
        }

    } else {
        echo "Pole nesmí být prázdné.<br>";
    }
}

# Mazání
if(isset($_GET['delete'])){
    $stmt = $db->prepare("DELETE FROM interests WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
}

# Editace
if(isset($_POST['update'])){
    $stmt = $db->prepare("UPDATE interests SET name = ? WHERE id = ?");
    $stmt->execute([$_POST['name'], $_POST['id']]);
}

?>

<h2>Zájmy</h2>

<ul>

<?php

$stmt = $db->query("SELECT * FROM interests");

foreach($stmt as $row){

echo "<li>";

echo $row['name'];

echo " <a href='?delete=".$row['id']."'>Smazat</a>";

?>

<form method="post" style="display:inline;">
<input type="hidden" name="id" value="<?= $row['id'] ?>">
<input type="text" name="name">
<button name="update">Upravit</button>
</form>

<?php

echo "</li>";

}

?>

</ul>

<hr>

<h3>Přidat zájem</h3>

<form method="post">

<input type="text" name="name">
<button name="add">Přidat</button>

</form>