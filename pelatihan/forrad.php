<?php

// siswa
$sql = "SELECT COUNT(*) AS total FROM siswa";
// pelanggaran
$sql = "SELECT COUNT(*) AS total FROM pelanggaran";
// hari ini
$sql = "SELECT COUNT(*) AS total FROM pelanggaran WHERE DATE(tanggal) = DATE(CURRENT_DATE())";

// kategori pelanggaran
$sql = "SELECT jp.nama AS jenis, COUNT(*) AS total
		FROM pelanggaran p
		LEFT JOIN jenis_pelanggaran jp ON p.jenis_id = jp.id
		GROUP BY jp.nama ORDER BY total DESC";
//  pelanggaran hari ini
$sql = "SELECT k.nama AS kelas, COUNT(p.id) AS total 
        FROM pelanggaran p
        LEFT JOIN siswa s ON p.nis = s.nis 
        LEFT JOIN kelas k ON s.kelas_id = k.id 
        WHERE DATE(tanggal) = DATE(CURRENT_DATE()) GROUP BY kelas";
// pelanggaran per kelas
$sql = "SELECT k.nama, COUNT(*) as total
		FROM pelanggaran p
		LEFT JOIN siswa s ON p.nis = s.nis
		LEFT JOIN kelas k ON s.kelas_id = k.id GROUP BY(k.nama) ORDER BY total DESC";
