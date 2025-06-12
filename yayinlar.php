<?php
require_once 'db_config.php';
$conn = get_db_connection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yayın Yönetimi</title>
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
        input[type="text"], input[type="date"], select {
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
        <h1 class="text-3xl font-bold mb-6 text-center">Yayın Yönetimi</h1>

        <?php
        // Yayın Ekle/Güncelle/Sil işlemleri
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Yayın Ekle
            if (isset($_POST["add_publication"])) {
                $projeID = empty($_POST['proje_id']) ? NULL : $_POST['proje_id']; // ProjeID boş ise NULL
                $baslik = $_POST['baslik'];
                $yayinTarihi = $_POST['yayin_tarihi'];
                $turu = $_POST['turu'];
                $dergiAdi = $_POST['dergi_adi'];
                $doi = $_POST['doi'];

                $stmt = $conn->prepare("CALL sp_YayinEkle(?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    // ProjeID NULL olabileceğinden, bind_param'da 'i' yerine 's' kullanabiliriz ve null değeri gönderebiliriz.
                    // Ya da int için null kontrolü yapıp bağlamadan önce ayarlayabiliriz.
                    // Ancak MySQL prosedürlerinde NULL int olarak doğrudan geçirilebilir.
                    $stmt->bind_param("isssss", $projeID, $baslik, $yayinTarihi, $turu, $dergiAdi, $doi);
                    if ($stmt->execute()) {
                        echo "<div class='text-green-500 font-semibold mb-4'>Yayın başarıyla eklendi!</div>";
                    } else {
                        echo "<div class='text-red-500 font-semibold mb-4'>Yayın eklenirken hata oluştu: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                    clear_mysqli_results($conn);
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
                }
            }

            // Yayın Güncelle
            if (isset($_POST["update_publication"])) {
                $yayinID = $_POST['edit_yayin_id'];
                $projeID = empty($_POST['edit_proje_id']) ? NULL : $_POST['edit_proje_id'];
                $baslik = $_POST['edit_baslik'];
                $yayinTarihi = $_POST['edit_yayin_tarihi'];
                $turu = $_POST['edit_turu'];
                $dergiAdi = $_POST['edit_dergi_adi'];
                $doi = $_POST['edit_doi'];

                $stmt = $conn->prepare("CALL sp_YayinGuncelle(?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("iisssss", $yayinID, $projeID, $baslik, $yayinTarihi, $turu, $dergiAdi, $doi);
                    if ($stmt->execute()) {
                        echo "<div class='text-green-500 font-semibold mb-4'>Yayın başarıyla güncellendi!</div>";
                    } else {
                        echo "<div class='text-red-500 font-semibold mb-4'>Yayın güncellenirken hata oluştu: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                    clear_mysqli_results($conn);
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
                }
            }

            // Yayın Sil
            if (isset($_POST["delete_publication"])) {
                $yayinID_to_delete = $_POST['delete_yayin_id'];

                $stmt = $conn->prepare("CALL sp_YayinSil(?)");
                if ($stmt) {
                    $stmt->bind_param("i", $yayinID_to_delete);
                    if ($stmt->execute()) {
                        echo "<div class='text-green-500 font-semibold mb-4'>Yayın başarıyla silindi!</div>";
                    } else {
                        echo "<div class='text-red-500 font-semibold mb-4'>Yayın silinirken hata oluştu: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                    clear_mysqli_results($conn);
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
                }
            }
        }
        ?>

        <h2 class="text-2xl font-semibold mb-4">Yeni Yayın Ekle</h2>
        <form method="post" action="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="proje_id" class="block text-sm font-medium text-gray-700">İlgili Proje (isteğe bağlı):</label>
                    <select id="proje_id" name="proje_id" class="mt-1">
                        <option value="">-- Proje Seçiniz --</option>
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
                    <label for="baslik" class="block text-sm font-medium text-gray-700">Başlık:</label>
                    <input type="text" id="baslik" name="baslik" required class="mt-1">
                </div>
                <div>
                    <label for="yayin_tarihi" class="block text-sm font-medium text-gray-700">Yayın Tarihi:</label>
                    <input type="date" id="yayin_tarihi" name="yayin_tarihi" required class="mt-1">
                </div>
                <div>
                    <label for="turu" class="block text-sm font-medium text-gray-700">Türü:</label>
                    <select id="turu" name="turu" required class="mt-1">
                        <option value="Makale">Makale</option>
                        <option value="Bildiri">Bildiri</option>
                        <option value="Kitap">Kitap</option>
                        <option value="Tez">Tez</option>
                    </select>
                </div>
                <div>
                    <label for="dergi_adi" class="block text-sm font-medium text-gray-700">Dergi Adı:</label>
                    <input type="text" id="dergi_adi" name="dergi_adi" class="mt-1">
                </div>
                <div>
                    <label for="doi" class="block text-sm font-medium text-gray-700">DOI:</label>
                    <input type="text" id="doi" name="doi" class="mt-1">
                </div>
            </div>
            <button type="submit" name="add_publication" class="mt-4 w-full">Yayın Ekle</button>
        </form>

        <h2 class="text-2xl font-semibold mt-8 mb-4">Yayınlar Listesi</h2>
        <?php
        clear_mysqli_results($conn);
        $result = $conn->query("CALL sp_YayinlariListele()");

        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<thead><tr><th>ID</th><th>Proje Adı</th><th>Başlık</th><th>Yayın Tarihi</th><th>Türü</th><th>Dergi Adı</th><th>DOI</th><th>İşlemler</th></tr></thead>";
            echo "<tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["YayinID"] . "</td>";
                echo "<td>" . htmlspecialchars($row["ProjeAdi"] ?? 'Yok') . "</td>"; // ProjeID NULL olabilir
                echo "<td>" . htmlspecialchars($row["Baslik"]) . "</td>";
                echo "<td>" . $row["YayinTarihi"] . "</td>";
                echo "<td>" . htmlspecialchars($row["Turu"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["DergiAdi"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["DOI"]) . "</td>";
                echo "<td>
                        <button onclick='openEditModal(" . $row["YayinID"] . ", " . ($row["ProjeAdi"] ? $row["ProjeID"] : 'null') . ", \"" . htmlspecialchars($row["Baslik"], ENT_QUOTES) . "\", \"" . $row["YayinTarihi"] . "\", \"" . htmlspecialchars($row["Turu"], ENT_QUOTES) . "\", \"" . htmlspecialchars($row["DergiAdi"], ENT_QUOTES) . "\", \"" . htmlspecialchars($row["DOI"], ENT_QUOTES) . "\")' class='bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm'>Düzenle</button>
                        <button onclick='showConfirmModal(" . $row["YayinID"] . ")' class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm ml-2'>Sil</button>
                      </td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>Henüz kayıtlı yayın bulunmamaktadır.</p>";
        }
        ?>
    </div>

    <!-- Edit Publication Modal -->
    <div id="editPublicationModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeEditModal()">&times;</span>
            <h2 class="text-2xl font-semibold mb-4">Yayın Bilgilerini Düzenle</h2>
            <form method="post" action="">
                <input type="hidden" id="edit_yayin_id" name="edit_yayin_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_proje_id" class="block text-sm font-medium text-gray-700">İlgili Proje (isteğe bağlı):</label>
                        <select id="edit_proje_id" name="edit_proje_id" class="mt-1">
                            <option value="">-- Proje Seçiniz --</option>
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
                        <label for="edit_baslik" class="block text-sm font-medium text-gray-700">Başlık:</label>
                        <input type="text" id="edit_baslik" name="edit_baslik" required class="mt-1">
                    </div>
                    <div>
                        <label for="edit_yayin_tarihi" class="block text-sm font-medium text-gray-700">Yayın Tarihi:</label>
                        <input type="date" id="edit_yayin_tarihi" name="edit_yayin_tarihi" required class="mt-1">
                    </div>
                    <div>
                        <label for="edit_turu" class="block text-sm font-medium text-gray-700">Türü:</label>
                        <select id="edit_turu" name="edit_turu" required class="mt-1">
                            <option value="Makale">Makale</option>
                            <option value="Bildiri">Bildiri</option>
                            <option value="Kitap">Kitap</option>
                            <option value="Tez">Tez</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_dergi_adi" class="block text-sm font-medium text-gray-700">Dergi Adı:</label>
                        <input type="text" id="edit_dergi_adi" name="edit_dergi_adi" class="mt-1">
                    </div>
                    <div>
                        <label for="edit_doi" class="block text-sm font-medium text-gray-700">DOI:</label>
                        <input type="text" id="edit_doi" name="edit_doi" class="mt-1">
                    </div>
                </div>
                <button type="submit" name="update_publication" class="mt-4 w-full bg-green-500 hover:bg-green-600">Güncelle</button>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal for Delete -->
    <div id="confirmDeleteModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <h2 class="text-xl font-semibold mb-4">Silme Onayı</h2>
            <p class="mb-6">Bu yayını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
            <div class="confirm-modal-buttons">
                <form method="post" action="" style="display:inline;">
                    <input type="hidden" id="delete_yayin_id" name="delete_yayin_id">
                    <button type="submit" name="delete_publication" class="bg-red-500 hover:bg-red-600">Evet, Sil</button>
                </form>
                <button type="button" onclick="closeConfirmModal()" class="bg-gray-500 hover:bg-gray-600">İptal</button>
            </div>
        </div>
    </div>

    <script>
        var editModal = document.getElementById("editPublicationModal");
        var confirmModal = document.getElementById("confirmDeleteModal");

        function openEditModal(id, projeID, baslik, yayinTarihi, turu, dergiAdi, doi) {
            document.getElementById("edit_yayin_id").value = id;
            document.getElementById("edit_proje_id").value = projeID;
            document.getElementById("edit_baslik").value = baslik;
            document.getElementById("edit_yayin_tarihi").value = yayinTarihi;
            document.getElementById("edit_turu").value = turu;
            document.getElementById("edit_dergi_adi").value = dergiAdi;
            document.getElementById("edit_doi").value = doi;
            editModal.style.display = "flex";
        }

        function closeEditModal() {
            editModal.style.display = "none";
        }

        function showConfirmModal(id) {
            document.getElementById("delete_yayin_id").value = id;
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
