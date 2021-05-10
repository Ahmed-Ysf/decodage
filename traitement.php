
<html>
    <head>
        <title>traitement form</title>
        <meta charset="UTF-8">
    </head>
    <body>
        <?php
        require_once './fonctions_ballon_inc.php';

        // affichage 
        echo "Données recue : " . "<br/>";
        echo "L'ID de l'appareil : {$_POST["device"]}<br/>";
        echo "Donnees : {$_POST["data"]}<br/>";
        echo afficherDate($_POST["time"]) . "<br/>";
        $date = afficherDate($_POST["time"]);

        // convertir hexadecimal en binaire
        $databin = hextobin($_POST["data"]);


        echo "Les données en binaire : " . "<br/>";
        echo $databin . "<br/>";
        echo "Extraction des données : " . "<br/>";

        // Selectionner les données et convertir en decimal

        $latitude = obtenirLat($databin, 1, 26);
        echo 'latitude = ' . $latitude . "<br>";

        $longitude = obtenirLong($databin, 27, 24);
        echo 'longitude = ' . $longitude . "<br>";

        $altitude = obtenirAlt($databin, 51, 15);
        echo 'altitude = ' . $altitude . "<br>";

        $temperature = obtenirTemp($databin, 66, 7);
        echo 'temperature = ' . $temperature . "<br>";

        $pression = obtenirPression($databin, 73, 11);
        echo 'pression = ' . $pression . "<br>";

        $radiation = obtenirRad($databin, 84, 12);
        echo 'radiation = ' . $radiation . "<br>";
        echo "<hr/><br/>";
        majBDD($date, $latitude, $longitude, $altitude, $pression, $temperature, $radiation);
        ?>
    </body>
</html> 