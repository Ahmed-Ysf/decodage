<?php

define("SERVEURBD", "172.18.58.63");
define("LOGIN", "root");
define("MOTDEPASSE", "toto");
define("NOMDELABASE", "ballon2021");
/**
 * @brief crée la connexion avec la base de donnée et retourne l'objet PDO pour manipuler la base
 * @return \PDO
 */
function connexionBdd() {
try {
$pdoOptions = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
$bdd = new PDO('mysql:host=' . SERVEURBD . ';dbname=' . NOMDELABASE, LOGIN, MOTDEPASSE, $pdoOptions);
$bdd->exec("set names utf8");
return $bdd;
} catch (PDOException $e) {
// En cas d'erreur, on affiche un message et on arrête tout
print "Erreur !: " . $e->getMessage() . "<br/>";
die();
}
}
/**
 * @brief insère la valeur des données décodé dans la BDD
 * @return data
 */
function majBDD($date, $latitude, $longitude, $altitude, $pression, $temperature, $radiation) {
try {

$bdd = connexionBdd();

$requete = $bdd->prepare("INSERT INTO ballon "
. "(horodatage, latitude, longitude, altitude, pression, temperature, radiation) "
. "VALUES(:horodatage, :latitude, :longitude, :altitude, :pression, :temperature, :radiation)");


$requete->bindParam(":horodatage", $date);
$requete->bindParam(":latitude", $latitude);
$requete->bindParam(":longitude", $longitude);
$requete->bindParam(":altitude", $altitude);
$requete->bindParam(":pression", $pression);
$requete->bindParam(":temperature", $temperature);
$requete->bindParam(":radiation", $radiation);
$retour = $requete->execute();


} catch (Exception $ex) {
print "Erreur : " . $ex->getMessage() . "<br/>";
die();
}
}


/**
 * @brief convertit le champ time format UNIX et retourne la date en format ISO 8601 
 * @return date
 */
function afficherDate($timeStamp) {
$date = new DateTime();
$date->setTimestamp($timeStamp);
return $date->format('Y-m-d H:i:s');
}

/**
 * @brief permet de contourner la limite de 64 bits max par trame 
 * @return $s
 */
function str_baseconvert($str, $frombase = 16, $tobase = 2) {
$str = trim($str);
if (intval($frombase) != 10) {
$len = strlen($str);
$q = 0;
for ($i = 0;
$i < $len;
$i++) {
$r = base_convert($str[$i], $frombase, 10);
$q = bcadd(bcmul($q, $frombase), $r);
}
} else
$q = $str;

if (intval($tobase) != 10) {
$s = '';
while (bccomp($q, '0', 0) > 0) {
$r = intval(bcmod($q, $tobase));
$s = base_convert($r, 10, $tobase) . $s;
$q = bcdiv($q, $tobase, 0);
}
} else
$s = $q;
return $s;
}

/**
 * @brief convertit les données reçue en binaire
 * @return databin
 */
function hextobin($data) {

$databin = str_baseconvert($data, 16, 2);

if (strlen($databin) < 96) {
$nbzero = 96 - strlen($databin);
for ($i = 0;
$i < $nbzero;
$i++) {
$databin = "0" . $databin;
}
}
return $databin;
}
/**
 * @brief récupère le champ "latitude" et le convertit en decimal 
 * @return val2
 */
function obtenirLat($databin, $deb, $long) {
$val = substr($databin, $deb, $long);
$val1 = base_convert($val, 2, 10);
$val2 = 0.000001 * $val1;
return $val2;
}
/**
 * @brief récupère le champ "longitude" et le convertit en decimal  
 * @return val2
 */
function obtenirLong($databin, $deb, $long) {
$signe = substr($databin, $deb, 1);
$val = substr($databin, $deb + 1, $long - 1);
$val1 = base_convert($val, 2, 10);
$val2 = 0.000001 * $val1;
if ($signe == 1) {
$val2 = -$val2;
}
return $val2;
}
/**
 * @brief récupère le champ "altitude" et le convertit en decimal  
 * @return val1
 */
function obtenirAlt($databin, $deb, $long) {
$val = substr($databin, $deb, $long);
$val1 = base_convert($val, 2, 10);
return $val1;
}
/**
 * @brief récupère le champ "temperature" et le convertit en decimal  
 * @return val1
 */
function obtenirTemp($databin, $deb, $long) {
$signe = substr($databin, $deb, 1);
$val = substr($databin, $deb + 1, $long - 1);
$val1 = base_convert($val, 2, 10);

if ($signe == 1) {
$val1 = -$val1;
}
return $val1;
}
/**
 * @brief récupère le champ "pression" et le convertit en decimal  
 * @return val1
 */
function obtenirPression($databin, $deb, $long) {
$val = substr($databin, $deb, $long);
$val1 = base_convert($val, 2, 10);
return $val1;
}
/**
 * @brief récupère le champ "radiation" et le convertit en decimal  
 * @return val1
 */
function obtenirRad($databin, $deb, $long) {
$val = substr($databin, $deb, $long);
$val1 = base_convert($val, 2, 10);
return $val1;
}
