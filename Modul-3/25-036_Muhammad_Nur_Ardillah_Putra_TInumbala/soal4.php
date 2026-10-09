<?php
$height = array("Andy" => "176", "Barry" => "165", "Charlie" => "170");

//4.1
$height["David"] = "180";
$height["Evan"] = "172";
$height["Frank"] = "168";
$height["George"] = "175";
$height["Henry"] = "178";

echo "height = (";
foreach ($height as $nama => $tinggi) {
echo $nama . " => " . $tinggi . ", ";
}
echo ")";
echo "<br><br>";

foreach ($height as $nama => $tinggi) {
    echo $nama . " is " . $tinggi . " cm Tall<br>";
}

echo "<br>";

//4.2

$weight = array("Andy" => "60", "Barry" => "65", "Charlie" => "70");

echo "weight = (";
foreach ($weight as $nama => $berat) {
echo $nama . " => " . $berat . ", ";
}
echo ")";
echo "<br><br>";

$nama = array_keys($weight);

for ($i = 0; $i < count($weight); $i++) {
    echo $nama[$i] . " is " . $weight[$nama[$i]] . " kg<br>";
}
?>