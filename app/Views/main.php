<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base64 Image</title>
</head>
<body>
    <img id="base64image" src="" alt="Base64 Image">

    <script>
        // Ambil elemen gambar menggunakan id
        var img = document.getElementById('base64image');

        // String base64 yang ingin Anda tampilkan sebagai gambar
        var base64String = "<?= $dataBase64; ?>";

        // Set src gambar dengan string base64
        img.src = base64String;
    </script>
</body>
</html>
