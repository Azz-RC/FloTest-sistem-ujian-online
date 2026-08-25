import "./bootstrap";

// =====================================================
// ELEMENT
// =====================================================

const btnMasukRoom = document.getElementById("btn-masuk-room");
const btnKirimJawaban = document.getElementById("btn-kirim-jawaban");
const btnRiwayat = document.getElementById("btn-riwayat");

// =====================================================
// STATE
// =====================================================

let currentRoom = {
    idRiwayatTes: null,
    idUser: null,
    idTest: null,
};

// =====================================================
// HELPER ALERT
// =====================================================

function showAlert(message, type = "info") {
    const alertElement = document.getElementById("alert");

    if (!alertElement) {
        return;
    }

    alertElement.textContent = message;

    alertElement.classList.remove(
        "hidden",
        "bg-green-100",
        "text-green-800",
        "bg-red-100",
        "text-red-800",
        "bg-blue-100",
        "text-blue-800",
    );

    if (type === "success") {
        alertElement.classList.add("bg-green-100", "text-green-800");
    } else if (type === "error") {
        alertElement.classList.add("bg-red-100", "text-red-800");
    } else {
        alertElement.classList.add("bg-blue-100", "text-blue-800");
    }
}

// =====================================================
// MASUK ROOM
// =====================================================

if (btnMasukRoom) {
    btnMasukRoom.addEventListener("click", async () => {
        const idUser = document.getElementById("id_user").value;
        const kodeTest = document
            .getElementById("kode_test")
            .value.trim()
            .toUpperCase();

        if (!idUser || !kodeTest) {
            showAlert("ID User dan Kode Test wajib diisi.", "error");
            return;
        }

        try {
            btnMasukRoom.disabled = true;
            btnMasukRoom.textContent = "Memproses...";

            const response = await axios.post("/api/masuk-room", {
                id_user: Number(idUser),
                kode_test: kodeTest,
            });

            console.log("Response masuk room:", response.data);

            const data = response.data.data;

            // Simpan data room
            currentRoom.idRiwayatTes = data.id_riwayat_tes;
            currentRoom.idUser = data.id_user;
            currentRoom.idTest = data.id_test;

            // Tampilkan informasi test
            document.getElementById("judul-test").textContent = data.judul_test;

            document.getElementById("kode-test-display").textContent =
                data.kode_test;

            document.getElementById("status-test-display").textContent =
                data.status_pengerjaan;

            // Container soal
            const soalContainer = document.getElementById("soal-container");

            soalContainer.innerHTML = "";

            // Generate soal
            data.soal.forEach((soal) => {
                const div = document.createElement("div");

                div.className = "mb-6 rounded-lg border border-gray-200 p-4";

                div.innerHTML = `
                    <p class="mb-4 font-semibold text-gray-800">
                        ${soal.nomor_soal}. ${soal.pertanyaan}
                    </p>

                    <div class="space-y-3">

                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-300 p-3 hover:bg-gray-50">
                            <input
                                type="radio"
                                name="soal-${soal.id_soal}"
                                value="A"
                                data-id-soal="${soal.id_soal}"
                                class="h-4 w-4"
                            >
                            <span>
                                <strong>A.</strong>
                                ${soal.pilihan_a}
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-300 p-3 hover:bg-gray-50">
                            <input
                                type="radio"
                                name="soal-${soal.id_soal}"
                                value="B"
                                data-id-soal="${soal.id_soal}"
                                class="h-4 w-4"
                            >
                            <span>
                                <strong>B.</strong>
                                ${soal.pilihan_b}
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-300 p-3 hover:bg-gray-50">
                            <input
                                type="radio"
                                name="soal-${soal.id_soal}"
                                value="C"
                                data-id-soal="${soal.id_soal}"
                                class="h-4 w-4"
                            >
                            <span>
                                <strong>C.</strong>
                                ${soal.pilihan_c}
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-300 p-3 hover:bg-gray-50">
                            <input
                                type="radio"
                                name="soal-${soal.id_soal}"
                                value="D"
                                data-id-soal="${soal.id_soal}"
                                class="h-4 w-4"
                            >
                            <span>
                                <strong>D.</strong>
                                ${soal.pilihan_d}
                            </span>
                        </label>

                    </div>
                `;

                soalContainer.appendChild(div);
            });

            // Tampilkan section test
            document.getElementById("test-section").classList.remove("hidden");

            // Sembunyikan hasil lama
            document.getElementById("hasil-section").classList.add("hidden");

            showAlert(response.data.message, "success");

            // Scroll ke soal
            document.getElementById("test-section").scrollIntoView({
                behavior: "smooth",
            });
        } catch (error) {
            console.error("Error masuk room:", error);

            if (error.response) {
                console.error("Response error:", error.response.data);

                showAlert(
                    error.response.data.message || "Gagal masuk room.",
                    "error",
                );
            } else {
                showAlert("Tidak dapat terhubung ke server.", "error");
            }
        } finally {
            btnMasukRoom.disabled = false;
            btnMasukRoom.textContent = "Masuk Room";
        }
    });
}

// =====================================================
// KIRIM JAWABAN
// =====================================================

if (btnKirimJawaban) {
    btnKirimJawaban.addEventListener("click", async () => {
        // Pastikan sudah masuk room
        if (
            !currentRoom.idRiwayatTes ||
            !currentRoom.idUser ||
            !currentRoom.idTest
        ) {
            showAlert("Silakan masuk room test terlebih dahulu.", "error");
            return;
        }

        // Ambil semua soal
        const semuaSoal = document.querySelectorAll(
            "#soal-container input[type='radio'][data-id-soal]",
        );

        // Ambil ID soal unik
        const idSoalUnik = [
            ...new Set(
                Array.from(semuaSoal).map((input) => input.dataset.idSoal),
            ),
        ];

        const jawaban = [];

        // Cek jawaban satu per satu
        for (const idSoal of idSoalUnik) {
            const pilihanDipilih = document.querySelector(
                `input[name="soal-${idSoal}"]:checked`,
            );

            if (!pilihanDipilih) {
                showAlert(`Soal dengan ID ${idSoal} belum dijawab.`, "error");

                pilihanDipilih?.scrollIntoView({
                    behavior: "smooth",
                });

                return;
            }

            jawaban.push({
                id_soal: Number(idSoal),
                jawaban: pilihanDipilih.value,
            });
        }

        // Konfirmasi
        const yakin = confirm(
            "Apakah semua jawaban sudah benar?\n\nSetelah dikirim, test akan dianggap selesai.",
        );

        if (!yakin) {
            return;
        }

        try {
            btnKirimJawaban.disabled = true;
            btnKirimJawaban.textContent = "Mengirim...";

            const response = await axios.post("/api/kerjakan-test", {
                id_riwayat_tes: currentRoom.idRiwayatTes,
                id_user: currentRoom.idUser,
                id_test: currentRoom.idTest,
                jawaban: jawaban,
            });

            console.log("Response kerjakan test:", response.data);

            const data = response.data.data;

            // Tampilkan hasil
            document.getElementById("hasil-nilai").textContent = Number(
                data.nilai,
            ).toFixed(2);

            document.getElementById("hasil-benar").textContent = data.benar;

            document.getElementById("hasil-salah").textContent = data.salah;

            // Tampilkan hasil
            document.getElementById("hasil-section").classList.remove("hidden");

            // Update status
            document.getElementById("status-test-display").textContent =
                data.status_pengerjaan;

            // Disable semua pilihan
            document
                .querySelectorAll("#soal-container input[type='radio']")
                .forEach((input) => {
                    input.disabled = true;
                });

            // Disable tombol
            btnKirimJawaban.disabled = true;
            btnKirimJawaban.textContent = "Test Selesai";

            showAlert(response.data.message, "success");

            // Scroll ke hasil
            document.getElementById("hasil-section").scrollIntoView({
                behavior: "smooth",
            });
        } catch (error) {
            console.error("Error mengirim jawaban:", error);

            if (error.response) {
                console.error("Response error:", error.response.data);

                showAlert(
                    error.response.data.message || "Gagal mengirim jawaban.",
                    "error",
                );
            } else {
                showAlert("Tidak dapat terhubung ke server.", "error");
            }
        } finally {
            if (
                !document
                    .getElementById("hasil-section")
                    .classList.contains("hidden")
            ) {
                btnKirimJawaban.disabled = true;
            } else {
                btnKirimJawaban.disabled = false;
                btnKirimJawaban.textContent = "Kirim Jawaban";
            }
        }
    });
}

// =====================================================
// LIHAT RIWAYAT
// =====================================================

if (btnRiwayat) {
    btnRiwayat.addEventListener("click", async () => {
        const idUser = document.getElementById("id_user").value;

        if (!idUser) {
            showAlert("Masukkan ID User terlebih dahulu.", "error");
            return;
        }

        try {
            btnRiwayat.disabled = true;
            btnRiwayat.textContent = "Memuat...";

            const response = await axios.get(
                `/api/user/${Number(idUser)}/riwayat-test`,
            );

            console.log("Response riwayat:", response.data);

            const data = response.data.data;

            const container = document.getElementById("riwayat-container");

            container.innerHTML = "";

            if (!data || data.length === 0) {
                container.innerHTML = `
                    <div class="rounded-lg bg-gray-100 p-4 text-center text-gray-600">
                        Belum ada riwayat test.
                    </div>
                `;

                return;
            }

            data.forEach((item) => {
                const div = document.createElement("div");

                div.className = "mb-4 rounded-lg border border-gray-200 p-4";

                div.innerHTML = `
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                        <div>
                            <h3 class="font-semibold text-gray-800">
                                ${item.judul_test}
                            </h3>

                            <p class="text-sm text-gray-500">
                                Kode: ${item.kode_test}
                            </p>

                            <p class="text-sm text-gray-500">
                                Status: ${item.status_pengerjaan}
                            </p>

                            <p class="text-sm text-gray-500">
                                Nilai: ${item.skor_akhir ?? "-"}
                            </p>
                        </div>

                        <button
                            type="button"
                            class="btn-detail-riwayat rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700"
                            data-id-riwayat="${item.id_riwayat_tes}"
                        >
                            Detail
                        </button>

                    </div>
                `;

                container.appendChild(div);
            });

            // Pasang event untuk tombol detail
            document
                .querySelectorAll(".btn-detail-riwayat")
                .forEach((button) => {
                    button.addEventListener("click", () => {
                        lihatDetailRiwayat(button.dataset.idRiwayat);
                    });
                });

            showAlert(response.data.message, "success");
        } catch (error) {
            console.error("Error mengambil riwayat:", error);

            if (error.response) {
                showAlert(
                    error.response.data.message || "Gagal mengambil riwayat.",
                    "error",
                );
            } else {
                showAlert("Tidak dapat terhubung ke server.", "error");
            }
        } finally {
            btnRiwayat.disabled = false;
            btnRiwayat.textContent = "Lihat Riwayat";
        }
    });
}

// =====================================================
// DETAIL RIWAYAT
// =====================================================

async function lihatDetailRiwayat(idRiwayat) {
    try {
        const response = await axios.get(`/api/riwayat-test/${idRiwayat}`);

        console.log("Response detail riwayat:", response.data);

        const data = response.data.data;

        const container = document.getElementById("detail-riwayat-container");

        container.innerHTML = "";

        if (!data.jawaban || data.jawaban.length === 0) {
            container.innerHTML = `
                <div class="rounded-lg bg-gray-100 p-4 text-center text-gray-600">
                    Belum ada jawaban.
                </div>
            `;
        } else {
            data.jawaban.forEach((item, index) => {
                const div = document.createElement("div");

                div.className = "mb-4 rounded-lg border border-gray-200 p-4";

                div.innerHTML = `
                    <p class="font-semibold text-gray-800">
                        Soal ${index + 1}
                    </p>

                    <p class="mt-2 text-gray-700">
                        ${item.soal?.pertanyaan ?? "-"}
                    </p>

                    <p class="mt-2 text-sm text-gray-600">
                        Jawaban: 
                        <strong>
                            ${item.jawaban_siswa}
                        </strong>
                    </p>
                `;

                container.appendChild(div);
            });
        }

        document
            .getElementById("detail-riwayat-section")
            .classList.remove("hidden");

        document.getElementById("detail-riwayat-section").scrollIntoView({
            behavior: "smooth",
        });
    } catch (error) {
        console.error("Error detail riwayat:", error);

        if (error.response) {
            showAlert(
                error.response.data.message ||
                    "Gagal mengambil detail riwayat.",
                "error",
            );
        } else {
            showAlert("Tidak dapat terhubung ke server.", "error");
        }
    }
}
