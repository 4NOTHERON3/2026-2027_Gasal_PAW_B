<?php

// array_push() menambahkan elemen ke akhir array
$buah = array("A");
array_push($buah, "B");

echo "Array awal: ";
foreach (array("A") as $item) {
    echo $item;
}

echo "<br>Hasil array_push: ";
foreach ($buah as $item) {
    echo $item . " ";
}


// array_merge() menggabungkan dua array
$buah2 = array("C");
$gabungan = array_merge($buah, $buah2);

echo "<br><br>Array awal: ";
foreach ($buah as $item) {
    echo $item . " ";
}

echo "<br>Hasil array_merge: ";
foreach ($gabungan as $item) {
    echo $item . " ";
}


// array_values() mengambil semua nilai array
$data = array("X" => 1, "Y" => 2);

echo "<br><br>Array awal: ";
foreach ($data as $key => $value) {
    echo $key . " => " . $value . " ";
}

echo "<br>Hasil array_values: ";
foreach (array_values($data) as $item) {
    echo $item . " ";
}


// array_search() mencari posisi suatu nilai dalam array
$huruf = array("A", "B", "C");
$hasil = array_search("B", $huruf);

echo "<br><br>Mencari B pada array: ";
foreach ($huruf as $item) {
    echo $item . " ";
}

echo "<br>Hasil array_search: " . $hasil;


// array_filter() menyaring nilai array berdasarkan kondisi
$angka = array(0, 1, false, 2, "", 3, "array");
$hasil = array_filter($angka);

echo "<br><br>Array awal: ";
foreach ($angka as $item) {
    echo var_export($item, true) . " ";
}

echo "<br>Hasil array_filter: ";
foreach ($hasil as $item) {
    echo $item . " ";
}
echo("<br>");

$angka = array(3, 1, 2);

echo "<br>array awal: ";
foreach ($angka as $item) {
    echo $item . " ";
}
// sort() mengurutkan array dari kecil ke besar
sort($angka);

echo "<br>Hasil sort: ";
foreach ($angka as $item) {
    echo $item . " ";
}


// rsort() mengurutkan array dari besar ke kecil
rsort($angka);

echo "<br>Hasil rsort: ";
foreach ($angka as $item) {
    echo $item . " ";
}
echo "<br><br>";

$nilai = array("Peter" => 35, "Ben" => 37, "Joe" => 43);

echo "array awal: ";
foreach ($nilai as $nama => $nilaiMahasiswa) {
    echo $nama . " => " . $nilaiMahasiswa . ", ";
}
// asort() mengurutkan nilai array asosiatif secara menaik
asort($nilai);

echo "<br>Hasil asort: ";
foreach ($nilai as $nama => $nilaiMahasiswa) {
    echo $nama . " => " . $nilaiMahasiswa . ", ";
}


// arsort() mengurutkan nilai array asosiatif secara menurun
arsort($nilai);

echo "<br>Hasil arsort: ";
foreach ($nilai as $nama => $nilaiMahasiswa) {
    echo $nama . " => " . $nilaiMahasiswa . ", ";
}


// ksort() mengurutkan array berdasarkan key secara menaik
ksort($nilai);

echo "<br>Hasil ksort: ";
foreach ($nilai as $nama => $nilaiMahasiswa) {
    echo $nama . " => " . $nilaiMahasiswa . ", ";
}


// krsort() mengurutkan array berdasarkan key secara menurun
krsort($nilai);

echo "<br>Hasil krsort: ";
foreach ($nilai as $nama => $nilaiMahasiswa) {
    echo $nama . " => " . $nilaiMahasiswa . ", ";
}

?>