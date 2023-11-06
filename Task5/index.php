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
                    <h2>Upload Two KML or GPX Files</h2>
                    <h3>To Compare Similarity of Tracks</h3>
                    <label class="upload-label" for="file-upload1">Choose File 1</label>
                    <input type="file" id="file-upload1" class="upload-input" name="file1" accept=".kml,.gpx" required>
                    <div class="file-name" id="file-name1"></div>

                    <label class="upload-label" for="file-upload2">Choose File 2</label>
                    <input type="file" id="file-upload2" class="upload-input" name="file2" accept=".kml,.gpx" required>
                    <div class="file-name" id="file-name2"></div>

                    <button type="submit" class="custom-button">Upload</button>
                </form>
            </div>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>

</html>