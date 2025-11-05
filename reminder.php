<?php
include 'includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$sql = "SELECT nama_barang, nomor_plat, tanggal_pajak 
        FROM aset_barang 
        WHERE kategori_barang LIKE '%kendaraan%' AND tanggal_pajak IS NOT NULL";
$res = $conn->query($sql);

if ($res->num_rows == 0) {
    echo '<div class="alert green">✅ Tidak ada pajak kendaraan yang jatuh tempo dalam waktu dekat.</div>';
} else {
    while ($r = $res->fetch_assoc()) {
        $nama = htmlspecialchars($r['nama_barang']);
        $plat = htmlspecialchars($r['nomor_plat']);
        $tgl = $r['tanggal_pajak'];
        $today = date('Y-m-d');

        $diff = (strtotime($tgl) - strtotime($today)) / (60 * 60 * 24);
        if ($diff < 0) {
            $class = 'red';
            $icon = '⛔';
            $text = "Pajak kendaraan <b>$nama</b> ($plat) sudah <b>terlewat</b> sejak <b>$tgl</b>!";
        } elseif ($diff <= 30) {
            $class = 'yellow';
            $icon = '⚠️';
            $text = "Pajak kendaraan <b>$nama</b> ($plat) jatuh tempo pada <b>$tgl</b>.";
        } else {
            $class = 'green';
            $icon = '✅';
            $text = "Pajak kendaraan <b>$nama</b> ($plat) masih aman hingga <b>$tgl</b>.";
        }

        echo "<div class='alert $class'>$icon $text</div>";
    }
}
?>
