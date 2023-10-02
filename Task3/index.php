<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Uploader</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="header">
        <h1>File Uploader</h1>
        <div class="container">
            <div class="upload-container" id="upload-container">
                <form method="post" action="parser.php" enctype="multipart/form-data">
                    <h2>Upload KML or GPX File</h2>
                    <label class="upload-label" for="file-upload">Choose File</label>
                    <input type="file" id="file-upload" class="upload-input" name="file" accept=".kml,.gpx" required>
                    <div class="file-name"></div>
                    <button type="submit" class="custom-button">Upload</button>
                </form>
            </div>
        </div>
        <script src="js/script.js"></script>
</body>

</html>