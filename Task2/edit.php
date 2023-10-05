<?php

class Edit
{
    public $json;
    public $filePath;

    public function __construct()
    {
        $this->filePath = 'files/out/parsed_' . $_GET['file'] . '.json';
        $this->json = json_decode(file_get_contents($this->filePath), true);
    }

    public function deleteRow($index)
    {
        if (isset($this->json[$index])) {
            unset($this->json[$index]);
            $json_output = json_encode($this->json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($this->filePath, $json_output);
        }
    }

    public function displayJSON($json)
    {
        if (empty($json)) {
            echo "<p>Json is empty!</p>";
            return;
        }

        $header = array_keys($json[0]);
        $header[] = "Action";
        $table_data = array_values($json);
        echo "<form method='post'>";
        echo "<table class=\"table\">";
        echo "<tr>";
        foreach ($header as $cell) {
            echo "<th>$cell</th>";
        }
        echo "</tr>";
        foreach ($table_data as $index => $row) {
            echo "<tr>";
            foreach ($row as $key => $cell) {
                echo "<td><input type='text' class=\"input-text\" name='json[$index][$key]' value='$cell'></td>";
            }
            echo "<td>";
            echo "<button class=\"del-button\" name=\"delete\" value=\"$index\">Delete</button>";
            echo "<input type='hidden' name='rowIndex' value='$index'>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div class=\"button-container\">";
        echo "<button class=\"custom-button\" type=\"submit\" name=\"action\">Save</button>";
        echo "<input type=\"hidden\" name=\"action1\">";
        echo "<button type=\"submit\" class=\"custom-button\">Reload JSON</button>";
        echo "</div>";
        echo "</form>";
    }

    public function saveJSON($json)
    {
        $json_output = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($this->filePath, $json_output);
    }
}

$edit = new Edit();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete']) && isset($_POST['rowIndex'])) {
        $index = $_POST['rowIndex'];
        $edit->deleteRow($index);
        header('Location: edit.php?file=' . $_GET['file']);
        exit();
    }

    if (isset($_POST['action'])) {
        $edit->json = $_POST['json'];
        $edit->saveJSON($edit->json);
        header('Location: edit.php?file=' . $_GET['file']);
        exit();
    }
    if (isset($_POST['action1'])) {
        header('Location: edit.php?file=' . $_GET['file']);
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSON Edit</title>
    <link rel="stylesheet" href="css/edit.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <h1>JSON Data</h1>
    <div class="container">
        <div>
            <?php $edit->displayJSON($edit->json); ?>
        </div>
        <div>
            <button type="button" onclick="location.href='index.php';" class="custom-button">Exit</button>
        </div>
    </div>
</body>

</html>