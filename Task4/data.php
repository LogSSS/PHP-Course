<?php


$id = $_GET["id"];

switch ($id) {
    case 1:
        include 'files/out/out.html';
        break;
    case 2:

        break;
    case 3:
        outputTxt("files/in/month.txt", "files/out/month.txt");
        break;
    case 4:
        outputTable("files/in/dates.txt", "files/out/dates.txt", "Date");
        break;
    case 5:
        outputZoo("files/in/zoo.txt", "files/out/zoo.txt");
        break;
    case 6:
        outputTable("files/in/names.txt", "files/out/names.txt", "Names");
        break;
    default:
        pageNotFound();
}


function outputTxt($fileIn, $fileOut)
{

    $textIn = file_get_contents($fileIn);
    $textOut = file_get_contents($fileOut);

    echo HTMLheader() . "
    <body>
        <div class=\"frame\">
            <h3>Before</h1>
            <p>$textIn</p>
        </div>
        <div class=\"frame\">
            <h3>After</h3>
            <p>$textOut</p>
        </div>
        <div>
            <a href=\"index.php\" class=\"custom-button\">Back</a>
        </div>
    </body>

    </html>";
}

function outputTable($fileIn, $fileOut, $name)
{
    $textIn = file_get_contents($fileIn);
    $textOut = file_get_contents($fileOut);
    $textOut = explode("\n", $textOut);
    $table = "<table>
    <tr>
        <th>№</th>
        <th>$name</th>
    </tr>";

    foreach ($textOut as $key => $data) {
        $table .= "<tr>
            <td>" . ($key + 1) . "</td>
            <td>$data</td>
        </tr>";
    }

    $table .= "</table>";

    echo HTMLheader() . "
    <body>
        <div class=\"frame\">
            <h3>Before</h1>
            <p>$textIn</p>
        </div>
        <div>
            <h3>After</h3>
            <div>$table</div>
        </div>
        <div>
            <a href=\"index.php\" class=\"custom-button\">Back</a>
        </div>
    </body>

    </html>";
}

function outputZoo($fileIn, $fileOut)
{
    $textIn = file_get_contents($fileIn);
    $zooCount = file_get_contents($fileOut);
    $zooCount = explode("\n", $zooCount);

    $counts = [];

    foreach ($zooCount as $line) {
        if (preg_match('/(Білих|Чорних) (псів|котів): (\d+)/', $line, $matches)) {
            $key = "{$matches[1]} {$matches[2]}";
            $counts[$key] = $matches[3];
        }
    }
    $table = "<table>
    <tr>
        <th>Word</th>
        <th>Count</th>
    </tr>";

    foreach ($counts as $animal => $count) {
        $table .= "<tr><td>$animal</td><td>$count</td></tr>";
    }
    $table .= "</table>";
    echo HTMLheader() . "
    <body>
        <div class=\"frame\">
            <h3>Before</h1>
            <p>$textIn</p>
        </div>
        <div>
            <h3>After</h3>
            <div>$table</div>
        </div>
        <div>
            <a href=\"index.php\" class=\"custom-button\">Back</a>
        </div>
    </body>

    </html>";
}

function HTMLheader()
{
    return "    
    <!DOCTYPE html>
    <html lang=\"en\">
    
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>Regex</title>
        <link rel=\"stylesheet\" href=\"css/styles.css\">
        <link rel=\"stylesheet\" href=\"css/data.css\">
        
    </head>";
}

function pageNotFound()
{
    echo HTMLheader() . "
    <body>
        <div>
            <h1>Oops! Page Not Found</h1>
            <p>Sorry, it seems like the page you're looking for doesn't exist.</p>
            <p>You can go back to the <a href=\"index.php\">Home</a> or contact us if you need assistance.</p>
        </div>
    </body>
    </html>
    ";
}
