<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilimsel Araştırma Yönetim Sistemi - Ana Sayfa</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
            color: #374151; /* Dark gray text */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 960px;
            margin: 2rem auto;
            padding: 1.5rem;
            background-color: #ffffff; /* White container background */
            border-radius: 0.75rem; /* Rounded corners */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Subtle shadow */
            text-align: center;
        }
        h1 {
            color: #1f2937; /* Darker title text */
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }
        .navigation-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            max-width: 600px;
            margin: 0 auto;
        }
        .navigation-button {
            display: block;
            background-color: #3b82f6; /* Blue button */
            color: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
            transition: background-color 0.2s ease-in-out, transform 0.2s ease-in-out;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navigation-button:hover {
            background-color: #2563eb; /* Darker blue on hover */
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        @media (min-width: 768px) {
            .navigation-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bilimsel Araştırma Yönetim Sistemi</h1>
        <div class="navigation-grid">
            <a href="arastirmaci.php" class="navigation-button">Araştırmacı Yönetimi</a>
            <a href="projeler.php" class="navigation-button">Proje Yönetimi</a>
            <a href="deneyler.php" class="navigation-button">Deney Yönetimi</a>
            <a href="yayinlar.php" class="navigation-button">Yayın Yönetimi</a>
            <!-- Gelecekte eklenebilecek diğer modüller için linkler -->
            <!-- <a href="ekipmanlar.php" class="navigation-button">Ekipman Yönetimi</a> -->
            <!-- <a href="verisetleri.php" class="navigation-button">Veri Seti Yönetimi</a> -->
        </div>
    </div>
</body>
</html>
