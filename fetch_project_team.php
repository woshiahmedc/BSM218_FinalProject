<?php
require_once 'db_config.php';
$conn = get_db_connection();

if (isset($_GET['proje_id'])) {
    $projeID = intval($_GET['proje_id']);

    // Proje ekibini getiren saklı yordamı çağır
    // sp_ProjeDetayGetir birden fazla sonuç seti döndürdüğü için dikkatli olmalıyız.
    // İlk sonuç seti proje detayları, ikinci sonuç seti proje ekibi.
    // Sadece proje ekibini almak için bu şekilde özel bir işlem yapabiliriz.
    $stmt = $conn->prepare("CALL sp_ProjeDetayGetir(?)");
    if ($stmt) {
        $stmt->bind_param("i", $projeID);
        $stmt->execute();

        // İlk sonuç setini atla (Proje Bilgileri)
        $stmt->get_result()->free();
        $conn->next_result();

        // İkinci sonuç setini al (Proje Ekibi)
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            echo "<table class='min-w-full bg-white border border-gray-200 rounded-lg shadow-sm'>";
            echo "<thead><tr><th class='py-2 px-4 border-b'>Araştırmacı Adı</th><th class='py-2 px-4 border-b'>Rol</th></tr></thead>";
            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['Adi'] . " " . $row['Soyadi']) . "</td>";
                echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['Rol']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            $result->free();
        } else {
            echo "<p class='text-gray-600'>Bu projede henüz bir ekip üyesi bulunmamaktadır.</p>";
        }
        $stmt->close();
    } else {
        echo "<p class='text-red-500'>Stored procedure hazırlanırken hata oluştu: " . $conn->error . "</p>";
    }
} else {
    echo "<p class='text-red-500'>Proje ID sağlanmadı.</p>";
}
$conn->close();
?>
