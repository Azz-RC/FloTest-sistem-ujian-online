USE db_flo_test;


-- =========================
-- TABEL USER
-- =========================
CREATE TABLE `user` (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);


-- =========================
-- TABEL TEST
-- =========================
CREATE TABLE test (
    id_test INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    judul_test VARCHAR(255) NOT NULL,
    kode_test VARCHAR(100) NOT NULL UNIQUE,
    tanggal_dibuat DATE NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (id_user)
        REFERENCES `user`(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- =========================
-- TABEL SOAL
-- =========================
CREATE TABLE soal (
    id_soal INT AUTO_INCREMENT PRIMARY KEY,
    id_test INT NOT NULL,
    nomor_soal INT NOT NULL,
    pertanyaan TEXT NOT NULL,
    pilihan_a VARCHAR(255) NOT NULL,
    pilihan_b VARCHAR(255) NOT NULL,
    pilihan_c VARCHAR(255) NOT NULL,
    pilihan_d VARCHAR(255) NOT NULL,
    kunci_jawaban VARCHAR(10) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (id_test)
        REFERENCES test(id_test)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- =========================
-- TABEL RIWAYAT TES
-- =========================
CREATE TABLE riwayat_tes (
    id_riwayat_tes INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_test INT NOT NULL,
    tanggal_masuk DATETIME NOT NULL,
    skor_akhir DECIMAL(5,2) DEFAULT NULL,
    jumlah_benar INT DEFAULT 0,
    jumlah_salah INT DEFAULT 0,
    status_pengerjaan ENUM('draft', 'selesai') NOT NULL DEFAULT 'draft',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (id_user)
        REFERENCES `user`(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    FOREIGN KEY (id_test)
        REFERENCES test(id_test)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- =========================
-- TABEL DETAIL RIWAYAT
-- =========================
CREATE TABLE detail_riwayat (
    id_detail_riwayat INT AUTO_INCREMENT PRIMARY KEY,
    id_riwayat_tes INT NOT NULL,
    id_soal INT NOT NULL,
    jawaban_siswa VARCHAR(10) DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (id_riwayat_tes)
        REFERENCES riwayat_tes(id_riwayat_tes)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    FOREIGN KEY (id_soal)
        REFERENCES soal(id_soal)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);