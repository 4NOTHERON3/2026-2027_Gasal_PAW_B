<?php
$height = array("Andy"=>"176","Barry"=>"165","Charlie"=>"170");

//3.1
$height["David"] = "180";
$height["Ethan"] = "172";
$height["Frank"] = "168";
$height["George"] = "175";
$height["Harry"] = "182";

echo "height = (";
foreach ($height as $nama => $tinggi) {
echo $nama . " => " . $tinggi . ", ";
}
echo ")";


echo "<br>Tinggi terakhir: " . end($height) . "<br>";

unset($height["Frank"]);

echo "height = (";
foreach ($height as $nama => $tinggi) {
echo $nama . " => " . $tinggi . ", ";
}
echo ")";

echo "<br>Tinggi terakhir setelah penghapusan: " . end($height) . "<br>";

//3.2
$weight = array("Andy"=>"70", "Berry"=>"65", "Charlie"=>"75");

echo "<br>weight = (";
foreach ($weight as $nama => $berat) {
echo $nama . " => " . $berat . ", ";
}
echo ")";
echo "<br>Data kedua: " . $weight["Berry"]


?>