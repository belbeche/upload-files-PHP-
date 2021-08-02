<?php
require './bdd.php';
// une fois le formulaire envoyé , on récupère ces valeurs sinon erreur index undefined
if (isset($_FILES['file'])) {
    $tmpName = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'];
    $size = $_FILES['file']['size'];
    $error = $_FILES['file']['error'];
    // je découpe une châine de caractère , je récupere que les entier
    $tabExtension = explode('.', $name);
    // la fonction strtolower permets de mettre en minuscule tout une chaîne (ex: jpg, JPG , jpG)
    $extension = strtolower(end($tabExtension));

    $extensions = ['jpg', 'png', 'jpeg', 'gif', 'jfif'];
    // Taille max que l'on accepte
    $maxSize = 400000;
    // on vérfie bien que la taille du fichier est inférieur à maxSize
    if (in_array($extension, $extensions) && $size <= $maxSize) {
        //uniqid génère une chaine de caractère uniq 5f586bf96dcd38.73540086
        $uniqueName = uniqid('', true);
        //$file = 5f586bf96dcd38.73540086.jpg
        $file = $uniqueName . "." . $extension;
        // on fait une requete preparer pour éviter les injection SQL , et enregistrer le nom du fichier en BDD
        $req = $db->prepare('INSERT INTO file (name) VALUES (?)');
        /**
         * La fonction execute() attend 1 seul paramètre qui est un tableau. 
         * Dans ce tableau on doit lui passer autant de paramètre que l’on de « ? » dans la requête.
         */
        $req->execute([$file]);

        move_uploaded_file($tmpName, './upload/' . $file);
    } else {
        echo "Une erreur est survenue";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de fichier</title>
</head>

<body>

    <form action="index.php" method="POST" enctype="multipart/form-data">
        <label for="file">Fichier</label>
        <input type="file" name="file">
        <button type="submit">Enregistrer</button>
    </form>
    <h2>Mes images</h2>
    <?php

    $req = $db->Query('SELECT name FROM file');
    /**
     * Tant qu'il y'a des résultats on continue , le fetch est une méthode PDO.
     */
    while ($data = $req->fetch()) {
        echo "<img src='./upload/" . $data['name'] . "' width='300px' ><br>";
    }
    ?>

</body>

</html>