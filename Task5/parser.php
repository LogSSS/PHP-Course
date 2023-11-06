<?php
error_reporting(E_ERROR | E_PARSE);

include "sql.php";

class Parser
{

    private $message = '';

    private $name1 = '';

    private $name2 = '';

    private $SQL;

    private $deep;

    public function __construct($SQL)
    {
        $this->SQL = $SQL;

        $this->truncate();

        $this->deep = 20;

        $extension1 = pathinfo($_FILES['file1']['name'], PATHINFO_EXTENSION);
        $this->name1 = pathinfo($_FILES['file1']['name'], PATHINFO_FILENAME);

        $extension2 = pathinfo($_FILES['file2']['name'], PATHINFO_EXTENSION);
        $this->name2 = pathinfo($_FILES['file2']['name'], PATHINFO_FILENAME);

        $this->parseFile($extension1, 1);
        $this->parseFile($extension2, 2);

        $this->compareTracks();

        $this->goBackHtml();
    }


    private function compareTracks()
    {
        $conn = $this->SQL->getConnect();
        $stmt = $conn->prepare("SELECT count(DISTINCT track) FROM first_track");
        $stmt->execute();
        $count = $stmt->fetchColumn();

        $stmt = $conn->prepare("SELECT count(DISTINCT first_track.track) FROM first_track INNER JOIN second_track ON first_track.track = second_track.track");
        $stmt->execute();
        $newCount = $stmt->fetchColumn();

        if ($count == 0) {
            $diff = 0;
        } else {
            $diff = ($newCount / $count) * 100;
        }
        $this->message = "Similarity: " . $diff . "%";
        $conn = null;
    }

    function parseFile($extension, $id)
    {
        if ($extension == 'gpx') {
            $this->parseFromGPX($this->getXML($id), $id);
        } elseif ($extension == 'kml') {
            $this->parseFromKML($this->getXML($id), $id);
        } else {
            $this->message .= "File {$id}: type not supported\n";
            $this->goBackHtml();
        }
    }

    private function getXML($id)
    {
        if ($id == 1)
            $name = $this->name1;
        else
            $name = $this->name2;
        $xml = new DOMDocument();
        $xml->load($_FILES['file' . $id]['tmp_name']);

        if ($xml === false) {
            $this->message = 'Error: Unable to parse file: ' . $name;
            $this->goBackHtml();
            exit();
        }
        return $xml;
    }

    private function parseFromGPX($xml, $id)
    {
        try {
            $new_gpx = new DOMDocument();
            $new_gpx->formatOutput = true;

            $gpx = $new_gpx->createElement('gpx');
            $gpx->setAttribute('xmlns', 'http://www.topografix.com/GPX/1/1');
            $gpx->setAttribute('creator', 'Blud - http://www.karpaty.com.ua/gps');
            $gpx->setAttribute('version', '1.1');
            $gpx->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $gpx->setAttribute('xsi:schemaLocation', 'http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensions/v3/GpxExtensionsv3.xsd http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd');

            $new_gpx->appendChild($gpx);

            $track = $new_gpx->createElement('trk');

            $trackSegment = $new_gpx->createElement('trkseg');

            $track->appendChild($trackSegment);
            $gpx->appendChild($track);

            $name = "";
            $outname = "";
            $outARR = array();
            if ($id == 1) {
                $name = "first";
                $outname = $this->name1;
            } else {
                $name = "second";
                $outname = $this->name2;
            }
            $trksegs = $xml->getElementsByTagName('trkseg');
            foreach ($trksegs as $trkseg) {
                $trkpts = $trkseg->getElementsByTagName('trkpt');
                foreach ($trkpts as $trkpt) {
                    $lat = $trkpt->getAttribute('lat');
                    $lon = $trkpt->getAttribute('lon');
                    $symbol = $this->ToSymbol($lon, $lat,  $this->deep);
                    $outARR[] = $symbol;

                    $new_trkpt = $new_gpx->createElement('trkpt');
                    $new_trkpt->setAttribute('lat', $lat);
                    $new_trkpt->setAttribute('lon', $lon);

                    $symb = $new_gpx->createElement('track',$symbol);
                    $new_trkpt->appendChild($symb);

                    $trackSegment->appendChild($new_trkpt);
                }
            }
            //$outARR = array_unique($outARR);
            $this->saveSymbols($outARR, $name);
            $new_gpx->save("files/out/parsed_$outname.gpx");
        } catch (Exception $e) {
            $this->message = $e->getMessage();
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    private function parseFromKML($xml, $id)
    {
        try {
            $newKML = new DOMDocument('1.0', 'UTF-8');
            $newKML->formatOutput = true;

            $root = $newKML->createElement('kml');
            $root->setAttribute('xmlns', 'http://earth.google.com/kml/2.1');
            $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $newKML->appendChild($root);

            $document = $newKML->createElement('Document');
            $root->appendChild($document);

            $folder = $newKML->createElement('Folder');
            $name = $newKML->createElement('name', 'New Waypoints and Track');
            $folder->appendChild($name);
            $document->appendChild($folder);

            $placemarks = $xml->getElementsByTagName('Placemark');
            if ($placemarks->length > 0) {
                $lastPlacemark = $placemarks->item($placemarks->length - 1);
                $lastPlacemark->parentNode->removeChild($lastPlacemark);
            }
            $trackFolder = $newKML->createElement('Folder');
            $trackFolderName = $newKML->createElement('name', 'Track');
            $trackFolder->appendChild($trackFolderName);
            $document->appendChild($trackFolder);
            $trackPlacemark = $newKML->createElement('Placemark');

            $trackName = $newKML->createElement('name', 'Path');
            $trackPlacemark->appendChild($trackName);

            $trackStyleUrl = $newKML->createElement('styleUrl', '#lineStyle');
            $trackPlacemark->appendChild($trackStyleUrl);

            $trackLineString = $newKML->createElement('LineString');
            $tessellate = $newKML->createElement('tessellate', '1');
            $trackLineString->appendChild($tessellate);
            $trackCoordinates = $newKML->createElement('coordinates');

            $name = "";
            $outname = "";
            $outARR = array();
            if ($id == 1) {
                $name = "first";
                $outname = $this->name1;
            } else {
                $name = "second";
                $outname = $this->name2;
            }

            $coordinatesString = '';
            foreach ($placemarks as $placemark) {
                $nameF = $placemark->getElementsByTagName('name')[0]->nodeValue;
                $coordinates = $placemark->getElementsByTagName('coordinates')[0]->nodeValue;
                list($lon, $lat, $altitude) = explode(",", $coordinates);
                $symbol = $this->ToSymbol($lon, $lat, $this->deep);
                $outARR[] = $symbol;

                $newPlacemark = $newKML->createElement('Placemark');

                $newName = $newKML->createElement('name', $nameF);
                $newPlacemark->appendChild($newName);
                $description = "Track: $symbol\nLatitude: $lon\nLongitude: $lat\nAltitude: $altitude\n";
                $newDescription = $newKML->createElement('description', $description);
                $newPlacemark->appendChild($newDescription);

                $newPoint = $newKML->createElement('Point');
                $newCoordinates = $newKML->createElement('coordinates', $coordinates);
                $newPoint->appendChild($newCoordinates);
                $newPlacemark->appendChild($newPoint);

                $folder->appendChild($newPlacemark);
                $coordinatesString .= $lon . "," . $lat . "," . $altitude . "\n";
            }
            $trackCoordinates->nodeValue = $coordinatesString;
            $trackLineString->appendChild($trackCoordinates);
            $trackPlacemark->appendChild($trackLineString);
            $trackFolder->appendChild($trackPlacemark);
            //$outARR = array_unique($outARR);
            $this->saveSymbols($outARR, $name);
            $newKML->save("files/out/parsed_$outname.kml");
        } catch (Exception $e) {
            $this->message = $e->getMessage();
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    private function ToSymbol($x, $y, $i, $bob = true)
    {
        if ($bob)
            return "0" . $this->ToSymbol($x, $y, $i - 1, false);
        if ($i == 0)
            return "";
        else if ($x >= 0 && $y >= 0)
            return "1" . $this->ToSymbol(2 * $x - 180, 2 * $y - 90, $i - 1, false);
        else if ($x <= 0 && $y >= 0)
            return "0" . $this->ToSymbol(2 * $x + 180, 2 * $y - 90, $i - 1, false);
        else if ($x <= 0 && $y <= 0)
            return "2" . $this->ToSymbol(2 * $x + 180, 2 * $y + 90, $i - 1, false);
        else if ($x >= 0 && $y <= 0)
            return "3" . $this->ToSymbol(2 * $x - 180, 2 * $y + 90, $i - 1, false);

        throw new Exception("Not possible!");
    }

    private function saveSymbols($arr, $table)
    {
        $conn = $this->SQL->getConnect();
        $conn->beginTransaction();
        try {
            $bob = "INSERT INTO $table" . "_track (track) VALUES ";
            if (count($arr) == 0)
                die("BOB");
            $stmt = $conn->prepare($bob . rtrim(str_repeat("(?), ", count($arr)), ", "));
            $stmt->execute($arr);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            $this->message = $e->getMessage();
            $this->goBackHtml();
        }
        $conn = null;
    }

    private function truncate()
    {
        $conn = $this->SQL->getConnect();
        $conn->beginTransaction();
        try {
            $stmt = $conn->prepare("TRUNCATE TABLE first_track, second_track;");
            $stmt->execute();
            $stmt = $conn->prepare("ALTER SEQUENCE first_track_id_seq RESTART WITH 1;");
            $stmt->execute();
            $stmt = $conn->prepare("ALTER SEQUENCE second_track_id_seq RESTART WITH 1;");
            $stmt->execute();
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            $this->message = $e->getMessage();
            $this->goBackHtml();
        }
        $conn = null;
    }

    private function goBackHtml()
    {
        die("
        <!DOCTYPE html>
        <html lang=\"en\">

        <head>
            <meta charset=\"UTF-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            <title>File Uploader</title>
            <link rel=\"stylesheet\" href=\"css/parser.css\">
            <link rel=\"stylesheet\" href=\"css/styles.css\">
        </head>

        <body>
            <div class=\"container\">
                <h1>File Uploader</h1>
                <div class=\"container-child\">$this->message</div>
                <a href=\"index.php\" class=\"custom-button\">Go Back</a>
           
            </div>

        </body>

        </html>
       ");
    }
}


new Parser(new SQL());
