function lanjutBuatSoal() {

    const judulTest = document.getElementById("judul-test").value.trim();

    if (judulTest === "") {

        alert("Silakan masukkan judul test terlebih dahulu.");

        return;
    }

    localStorage.setItem("judulTest", judulTest);

    window.location.href = "buat-soal.php";
}

// =========================
// TAMBAH SOAL
// =========================

let nomorSoal = 1;

function tambahSoal() {

    const container = document.getElementById("soal-container");

    const index = nomorSoal;

    nomorSoal++;

    const soalBaru = document.createElement("div");

    soalBaru.classList.add("soal-card");

    soalBaru.innerHTML = `

        <div class="soal-title">
            Soal ${nomorSoal}
        </div>

        <div class="soal-content">

            <div class="pertanyaan-area">

                <textarea
                    class="input-soal"
                    name="soal[${index}][pertanyaan]"
                    placeholder="Tulis soal di sini..."
                    required
                ></textarea>

            </div>


            <div class="pilihan-area">

                <label class="pilihan-item">

                    <span>A.</span>

                    <input
                        type="text"
                        class="input-pilihan"
                        name="soal[${index}][pilihan_a]"
                        placeholder="Pilihan A"
                        required
                    >

                </label>


                <label class="pilihan-item">

                    <span>B.</span>

                    <input
                        type="text"
                        class="input-pilihan"
                        name="soal[${index}][pilihan_b]"
                        placeholder="Pilihan B"
                        required
                    >

                </label>


                <label class="pilihan-item">

                    <span>C.</span>

                    <input
                        type="text"
                        class="input-pilihan"
                        name="soal[${index}][pilihan_c]"
                        placeholder="Pilihan C"
                        required
                    >

                </label>


                <label class="pilihan-item">

                    <span>D.</span>

                    <input
                        type="text"
                        class="input-pilihan"
                        name="soal[${index}][pilihan_d]"
                        placeholder="Pilihan D"
                        required
                    >

                </label>

            </div>


            <div class="kunci-area">

                <label>
                    Kunci Jawaban
                </label>

                <select
                    name="soal[${index}][kunci_jawaban]"
                    required
                >

                    <option value="">
                        Pilih jawaban
                    </option>

                    <option value="A">
                        A
                    </option>

                    <option value="B">
                        B
                    </option>

                    <option value="C">
                        C
                    </option>

                    <option value="D">
                        D
                    </option>

                </select>

            </div>

        </div>
    `;

    container.appendChild(soalBaru);

    updateLangkah();
}

// =========================
// UPDATE LANGKAH
// =========================

function updateLangkah() {

    const langkah = document.getElementById("jumlah-langkah");

    if (langkah) {
        langkah.textContent = `Jumlah Soal: ${nomorSoal}`;
    }

}


// =========================
// KEMBALI
// =========================

function kembaliKeBuatTest() {

    window.location.href = "buat-test.php";
}


// =========================
// SELANJUTNYA
// =========================

function selesaiBuatSoal() {

    if (nomorSoal < 1) {
        return;
    }

    window.location.href = "test-selesai.php";
}

// =========================
// MASUK TEST
// =========================

function masukTest() {

    const kodeTest = document.getElementById("kode-test").value.trim();

    if (kodeTest === "") {

        alert("Silakan masukkan kode test terlebih dahulu.");

        return;
    }

    localStorage.setItem("kodeTest", kodeTest);

    window.location.href = "kerjakan-test.php";
}


// =========================
// MOBILE MENU
// =========================

function toggleMenu() {

    const menu = document.querySelector(".navbar-menu");

    if (menu) {
        menu.classList.toggle("show");
    }
}