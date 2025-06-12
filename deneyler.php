<?php
require_once 'db_config.php';
$conn = get_db_connection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deney Yönetimi</title>
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
        input[type="text"], input[type="datetime-local"], select, textarea {
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
            max-width: 500px;
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
        <h1 class="text-3xl font-bold mb-6 text-center">Deney Yönetimi</h1>

        <?php
        // Deney Ekle/Güncelle/Sil işlemleri
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Deney Ekle
            if (isset($_POST["add_experiment"])) {
                $projeID = $_POST['proje_id'];
                $deneyAdi = $_POST['deney_adi'];
                $deneyTarihi = $_POST['deney_tarihi'];
                $aciklama = $_POST['aciklama'];

                $stmt = $conn->prepare("CALL sp_DeneyEkle(?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("isss", $projeID, $deneyAdi, $deneyTarihi, $aciklama);
                    if ($stmt->execute()) {
                        echo "<div class='text-green-500 font-semibold mb-4'>Deney başarıyla eklendi!</div>";
                    } else {
                        echo "<div class='text-red-500 font-semibold mb-4'>Deney eklenirken hata oluştu: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                    clear_mysqli_results($conn);
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
                }
            }

            // Deney Güncelle
            if (isset($_POST["update_experiment"])) {
                $deneyID = $_POST['edit_deney_id'];
                $projeID = $_POST['edit_proje_id'];
                $deneyAdi = $_POST['edit_deney_adi'];
                $deneyTarihi = $_POST['edit_deney_tarihi'];
                $aciklama = $_POST['edit_aciklama'];

                $stmt = $conn->prepare("CALL sp_DeneyGuncelle(?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("iisss", $deneyID, $projeID, $deneyAdi, $deneyTarihi, $aciklama);
                    if ($stmt->execute()) {
                        echo "<div class='text-green-500 font-semibold mb-4'>Deney başarıyla güncellendi!</div>";
                    } else {
                        echo "<div class='text-red-500 font-semibold mb-4'>Deney güncellenirken hata oluştu: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                    clear_mysqli_results($conn);
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
                }
            }

            // Deney Sil
            if (isset($_POST["delete_experiment"])) {
                $deneyID_to_delete = $_POST['delete_deney_id'];

                $stmt = $conn->prepare("CALL sp_DeneySil(?)");
                if ($stmt) {
                    $stmt->bind_param("i", $deneyID_to_delete);
                    if ($stmt->execute()) {
                        echo "<div class='text-green-500 font-semibold mb-4'>Deney başarıyla silindi!</div>";
                    } else {
                        echo "<div class='text-red-500 font-semibold mb-4'>Deney silinirken hata oluştu: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                    clear_mysqli_results($conn);
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
                }
            }
        }
        ?>

        <h2 class="text-2xl font-semibold mb-4">Yeni Deney Ekle</h2>
        <form method="post" action="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="proje_id" class="block text-sm font-medium text-gray-700">Proje:</label>
                    <select id="proje_id" name="proje_id" required class="mt-1">
                        <option value="">Seçiniz...</option>
                        <?php
                        clear_mysqli_results($conn);
                        $projects_result = $conn->query("CALL sp_ProjeleriListele()");
                        if ($projects_result && $projects_result->num_rows > 0) {
                            while ($row = $projects_result->fetch_assoc()) {
                                echo "<option value='" . $row['ProjeID'] . "'>" . htmlspecialchars($row['ProjeAdi']) . "</option>";
                            }
                            $projects_result->free();
                        }
                        clear_mysqli_results($conn);
                        ?>
                    </select>
                </div>
                <div>
                    <label for="deney_adi" class="block text-sm font-medium text-gray-700">Deney Adı:</label>
                    <input type="text" id="deney_adi" name="deney_adi" required class="mt-1">
                </div>
                <div>
                    <label for="deney_tarihi" class="block text-sm font-medium text-gray-700">Deney Tarihi:</label>
                    <input type="datetime-local" id="deney_tarihi" name="deney_tarihi" required class="mt-1">
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="aciklama" class="block text-sm font-medium text-gray-700">Açıklama:</label>
                    <textarea id="aciklama" name="aciklama" rows="3" class="mt-1"></textarea>
                </div>
            </div>
            <button type="submit" name="add_experiment" class="mt-4 w-full">Deney Ekle</button>
        </form>

        <h2 class="text-2xl font-semibold mt-8 mb-4">Deneyler Listesi</h2>
        <?php
        clear_mysqli_results($conn);
        $result = $conn->query("CALL sp_DeneyleriListele()");

        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<thead><tr><th>ID</th><th>Proje Adı</th><th>Deney Adı</th><th>Tarih</th><th>Açıklama</th><th>İşlemler</th></tr></thead>";
            echo "<tbody>";
            while($row = $result->fetch_assoc()) {
                // Deney Tarihini formatla (HTML datetime-local uyumu için)
                $deneyTarihiFormatted = date('Y-m-d\TH:i', strtotime($row["DeneyTarihi"]));
                echo "<tr>";
                echo "<td>" . $row["DeneyID"] . "</td>";
                echo "<td>" . htmlspecialchars($row["ProjeAdi"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["DeneyAdi"]) . "</td>";
                echo "<td>" . $row["DeneyTarihi"] . "</td>";
                echo "<td>" . htmlspecialchars($row["Aciklama"]) . "</td>";
                echo "<td>
                        <button onclick='openEditModal(" . $row["DeneyID"] . ", " . $row["ProjeID"] . ", \"" . htmlspecialchars($row["DeneyAdi"], ENT_QUOTES) . "\", \"" . $deneyTarihiFormatted . "\", \"" . htmlspecialchars($row["Aciklama"], ENT_QUOTES) . "\")' class='bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm'>Düzenle</button>
                        <button onclick='showConfirmModal(" . $row["DeneyID"] . ")' class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm ml-2'>Sil</button>
                      </td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>Henüz kayıtlı deney bulunmamaktadır.</p>";
        }
        ?>
    </div>

    <!-- Edit Experiment Modal -->
    <div id="editExperimentModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeEditModal()">&times;</span>
            <h2 class="text-2xl font-semibold mb-4">Deney Bilgilerini Düzenle</h2>
            <form method="post" action="">
                <input type="hidden" id="edit_deney_id" name="edit_deney_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_proje_id" class="block text-sm font-medium text-gray-700">Proje:</label>
                        <select id="edit_proje_id" name="edit_proje_id" required class="mt-1">
                            <option value="">Seçiniz...</option>
                            <?php
                            clear_mysqli_results($conn);
                            $projects_edit_result = $conn->query("CALL sp_ProjeleriListele()");
                            if ($projects_edit_result && $projects_edit_result->num_rows > 0) {
                                while ($row = $projects_edit_result->fetch_assoc()) {
                                    echo "<option value='" . $row['ProjeID'] . "'>" . htmlspecialchars($row['ProjeAdi']) . "</option>";
                                }
                                $projects_edit_result->free();
                            }
                            clear_mysqli_results($conn);
                            ?>
                        </select>
                    </div>
                    <div>
                        <label for="edit_deney_adi" class="block text-sm font-medium text-gray-700">Deney Adı:</label>
                        <input type="text" id="edit_deney_adi" name="edit_deney_adi" required class="mt-1">
                    </div>
                    <div>
                        <label for="edit_deney_tarihi" class="block text-sm font-medium text-gray-700">Deney Tarihi:</label>
                        <input type="datetime-local" id="edit_deney_tarihi" name="edit_deney_tarihi" required class="mt-1">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label for="edit_aciklama" class="block text-sm font-medium text-gray-700">Açıklama:</label>
                        <textarea id="edit_aciklama" name="edit_aciklama" rows="3" class="mt-1"></textarea>
                    </div>
                </div>
                <button type="submit" name="update_experiment" class="mt-4 w-full bg-green-500 hover:bg-green-600">Güncelle</button>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal for Delete -->
    <div id="confirmDeleteModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <h2 class="text-xl font-semibold mb-4">Silme Onayı</h2>
            <p class="mb-6">Bu deneyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
            <div class="confirm-modal-buttons">
                <form method="post" action="" style="display:inline;">
                    <input type="hidden" id="delete_deney_id" name="delete_deney_id">
                    <button type="submit" name="delete_experiment" class="bg-red-500 hover:bg-red-600">Evet, Sil</button>
                </form>
                <button type="button" onclick="closeConfirmModal()" class="bg-gray-500 hover:bg-gray-600">İptal</button>
            </div>
        </div>
    </div>

    <script>
        var editModal = document.getElementById("editExperimentModal");
        var confirmModal = document.getElementById("confirmDeleteModal");

        function openEditModal(id, projeID, adi, tarih, aciklama) {
            document.getElementById("edit_deney_id").value = id;
            document.getElementById("edit_proje_id").value = projeID;
            document.getElementById("edit_deney_adi").value = adi;
            document.getElementById("edit_deney_tarihi").value = tarih;
            document.getElementById("edit_aciklama").value = aciklama;
            editModal.style.display = "flex";
        }

        function closeEditModal() {
            editModal.style.display = "none";
        }

        function showConfirmModal(id) {
            document.getElementById("delete_deney_id").value = id;
            confirmModal.style.display = "flex";
        }

        function closeConfirmModal() {
            confirmModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == editModal) {
                editModal.style.display = "none";
            }
            if (event.target == confirmModal) {
                confirmModal.style.display = "none";
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
