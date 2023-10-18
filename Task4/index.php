<?php


error_reporting(E_ERROR | E_PARSE);

//All

$months = array(
    'січня', 'лютого', 'березня', 'квітня', 'травня', 'червня',
    'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня'
);

function getText($path)
{
    return file_get_contents($path);
}

function saveTXT($path, $text)
{
    file_put_contents($path, $text);
}

//First task


function getLinks($text)
{
    $pattern = "/<a href=\"(.*)\">(.*)<\/a>/U";
    if (preg_match_all($pattern, $text, $matches))
        return $matches[1];
    return null;
}

function createFirstHTML($links)
{
    $table = "<table>
    <tr>
        <th>№</th>
        <th>Link</th>
    </tr>";

    foreach ($links as $key => $link) {
        $table .= "<tr>
            <td>" . ($key + 1) . "</td>
            <td>$link</td>
        </tr>";
    }

    $table .= "</table>";

    $html = "
    <!DOCTYPE html>
    <html lang=\"en\">
    <head>
        <meta charset=\"UTF-8\" />
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
        <title>Regex</title>
        <link rel=\"stylesheet\" href=\"css/styles.css\">
        <link rel=\"stylesheet\" href=\"css/data.css\">
    </head>
    <body>
        <div>
            <h3>Links</h3>
            $table
        </div>
        <div>
            <a href=\"index.php\" class=\"custom-button\">Back</a>
        </div>
    </body>
    </html>";

    $formatted_html = preg_replace('/\n\s*\n/', "\n", $html);
    $formatted_html = preg_replace('/\n\s+/', "\n", $formatted_html);

    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($formatted_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    $dom->formatOutput = true;


    file_put_contents("files/out/out.html", $dom->saveHTML($dom->documentElement));
}

createFirstHTML(getLinks(getText("files/in/in.html")));

//Second task


function saveHTML($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $out = curl_exec($ch);
    curl_close($ch);
    file_put_contents("files/in/phone_book_pnu.html", $out);
}

function listOfAllPhonesAndNames($file)
{
    $html = file_get_contents($file);
    $pattern = '/<div id="tab">(.*?)<\/div>/s';
    preg_match_all($pattern, $html, $matches);

    $html = implode($matches[0]);

    $pattern = '/<td.*>.*(.*?)<\/td>/s';
    preg_match_all($pattern, $html, $matches);

    $html = implode($matches[0]);

    $pattern = '/<td.*><span.*>.*<\/span>(.*?)<\/td>/s';

    preg_match_all($pattern, $html, $matches);

    // file_put_contents("files/out/phone_book_pnu1.html", $html);
    // file_put_contents("files/out/phone_book_pnu2.html", implode($matches[0]));
}
saveHTML("https://pnu.edu.ua/phone_book_pnu/");

listOfAllPhonesAndNames("files/in/phone_book_pnu.html");

//Third task


function changeText($text)
{
    $months_pattern = implode('|', array_map('preg_quote', $GLOBALS['months']));

    $pattern = "/(\d+)\s*($months_pattern)|($months_pattern)\s*(\d+)/u";

    $replacement = "$1 січня";

    $new_text = preg_replace($pattern, $replacement, $text);

    return $new_text;
}

saveTXT("files/out/month.txt", changeText(getText("files/in/month.txt")));

//Fourth task


function getDates($text)
{
    $month_names = implode('|', array_map(function ($month) {
        return preg_quote($month, '/');
    }, $GLOBALS['months']));
    $pattern = "/(0[1-9]|[12][0-9]|3[01])([-\/])(0[1-9]|1[0-2]|$month_names)([-\/](\d{4}))?/u";

    $matches = array();
    $datesOut = null;
    if (preg_match_all($pattern, $text, $matches)) {
        $dates = $matches[0];
        foreach ($dates as $date) {
            $datesOut .= $date . "\n";
        }
    }
    return substr($datesOut, 0, -1);
}

saveTXT("files/out/dates.txt", getDates(getText("files/in/dates.txt")));

//Fifth task

function countColors($text)
{
    $pattern = "/(білий|чорний)\s+(пес|кіт)/iu";

    preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

    $whiteDogCount = 0;
    $blackDogCount = 0;
    $whiteCatCount = 0;
    $blackCatCount = 0;

    foreach ($matches as $match) {
        switch ($match[1]) {
            case "білий":
                switch ($match[2]) {
                    case "пес":
                        $whiteDogCount++;
                        break;
                    case "кіт":
                        $whiteCatCount++;
                        break;
                }
                break;
            case "чорний":
                switch ($match[2]) {
                    case "пес":
                        $blackDogCount++;
                        break;
                    case "кіт":
                        $blackCatCount++;
                        break;
                }
                break;
        }
    }

    return "Білих псів: $whiteDogCount\nЧорних псів: $blackDogCount\nБілих котів: $whiteCatCount\nЧорних котів: $blackCatCount";
}

saveTXT("files/out/zoo.txt", countColors(getText("files/in/zoo.txt")));

//Sixth task

function nameValidator($text)
{
    //get all names with surnames from text using regex
    $pattern = "/([А-ЯІЇЄ][а-яіїє']+\s+[А-ЯІЇЄ][а-яіїє']+)/u";
    preg_match_all($pattern, $text, $matches);
    $namesOut = null;
    foreach ($matches[0] as $name) {
        $namesOut .= $name . "\n";
    }
    return substr($namesOut, 0, -1);
}

saveTXT("files/out/names.txt", nameValidator(getText("files/in/names.txt")));

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regex</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container">
        <div>
            <a href="data.php?id=1" class="custom-button">First task</a>
        </div>
        <div>
            <a href="data.php?id=2" class="custom-button">Second task</a>
        </div>
        <div>
            <a href="data.php?id=3" class="custom-button">Third task</a>
        </div>
        <div>
            <a href="data.php?id=4" class="custom-button">Fourth task</a>
        </div>
        <div>
            <a href="data.php?id=5" class="custom-button">Fifth task</a>
        </div>
        <div>
            <a href="data.php?id=6" class="custom-button">Sixth task</a>
        </div>
    </div>
</body>

</html>