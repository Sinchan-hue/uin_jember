<?php
// Sisipkan file koneksi
require_once 'koneksi.php';

// Ambil NIDN dari parameter GET
$nidn = $_GET['nidn'];

// Query untuk mengambil data tesis/disertasi berdasarkan NIDN
$query = "
    SELECT 
        t.judul AS judul,
        m.nama AS nama_mahasiswa,
        p.Program AS prodi
    FROM dosen d
    LEFT JOIN ujian_tesis ut ON d.id_dosen = ut.id_dosen
    LEFT JOIN ujian_disertasi ud ON d.id_dosen = ud.id_dosen
    LEFT JOIN tesis t ON ut.id_tesis = t.id_tesis OR ud.id_tesis = t.id_tesis
    LEFT JOIN mahasiswa m ON t.id_mahasiswa = m.id_mahasiswa
    LEFT JOIN prodi p ON t.id_prodi = p.id
    WHERE d.nidn = '$nidn'
    GROUP BY t.id_tesis
";

// Eksekusi query
$result = $koneksi->query($query);

// Tampilkan data dalam tabel
if ($result->num_rows > 0) {
    echo "<table>
            <thead>
                <tr>
                    <th>Judul Tesis/Disertasi</th>
                    <th>Nama Mahasiswa</th>
                    <th>Prodi</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['judul']}</td>
                <td>{$row['nama_mahasiswa']}</td>
                <td>{$row['prodi']}</td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>Tidak ada data tesis/disertasi ditemukan untuk dosen ini.</p>";
}

// Tutup koneksi
$koneksi->close();
?>