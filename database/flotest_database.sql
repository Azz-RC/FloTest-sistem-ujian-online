CREATE DATABASE IF NOT EXISTS flotest;
USE flotest;

-- =========================================================
-- 1. TABEL classes
-- Menyimpan data kelas siswa
-- =========================================================
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(50) NOT NULL
);

-- =========================================================
-- 2. TABEL users
-- Menyimpan data guru & siswa
-- =========================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('guru', 'siswa') NOT NULL,
    class_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);

-- =========================================================
-- 3. TABEL tests
-- Menyimpan data test yang dibuat guru
-- =========================================================
CREATE TABLE tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guru_id INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guru_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================================================
-- 4. TABEL questions
-- Menyimpan soal per test
-- =========================================================
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT NOT NULL,
    soal TEXT NOT NULL,
    urutan INT NOT NULL,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
);

-- =========================================================
-- 5. TABEL answers
-- Menyimpan pilihan jawaban (A/B/C/D) tiap soal + kunci jawaban
-- =========================================================
CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    pilihan CHAR(1) NOT NULL, -- A, B, C, D
    teks_jawaban VARCHAR(255) NOT NULL,
    is_correct BOOLEAN NOT NULL DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- =========================================================
-- 6. TABEL rooms
-- Menyimpan kode test yang dibagikan ke siswa
-- =========================================================
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT NOT NULL,
    kode_test VARCHAR(10) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
);

-- =========================================================
-- 7. TABEL student_answers
-- Menyimpan jawaban siswa per soal, per room
-- =========================================================
CREATE TABLE student_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_id INT NULL, -- jawaban yang dipilih siswa, NULL kalau belum dijawab
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_jawaban (room_id, user_id, question_id),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (answer_id) REFERENCES answers(id) ON DELETE SET NULL
);

-- =========================================================
-- 8. TABEL results
-- Menyimpan hasil akhir test siswa
-- =========================================================
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    skor INT NOT NULL,
    jumlah_benar INT NOT NULL,
    jumlah_salah INT NOT NULL,
    selesai_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_hasil (room_id, user_id),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================================================
-- INSERT DATA DUMMY
-- =========================================================

-- Kelas
INSERT INTO classes (nama_kelas) VALUES ('RPL 1');

-- Users (1 guru, 1 siswa - sesuai default login di storyboard)
INSERT INTO users (username, password, role, class_id) VALUES
('guru1', 'guru123', 'guru', NULL),
('user', '161820', 'siswa', 1);

-- Test
INSERT INTO tests (guru_id, judul) VALUES
(1, 'Test Contoh');

-- Room (kode test sesuai storyboard: 335)
INSERT INTO rooms (test_id, kode_test) VALUES
(1, '335');

-- Soal (5 soal sesuai storyboard "Soal 1 dari 5")
INSERT INTO questions (test_id, soal, urutan) VALUES
(1, 'Ibukota Indonesia adalah ...', 1),
(1, 'Bahasa pemrograman untuk styling web disebut ...', 2),
(1, 'Perintah untuk mengambil data di SQL adalah ...', 3),
(1, 'Framework PHP yang dipakai di FloTest Creator Level adalah ...', 4),
(1, 'Struktur ERD berguna untuk merancang ...', 5);

-- Jawaban Soal 1
INSERT INTO answers (question_id, pilihan, teks_jawaban, is_correct) VALUES
(1, 'A', 'Bandung', 0),
(1, 'B', 'Jakarta', 1),
(1, 'C', 'Surabaya', 0),
(1, 'D', 'Yogyakarta', 0);

-- Jawaban Soal 2
INSERT INTO answers (question_id, pilihan, teks_jawaban, is_correct) VALUES
(2, 'A', 'PHP', 0),
(2, 'B', 'CSS', 1),
(2, 'C', 'SQL', 0),
(2, 'D', 'HTML', 0);

-- Jawaban Soal 3
INSERT INTO answers (question_id, pilihan, teks_jawaban, is_correct) VALUES
(3, 'A', 'INSERT', 0),
(3, 'B', 'DELETE', 0),
(3, 'C', 'SELECT', 1),
(3, 'D', 'UPDATE', 0);

-- Jawaban Soal 4
INSERT INTO answers (question_id, pilihan, teks_jawaban, is_correct) VALUES
(4, 'A', 'CodeIgniter', 0),
(4, 'B', 'Laravel', 1),
(4, 'C', 'Symfony', 0),
(4, 'D', 'Yii', 0);

-- Jawaban Soal 5
INSERT INTO answers (question_id, pilihan, teks_jawaban, is_correct) VALUES
(5, 'A', 'Tampilan web', 0),
(5, 'B', 'Struktur database', 1),
(5, 'C', 'Warna website', 0),
(5, 'D', 'Kecepatan internet', 0);

-- Jawaban siswa (dummy: 4 benar, 1 salah sesuai storyboard "Hasil Test 80/100")
INSERT INTO student_answers (room_id, user_id, question_id, answer_id) VALUES
(1, 2, 1, 2),  -- benar (Jakarta)
(1, 2, 2, 6),  -- benar (CSS)
(1, 2, 3, 11), -- benar (SELECT)
(1, 2, 4, 14), -- benar (Laravel)
(1, 2, 5, 17); -- salah (pilih 'Tampilan web', harusnya 'Struktur database')

-- Hasil akhir
INSERT INTO results (room_id, user_id, skor, jumlah_benar, jumlah_salah) VALUES
(1, 2, 80, 4, 1);
