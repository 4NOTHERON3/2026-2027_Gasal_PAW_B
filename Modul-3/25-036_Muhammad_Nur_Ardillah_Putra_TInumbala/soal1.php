<?php
$fruits = array("Avocado","Blueberry","Cherry");

//1.1
array_push($fruits, "Durian", "Ederberry", "Fig", "Grape", "Honeydew");

echo "fruits = (";
foreach ($fruits as $semua) {
	echo $semua . ", ";
}
echo ")";

echo "<br> Nilai dengan index tertinggi: " . max($fruits) . "<br>";
//1.2
unset($fruits[1]);
echo "<br> data Blueberry dihapus <br>";

echo "fruits = (";
foreach ($fruits as $semua) {
	echo $semua . ", ";
}
echo ")";
?>