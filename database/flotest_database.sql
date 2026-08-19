CREATE DATABASE IF NOT EXISTS flotest
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE flotest;

-- Hapus tabel jika ingin menjalankan ulang file ini
DROP TABLE IF EXISTS result;
DROP TABLE IF EXISTS room;
DROP TABLE IF EXISTS question;
DROP TABLE IF EXISTS test;

-- =========================================================
-- 1. TEST
-- Menyimpan data test yang dibuat melalui aplikasi.
-- =========================================================
CREATE TABLE test (
    id_test INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul_test VARCHAR(150) NOT NULL,
    kode_test VARCHAR(20) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- 2. QUESTION
-- Satu test memiliki banyak soal.
-- =========================================================
CREATE TABLE question (
    id_soal INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_test INT UNSIGNED NOT NULL,
    nomor_soal INT UNSIGNED NOT NULL,
    pertanyaan TEXT NOT NULL,
    pilihan_a VARCHAR(255) NOT NULL,
    pilihan_b VARCHAR(255) NOT NULL,
    pilihan_c VARCHAR(255) NOT NULL,
    pilihan_d VARCHAR(255) NOT NULL,
    kunci_jawaban ENUM('A','B','C','D') NOT NULL,

    CONSTRAINT fk_question_test
        FOREIGN KEY (id_test)
        REFERENCES test(id_test)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT uq_question_number
        UNIQUE (id_test, nomor_soal)
) ENGINE=InnoDB;

-- =========================================================
-- 3. ROOM
-- Dibuat ketika peserta memasukkan kode test.
-- Satu test dapat memiliki banyak room/pengerjaan.
-- =========================================================
CREATE TABLE room (
    id_room BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_test INT UNSIGNED NOT NULL,
    status_pengerjaan ENUM(
        'belum_dikerjakan',
        'sedang_dikerjakan',
        'selesai'
    ) NOT NULL DEFAULT 'belum_dikerjakan',
    tanggal_masuk DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    skor_akhir DECIMAL(5,2) NULL,

    CONSTRAINT fk_room_test
        FOREIGN KEY (id_test)
        REFERENCES test(id_test)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- 4. RESULT
-- Menyimpan hasil setelah pengerjaan sebuah room selesai.
-- Satu room menghasilkan maksimal satu result.
-- =========================================================
CREATE TABLE result (
    id_result BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_room BIGINT UNSIGNED NOT NULL UNIQUE,
    status_pengerjaan ENUM('selesai') NOT NULL DEFAULT 'selesai',
    skor_akhir DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    jumlah_benar INT UNSIGNED NOT NULL DEFAULT 0,
    jumlah_salah INT UNSIGNED NOT NULL DEFAULT 0,

    CONSTRAINT fk_result_room
        FOREIGN KEY (id_room)
        REFERENCES room(id_room)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- DATA CONTOH (opsional)
-- Bisa dihapus jika tidak diperlukan.
-- =========================================================

INSERT INTO test (judul_test, kode_test)
VALUES
('Dasar Pemrograman', 'DP2026'),
('Basis Data', 'BD2026'),
('Matematika', 'MT2026');

INSERT INTO question
(id_test, nomor_soal, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, kunci_jawaban)
VALUES
(1, 1, 'Apa kepanjangan dari HTML?', 'Hyper Text Markup Language', 'High Text Machine Language', 'Hyperlink Text Management Language', 'Home Tool Markup Language', 'A'),
(1, 2, 'Tag HTML untuk membuat paragraf adalah?', '<p>', '<h1>', '<br>', '<div>', 'A'),
(1, 3, 'Bahasa yang digunakan untuk mengatur tampilan halaman web adalah?', 'HTML', 'CSS', 'SQL', 'PHP', 'B'),
(1, 4, 'Tag untuk membuat judul terbesar pada HTML adalah?', '<h6>', '<title>', '<h1>', '<head>', 'C'),
(1, 5, 'Tag HTML untuk membuat tautan/link adalah?', '<img>', '<a>', '<link>', '<url>', 'B'),

(2, 1, 'SQL digunakan untuk?', 'Mengolah database', 'Menggambar', 'Mengedit video', 'Membuat desain', 'A'),
(2, 2, 'Perintah untuk mengambil data dari tabel adalah?', 'GET', 'SELECT', 'SHOWDATA', 'TAKE', 'B'),
(2, 3, 'Primary Key digunakan sebagai?', 'Penanda unik data', 'Password database', 'Nama database', 'Nama tabel', 'A'),
(2, 4, 'Perintah untuk menambahkan data adalah?', 'ADD', 'INSERT', 'INPUT', 'CREATE DATA', 'B'),
(2, 5, 'Perintah untuk menghapus data adalah?', 'REMOVE', 'DELETE', 'DROP ROW', 'CLEAR', 'B'),

(3, 1, 'Hasil dari 10 + 5 adalah?', '12', '15', '20', '25', 'B'),
(3, 2, 'Hasil dari 8 x 3 adalah?', '11', '18', '24', '32', 'C'),
(3, 3, 'Hasil dari 100 / 4 adalah?', '20', '25', '30', '40', 'B'),
(3, 4, 'Hasil dari 7² adalah?', '14', '21', '49', '56', 'C'),
(3, 5, 'Hasil dari 50 - 18 adalah?', '22', '28', '32', '38', 'C');

-- Contoh peserta memasukkan kode DP2026,
-- lalu sistem membuat room baru.
INSERT INTO room (id_test, status_pengerjaan)
VALUES (1, 'sedang_dikerjakan');

-- Setelah pengerjaan selesai:
UPDATE room
SET status_pengerjaan = 'selesai',
    skor_akhir = 80.00
WHERE id_room = 1;

INSERT INTO result
(id_room, status_pengerjaan, skor_akhir, jumlah_benar, jumlah_salah)
VALUES
(1, 'selesai', 80.00, 4, 1);

-- =========================================================
-- QUERY PENGECEKAN
-- =========================================================

-- Melihat semua test dan kode test
SELECT * FROM test;

-- Melihat soal berdasarkan test
SELECT *
FROM question
WHERE id_test = 1
ORDER BY nomor_soal;

-- Melihat room beserta judul test
SELECT
    r.id_room,
    t.judul_test,
    t.kode_test,
    r.status_pengerjaan,
    r.tanggal_masuk,
    r.skor_akhir
FROM room r
JOIN test t ON r.id_test = t.id_test;

-- Melihat hasil test
SELECT
    rs.id_result,
    t.judul_test,
    t.kode_test,
    rs.skor_akhir,
    rs.jumlah_benar,
    rs.jumlah_salah,
    rs.status_pengerjaan
FROM result rs
JOIN room r ON rs.id_room = r.id_room
JOIN test t ON r.id_test = t.id_test;
