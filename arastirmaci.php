<?php
// db_config.php dosyasını dahil et
require_once 'db_config.php';
$conn = get_db_connection(); // Veritabanı bağlantısını al

// Genel CSS stillerini buraya da ekleyebilirsiniz veya ayrı bir CSS dosyası kullanabilirsiniz.
// Şimdilik index.php'deki stilleri bu dosyaya taşıyorum.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araştırmacı Yönetimi</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
            color: #374151; /* Dark gray text */
        }
        .container {
            max-width: 960px;
            margin: 2rem auto;
            padding: 1.5rem;
            background-color: #ffffff; /* White container background */
            border-radius: 0.75rem; /* Rounded corners */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Subtle shadow */
        }
        h1, h2 {
            color: #1f2937; /* Darker title text */
            margin-bottom: 1rem;
        }
        input[type="text"], input[type="email"], input[type="tel"] {
            border: 1px solid #d1d5db; /* Light gray border */
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem; /* Rounded input fields */
            width: 100%;
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
            margin-bottom: 0.75rem;
        }
        button {
            background-color: #3b82f6; /* Blue button */
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }
        button:hover {
            background-color: #2563eb; /* Darker blue on hover */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }
        th, td {
            border: 1px solid #e5e7eb; /* Lighter gray table border */
            padding: 0.75rem;
            text-align: left;
        }
        th {
            background-color: #e0e7ff; /* Light blue header background */
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f9fafb; /* Slightly different background for even rows */
        }
        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
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
            position: relative; /* For close button positioning */
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
        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .confirm-modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1001; /* Sit on top, higher than edit modal */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.5); /* Black w/ more opacity */
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
        <h1 class="text-3xl font-bold mb-6 text-center">Araştırmacı Yönetimi</h1>

        <?php
        // Araştırmacı ekleme form gönderimini ele al
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_researcher"])) {
            $adi = $_POST['adi'];
            $soyadi = $_POST['soyadi'];
            $unvani = $_POST['unvani'];
            $eposta = $_POST['eposta'];
            $telefon = $_POST['telefon'];

            // Saklı yordam çağrısını hazırla
            $stmt = $conn->prepare("CALL sp_ArastirmaciEkle(?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssss", $adi, $soyadi, $unvani, $eposta, $telefon);
                if ($stmt->execute()) {
                    echo "<div class='text-green-500 font-semibold mb-4'>Araştırmacı başarıyla eklendi!</div>";
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Araştırmacı eklenirken hata oluştu: " . $stmt->error . "</div>";
                }
                $stmt->close();
                clear_mysqli_results($conn); // Önemli: Bekleyen sonuçları temizle
            } else {
                echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
            }
        }

        // Araştırmacı güncelleme form gönderimini ele al
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_researcher"])) {
            $arastirmaciID = $_POST['edit_arastirmaciID'];
            $adi = $_POST['edit_adi'];
            $soyadi = $_POST['edit_soyadi'];
            $unvani = $_POST['edit_unvani'];
            $eposta = $_POST['edit_eposta'];
            $telefon = $_POST['edit_telefon'];

            // Saklı yordam çağrısını hazırla
            $stmt = $conn->prepare("CALL sp_ArastirmaciGuncelle(?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("isssss", $arastirmaciID, $adi, $soyadi, $unvani, $eposta, $telefon);
                if ($stmt->execute()) {
                    echo "<div class='text-green-500 font-semibold mb-4'>Araştırmacı başarıyla güncellendi!</div>";
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Araştırmacı güncellenirken hata oluştu: " . $stmt->error . "</div>";
                }
                $stmt->close();
                clear_mysqli_results($conn); // Önemli: Bekleyen sonuçları temizle
            } else {
                echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
            }
        }

        // Silme isteğini ele al
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_researcher"])) {
            $arastirmaciID_to_delete = $_POST['delete_arastirmaciID'];

            $stmt = $conn->prepare("CALL sp_ArastirmaciSil(?)");
            if ($stmt) {
                $stmt->bind_param("i", $arastirmaciID_to_delete);
                if ($stmt->execute()) {
                    echo "<div class='text-green-500 font-semibold mb-4'>Araştırmacı başarıyla silindi!</div>";
                } else {
                    echo "<div class='text-red-500 font-semibold mb-4'>Araştırmacı silinirken hata oluştu: " . $stmt->error . "</div>";
                }
                $stmt->close();
                clear_mysqli_results($conn); // Önemli: Bekleyen sonuçları temizle
            } else {
                echo "<div class='text-red-500 font-semibold mb-4'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</div>";
            }
        }
        ?>

        <h2 class="text-2xl font-semibold mb-4">Yeni Araştırmacı Ekle</h2>
        <form method="post" action="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="adi" class="block text-sm font-medium text-gray-700">Adı:</label>
                    <input type="text" id="adi" name="adi" required class="mt-1">
                </div>
                <div>
                    <label for="soyadi" class="block text-sm font-medium text-gray-700">Soyadı:</label>
                    <input type="text" id="soyadi" name="soyadi" required class="mt-1">
                </div>
                <div>
                    <label for="unvani" class="block text-sm font-medium text-gray-700">Unvanı:</label>
                    <input type="text" id="unvani" name="unvani" class="mt-1">
                </div>
                <div>
                    <label for="eposta" class="block text-sm font-medium text-gray-700">E-posta:</label>
                    <input type="email" id="eposta" name="eposta" required class="mt-1">
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="telefon" class="block text-sm font-medium text-gray-700">Telefon:</label>
                    <input type="tel" id="telefon" name="telefon" class="mt-1">
                </div>
            </div>
            <button type="submit" name="add_researcher" class="mt-4 w-full">Araştırmacı Ekle</button>
        </form>

        <h2 class="text-2xl font-semibold mt-8 mb-4">Araştırmacılar Listesi</h2>
        <?php
        // Araştırmacıları getir ve göster
        // Herhangi bir önceki saklı yordam çağrısından kalan sonuçları temizle
        clear_mysqli_results($conn);

        $result = $conn->query("CALL sp_ArastirmacilariListele()");

        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<thead><tr><th>ID</th><th>Adı</th><th>Soyadı</th><th>Unvanı</th><th>E-posta</th><th>Telefon</th><th>İşlemler</th></tr></thead>";
            echo "<tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["ArastirmaciID"] . "</td>";
                echo "<td>" . htmlspecialchars($row["Adi"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["Soyadi"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["Unvani"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["Eposta"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["Telefon"]) . "</td>";
                echo "<td>
                        <button onclick='openEditModal(" . $row["ArastirmaciID"] . ", \"" . htmlspecialchars($row["Adi"], ENT_QUOTES) . "\", \"" . htmlspecialchars($row["Soyadi"], ENT_QUOTES) . "\", \"" . htmlspecialchars($row["Unvani"], ENT_QUOTES) . "\", \"" . htmlspecialchars($row["Eposta"], ENT_QUOTES) . "\", \"" . htmlspecialchars($row["Telefon"], ENT_QUOTES) . "\")' class='bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm'>Düzenle</button>
                        <button onclick='showConfirmModal(" . $row["ArastirmaciID"] . ")' class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm ml-2'>Sil</button>
                      </td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>Henüz kayıtlı araştırmacı bulunmamaktadır.</p>";
        }

        // Bağlantıyı kapat
        $conn->close();
        ?>
    </div>

    <!-- Edit Researcher Modal -->
    <div id="editResearcherModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeEditModal()">&times;</span>
            <h2 class="text-2xl font-semibold mb-4">Araştırmacı Bilgilerini Düzenle</h2>
            <form method="post" action="">
                <input type="hidden" id="edit_arastirmaciID" name="edit_arastirmaciID">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_adi" class="block text-sm font-medium text-gray-700">Adı:</label>
                        <input type="text" id="edit_adi" name="edit_adi" required class="mt-1">
                    </div>
                    <div>
                        <label for="edit_soyadi" class="block text-sm font-medium text-gray-700">Soyadı:</label>
                        <input type="text" id="edit_soyadi" name="edit_soyadi" required class="mt-1">
                    </div>
                    <div>
                        <label for="edit_unvani" class="block text-sm font-medium text-gray-700">Unvanı:</label>
                        <input type="text" id="edit_unvani" name="edit_unvani" class="mt-1">
                    </div>
                    <div>
                        <label for="edit_eposta" class="block text-sm font-medium text-gray-700">E-posta:</label>
                        <input type="email" id="edit_eposta" name="edit_eposta" required class="mt-1">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label for="edit_telefon" class="block text-sm font-medium text-gray-700">Telefon:</label>
                        <input type="tel" id="edit_telefon" name="edit_telefon" class="mt-1">
                    </div>
                </div>
                <button type="submit" name="update_researcher" class="mt-4 w-full bg-green-500 hover:bg-green-600">Güncelle</button>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal for Delete -->
    <div id="confirmDeleteModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <h2 class="text-xl font-semibold mb-4">Silme Onayı</h2>
            <p class="mb-6">Bu araştırmacıyı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
            <div class="confirm-modal-buttons">
                <form method="post" action="" style="display:inline;">
                    <input type="hidden" id="delete_arastirmaciID" name="delete_arastirmaciID">
                    <button type="submit" name="delete_researcher" class="bg-red-500 hover:bg-red-600">Evet, Sil</button>
                </form>
                <button type="button" onclick="closeConfirmModal()" class="bg-gray-500 hover:bg-gray-600">İptal</button>
            </div>
        </div>
    </div>

    <script>
        // Get the modals
        var editModal = document.getElementById("editResearcherModal");
        var confirmModal = document.getElementById("confirmDeleteModal");

        // Function to open the edit modal and populate form fields
        function openEditModal(id, adi, soyadi, unvani, eposta, telefon) {
            document.getElementById("edit_arastirmaciID").value = id;
            document.getElementById("edit_adi").value = adi;
            document.getElementById("edit_soyadi").value = soyadi;
            document.getElementById("edit_unvani").value = unvani;
            document.getElementById("edit_eposta").value = eposta;
            document.getElementById("edit_telefon").value = telefon;
            editModal.style.display = "flex"; // Use flex to center the modal
        }

        // Function to close the edit modal
        function closeEditModal() {
            editModal.style.display = "none";
        }

        // Function to show the confirmation modal for delete
        function showConfirmModal(id) {
            document.getElementById("delete_arastirmaciID").value = id;
            confirmModal.style.display = "flex";
        }

        // Function to close the confirmation modal
        function closeConfirmModal() {
            confirmModal.style.display = "none";
        }

        // Close the modals if the user clicks outside of them
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
