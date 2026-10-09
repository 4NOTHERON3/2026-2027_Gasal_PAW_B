<?php
$mahasiswa = array(
    array("Alex", "220401", "0812345678"),
    array("Bianca", "220402", "0812345687"),
    array("Candice", "220403", "0812345665")
);

echo "data awal = <br>";
echo "Student = (";
foreach ($mahasiswa as $data) {
echo $data[0] . ", " . $data[1] . ", " . $data[2] . ", ";
}
echo ")";
echo "<br><br>";

$mahasiswa[] = array("Daniel", "220404", "0812345670");
$mahasiswa[] = array("Elena", "220405", "0812345671");
$mahasiswa[] = array("Farhan", "220406", "0812345672");
$mahasiswa[] = array("Gina", "220407", "0812345673");
$mahasiswa[] = array("Hendra", "220408", "0812345674");

echo "data setelah ditambahkan 5 data baru = <br>";
echo "Student = (";
foreach ($mahasiswa as $data) {
echo $data[0] . ", " . $data[1] . ", " . $data[2] . ", ";
}
echo ")";
echo "<br><br>";


echo "<h3>Data Akhir Mahasiswa</h3>";
echo "<table border='1' cellpadding='5'>";
echo "
<tr>
    <th>Name</th>
    <th>NIM</th>
    <th>Mobile</th>
</tr>";

for ($i = 0; $i < count($mahasiswa); $i++) {
    echo "<tr>";
    echo "<td>" . $mahasiswa[$i][0] . "</td>";
    echo "<td>" . $mahasiswa[$i][1] . "</td>";
    echo "<td>" . $mahasiswa[$i][2] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>