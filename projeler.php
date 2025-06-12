<?php
require_once 'db_config.php';
$conn = get_db_connection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proje Yönetimi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #374151;
        }
        .container {
            max-width: 960px;
            margin: 2rem auto;
            padding: 1.5rem;
            background-color: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        h1, h2 {
            color: #1f2937;
            margin-bottom: 1rem;
        }
        input[type="text"], input[type="date"], input[type="number"], select, textarea {
            border: 1px solid #d1d5db;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 0.75rem;
        }
        button {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }
        button:hover {
            background-color: #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 0.75rem;
            text-align: left;
        }
        th {
            background-color: #e0e7ff;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 600px;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
        }
        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
        }
        .close-button:hover, .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .confirm-modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .confirm-modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 400px;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            text-align: center;
        }
        .confirm-modal-buttons button {
            margin: 0.5rem;
        }
        .back-button {
            background-color: #6b7280;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.2s ease-in-out;
            margin-bottom: 1.5rem;
            display: inline-block;
        }
        .back-button:hover {
            background-color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-button">← Ana Sayfaya Dön</a>
        <h1 class="text-3xl font-bold mb-6 text-center">Proje Yönetimi</h1>

        <?php
        // Proje Ekle/Güncelle/Sil işlemleri
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Proje Ekle
            if (isset($_POST["add_project"])) {
                $projeAdi = $_POST['proje_adi'];
                $baslangicTarihi = $_POST['baslangic_tarihi'];
                $bitisTarihi = $_POST['bitis_tarihi'];
                $butce = $_POST['butce'];
                $yurutucuArastirmaciID = $_POST['yurutucu_arastirmaci_id'];

                $stmt = $conn->prepare("CALL sp_ProjeVeYurutucuEkle(?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sssdi", $projeAdi, $baslangicTarihi, $bitisTarihi, $butce, $yurutucuArastirmaciID);
                    if ($stmt->execute()) {
                        echo "<div class='text-green-500 font-semibold mb-4'>Proje başarıyla eklendi!</div>";
                    } else {
                        echo "<div class='text-red-500 font-semibold mb-4'>Proje eklenirken hata oluştu: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                    clear_mysqli_results($conn);
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
                }
            }

            // Proje Güncelle
            if (isset($_POST["update_project"])) {
                $projeID = $_POST['edit_proje_id'];
                $projeAdi = $_POST['edit_proje_adi'];
                $baslangicTarihi = $_POST['edit_baslangic_tarihi'];
                $bitisTarihi = $_POST['edit_bitis_tarihi'];
                $butce = $_POST['edit_butce'];
                $durumu = $_POST['edit_durumu'];

                $stmt = $conn->prepare("CALL sp_ProjeGuncelle(?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("isssds", $projeID, $projeAdi, $baslangicTarihi, $bitisTarihi, $butce, $durumu);
                    if ($stmt->execute()) {
                        echo "<div class='text-green-500 font-semibold mb-4'>Proje başarıyla güncellendi!</div>";
                    } else {
                        echo "<div class='text-red-500 font-semibold mb-4'>Proje güncellenirken hata oluştu: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                    clear_mysqli_results($conn);
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
                }
            }

            // Proje Sil
            if (isset($_POST["delete_project"])) {
                $projeID_to_delete = $_POST['delete_proje_id'];

                $stmt = $conn->prepare("CALL sp_ProjeSil(?)");
                if ($stmt) {
                    $stmt->bind_param("i", $projeID_to_delete);
                    if ($stmt->execute()) {
                        echo "<div class='text-green-500 font-semibold mb-4'>Proje başarıyla silindi!</div>";
                    } else {
                        echo "<div class='text-red-500 font-semibold mb-4'>Proje silinirken hata oluştu: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                    clear_mysqli_results($conn);
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
                }
            }

            // Projeye Araştırmacı Ata
            if (isset($_POST["assign_researcher"])) {
                $projeID = $_POST['assign_proje_id'];
                $arastirmaciID = $_POST['assign_arastirmaci_id'];
                $rol = $_POST['assign_rol'];

                $stmt = $conn->prepare("CALL sp_ProjeyeArastirmaciAta(?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("iis", $projeID, $arastirmaciID, $rol);
                    if ($stmt->execute()) {
                        echo "<div class='text-green-500 font-semibold mb-4'>Araştırmacı projeye başarıyla atandı!</div>";
                    } else {
                        echo "<div class='text-red-500 font-semibold mb-4'>Araştırmacı atanırken hata oluştu: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                    clear_mysqli_results($conn);
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
                }
            }
        }
        ?>

        <h2 class="text-2xl font-semibold mb-4">Yeni Proje Ekle</h2>
        <form method="post" action="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="proje_adi" class="block text-sm font-medium text-gray-700">Proje Adı:</label>
                    <input type="text" id="proje_adi" name="proje_adi" required class="mt-1">
                </div>
                <div>
                    <label for="baslangic_tarihi" class="block text-sm font-medium text-gray-700">Başlangıç Tarihi:</label>
                    <input type="date" id="baslangic_tarihi" name="baslangic_tarihi" required class="mt-1">
                </div>
                <div>
                    <label for="bitis_tarihi" class="block text-sm font-medium text-gray-700">Bitiş Tarihi:</label>
                    <input type="date" id="bitis_tarihi" name="bitis_tarihi" required class="mt-1">
                </div>
                <div>
                    <label for="butce" class="block text-sm font-medium text-gray-700">Bütçe:</label>
                    <input type="number" step="0.01" id="butce" name="butce" class="mt-1">
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="yurutucu_arastirmaci_id" class="block text-sm font-medium text-gray-700">Yürütücü Araştırmacı:</label>
                    <select id="yurutucu_arastirmaci_id" name="yurutucu_arastirmaci_id" required class="mt-1">
                        <option value="">Seçiniz...</option>
                        <?php
                        clear_mysqli_results($conn);
                        $researchers_result = $conn->query("CALL sp_ArastirmacilariListele()");
                        if ($researchers_result && $researchers_result->num_rows > 0) {
                            while ($row = $researchers_result->fetch_assoc()) {
                                echo "<option value='" . $row['ArastirmaciID'] . "'>" . htmlspecialchars($row['Adi'] . " " . $row['Soyadi']) . "</option>";
                            }
                            $researchers_result->free();
                        }
                        clear_mysqli_results($conn);
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="add_project" class="mt-4 w-full">Proje Ekle</button>
        </form>

        <h2 class="text-2xl font-semibold mt-8 mb-4">Projeler Listesi</h2>
        <?php
        clear_mysqli_results($conn);
        $result = $conn->query("CALL sp_ProjeleriListele()");

        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<thead><tr><th>ID</th><th>Proje Adı</th><th>Başlangıç</th><th>Bitiş</th><th>Bütçe</th><th>Durum</th><th>İşlemler</th></tr></thead>";
            echo "<tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["ProjeID"] . "</td>";
                echo "<td>" . htmlspecialchars($row["ProjeAdi"]) . "</td>";
                echo "<td>" . $row["BaslangicTarihi"] . "</td>";
                echo "<td>" . $row["BitisTarihi"] . "</td>";
                echo "<td>" . number_format($row["Butce"], 2) . "</td>";
                echo "<td>" . htmlspecialchars($row["Durumu"]) . "</td>";
                echo "<td>
                        <button onclick='openEditModal(" . $row["ProjeID"] . ", \"" . htmlspecialchars($row["ProjeAdi"], ENT_QUOTES) . "\", \"" . $row["BaslangicTarihi"] . "\", \"" . $row["BitisTarihi"] . "\", " . $row["Butce"] . ", \"" . $row["Durumu"] . "\")' class='bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm'>Düzenle</button>
                        <button onclick='openAssignModal(" . $row["ProjeID"] . ", \"" . htmlspecialchars($row["ProjeAdi"], ENT_QUOTES) . "\")' class='bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm ml-2'>Ekip Ata</button>
                        <button onclick='showConfirmModal(" . $row["ProjeID"] . ")' class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm ml-2'>Sil</button>
                      </td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>Henüz kayıtlı proje bulunmamaktadır.</p>";
        }
        ?>
    </div>

    <!-- Edit Project Modal -->
    <div id="editProjectModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeEditModal()">&times;</span>
            <h2 class="text-2xl font-semibold mb-4">Proje Bilgilerini Düzenle</h2>
            <form method="post" action="">
                <input type="hidden" id="edit_proje_id" name="edit_proje_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_proje_adi" class="block text-sm font-medium text-gray-700">Proje Adı:</label>
                        <input type="text" id="edit_proje_adi" name="edit_proje_adi" required class="mt-1">
                    </div>
                    <div>
                        <label for="edit_baslangic_tarihi" class="block text-sm font-medium text-gray-700">Başlangıç Tarihi:</label>
                        <input type="date" id="edit_baslangic_tarihi" name="edit_baslangic_tarihi" required class="mt-1">
                    </div>
                    <div>
                        <label for="edit_bitis_tarihi" class="block text-sm font-medium text-gray-700">Bitiş Tarihi:</label>
                        <input type="date" id="edit_bitis_tarihi" name="edit_bitis_tarihi" required class="mt-1">
                    </div>
                    <div>
                        <label for="edit_butce" class="block text-sm font-medium text-gray-700">Bütçe:</label>
                        <input type="number" step="0.01" id="edit_butce" name="edit_butce" class="mt-1">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label for="edit_durumu" class="block text-sm font-medium text-gray-700">Durumu:</label>
                        <select id="edit_durumu" name="edit_durumu" required class="mt-1">
                            <option value="Planlanıyor">Planlanıyor</option>
                            <option value="Devam Ediyor">Devam Ediyor</option>
                            <option value="Tamamlandı">Tamamlandı</option>
                            <option value="İptal Edildi">İptal Edildi</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="update_project" class="mt-4 w-full bg-green-500 hover:bg-green-600">Güncelle</button>
            </form>
        </div>
    </div>

    <!-- Assign Researcher Modal -->
    <div id="assignResearcherModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeAssignModal()">&times;</span>
            <h2 class="text-2xl font-semibold mb-4">Projeye Araştırmacı Ata</h2>
            <form method="post" action="">
                <input type="hidden" id="assign_proje_id" name="assign_proje_id">
                <p class="mb-4">Seçili Proje: <span id="assign_proje_adi" class="font-semibold"></span></p>
                <div>
                    <label for="assign_arastirmaci_id" class="block text-sm font-medium text-gray-700">Araştırmacı:</label>
                    <select id="assign_arastirmaci_id" name="assign_arastirmaci_id" required class="mt-1">
                        <option value="">Seçiniz...</option>
                        <?php
                        // Modalı açtığımızda dinamik olarak yükleyebilmek için burada da araştırmacıları çekiyoruz.
                        // Alternatif olarak, JS ile AJAX isteği yapılabilir.
                        clear_mysqli_results($conn);
                        $researchers_assign_result = $conn->query("CALL sp_ArastirmacilariListele()");
                        if ($researchers_assign_result && $researchers_assign_result->num_rows > 0) {
                            while ($row = $researchers_assign_result->fetch_assoc()) {
                                echo "<option value='" . $row['ArastirmaciID'] . "'>" . htmlspecialchars($row['Adi'] . " " . $row['Soyadi']) . "</option>";
                            }
                            $researchers_assign_result->free();
                        }
                        clear_mysqli_results($conn);
                        ?>
                    </select>
                </div>
                <div>
                    <label for="assign_rol" class="block text-sm font-medium text-gray-700">Rol:</label>
                    <input type="text" id="assign_rol" name="assign_rol" required class="mt-1" placeholder="Örn: Yürütücü, Araştırmacı, Danışman">
                </div>
                <button type="submit" name="assign_researcher" class="mt-4 w-full bg-blue-500 hover:bg-blue-600">Araştırmacı Ata</button>
            </form>
            <h3 class="text-xl font-semibold mt-6 mb-3">Proje Ekibi</h3>
            <div id="projectTeamList" class="overflow-x-auto">
                <!-- Proje ekibi AJAX ile yüklenecek -->
            </div>
        </div>
    </div>


    <!-- Confirmation Modal for Delete -->
    <div id="confirmDeleteModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <h2 class="text-xl font-semibold mb-4">Silme Onayı</h2>
            <p class="mb-6">Bu projeyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
            <div class="confirm-modal-buttons">
                <form method="post" action="" style="display:inline;">
                    <input type="hidden" id="delete_proje_id" name="delete_proje_id">
                    <button type="submit" name="delete_project" class="bg-red-500 hover:bg-red-600">Evet, Sil</button>
                </form>
                <button type="button" onclick="closeConfirmModal()" class="bg-gray-500 hover:bg-gray-600">İptal</button>
            </div>
        </div>
    </div>

    <script>
        var editModal = document.getElementById("editProjectModal");
        var assignModal = document.getElementById("assignResearcherModal");
        var confirmModal = document.getElementById("confirmDeleteModal");

        function openEditModal(id, adi, baslangic, bitis, butce, durumu) {
            document.getElementById("edit_proje_id").value = id;
            document.getElementById("edit_proje_adi").value = adi;
            document.getElementById("edit_baslangic_tarihi").value = baslangic;
            document.getElementById("edit_bitis_tarihi").value = bitis;
            document.getElementById("edit_butce").value = butce;
            document.getElementById("edit_durumu").value = durumu;
            editModal.style.display = "flex";
        }

        function closeEditModal() {
            editModal.style.display = "none";
        }

        function openAssignModal(projeID, projeAdi) {
            document.getElementById("assign_proje_id").value = projeID;
            document.getElementById("assign_proje_adi").textContent = projeAdi;
            assignModal.style.display = "flex";
            loadProjectTeam(projeID); // Proje ekibini yükle
        }

        function closeAssignModal() {
            assignModal.style.display = "none";
        }

        function showConfirmModal(id) {
            document.getElementById("delete_proje_id").value = id;
            confirmModal.style.display = "flex";
        }

        function closeConfirmModal() {
            confirmModal.style.display = "none";
        }

        function loadProjectTeam(projeID) {
            fetch('fetch_project_team.php?proje_id=' + projeID)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('projectTeamList').innerHTML = data;
                })
                .catch(error => console.error('Error fetching project team:', error));
        }

        window.onclick = function(event) {
            if (event.target == editModal) {
                editModal.style.display = "none";
            }
            if (event.target == assignModal) {
                assignModal.style.display = "none";
            }
            if (event.target == confirmModal) {
                confirmModal.style.display = "none";
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
