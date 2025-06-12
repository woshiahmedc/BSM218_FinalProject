<?php
// db_config.php - Veritabanı bağlantı detayları ve bağlantı fonksiyonu
$servername = "localhost"; // Genellikle XAMPP için localhost
$username = "root";        // Varsayılan XAMPP MySQL kullanıcı adı
$password = "";            // Varsayılan XAMPP MySQL şifresi (boş)
$dbname = "bilimsel_arastirma_db"; // Veritabanı adınız

/**
 * Veritabanı bağlantısını döndürür.
 * Hata durumunda bağlantıyı keser ve hata mesajı gösterir.
 * @return mysqli Veritabanı bağlantı nesnesi.
 */
function get_db_connection() {
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Bağlantıyı kontrol et
    if ($conn->connect_error) {
        die("<div class='text-red-500 font-semibold mb-4'>Veritabanı bağlantısı başarısız: " . $conn->connect_error . "</div>");
    }
    return $conn;
}

/**
 * Bekleyen tüm sonuç setlerini temizler.
 * MySQLi ile birden fazla sonuç seti döndüren saklı yordamlar arasında geçiş yaparken gereklidir.
 * @param mysqli $conn Veritabanı bağlantı nesnesi.
 */
function clear_mysqli_results($conn) {
    while ($conn->more_results() && $conn->next_result()) {
        $dummyResult = $conn->use_result();
        if ($dummyResult instanceof mysqli_result) {
            $dummyResult->free();
        }
    }
}
?>
