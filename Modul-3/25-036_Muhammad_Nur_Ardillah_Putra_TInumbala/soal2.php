<?php
$fruits = array("Avocado","Blueberry","Cherry",);
$tambah = array("Durian", "Ederberry", "Fig", "Grape", "Honeydew");
$arrlength = count($fruits);

//2.1
for($i = 0; $i < count($tambah); $i++) {
	$fruits[] = $tambah[$i];
}

echo "panjang array: " . count($fruits) . "<br>";

for($x = 0; $x < count($fruits); $x++) {
	echo $fruits[$x];
	echo "<br>";

}
echo "<br> pada baris ke 5 aku ubah, penjelasannya karena arrlength masih belom diperbarui untuk datanya jadi aku memakai count(fruits) agar datanya diperbarui <br><br>";

//2.1
$vegies = array("Carrot","Broccoli","Spinach");
for($i = 0; $i < count($vegies); $i++) {
	echo $vegies[$i];
	echo "<br>";
}
?>