<?php

error_reporting(E_ERROR | E_PARSE);

class Parser
{

    private $message = '';

    private $name = '';

    public function __construct()
    {
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        $this->name = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);

        if ($extension == 'gpx') {

            $this->parseFromGPX($this->getXML());
            goto bob;
        }

        if ($extension == 'kml') {
            $this->parseFromKML($this->getXML());
            goto bob;
        }

        $this->message = 'File type not supported';

        bob:
        $this->goBackHtml();
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

    private function getXML()
    {
        $xml = simplexml_load_string(file_get_contents($_FILES['file']['tmp_name']));
        if ($xml === false) {
            $this->message = 'Error: Unable to parse file';
            $this->goBackHtml();
            exit();
        }
        return $xml;
    }

    private function parseFromGPX($xml)
    {
        try {
            $kml = new SimpleXMLElement('<kml xmlns="http://www.opengis.net/kml/2.2"></kml>');
            $document = $kml->addChild('Document');

            foreach ($xml->wpt as $waypoint) {
                $placemark = $document->addChild('Placemark');

                $coordinates = $waypoint['lon'] . ',' . $waypoint['lat'];
                $placemark->addChild('Point')->addChild('coordinates', $coordinates);

                foreach (['name', 'cmt'] as $tag)
                    $placemark->addChild($tag, (string)$waypoint->$tag);

                $placemark->addChild('styleUrl', 'some_style');

                $extendedData = $placemark->addChild('ExtendedData');
                $data = $extendedData->addChild('Data');
                $data->addAttribute('name', 'Link');
                $data->addChild('value', (string)$waypoint->link['href']);
            }

            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($kml->asXML());

            $dom->save('files/out/parsed_' . $this->name . '.kml');
            $this->message = 'GPX to KML file parsed successfully!';
        } catch (Exception $e) {
            $this->message = $e->getMessage();
        }
    }

    private function parseFromKML($xml)
    {
        try {
            $gpx = new DOMDocument('1.0', 'UTF-8');
            $gpx->formatOutput = true;

            $root = $gpx->appendChild($gpx->createElement('gpx'));
            $root->setAttribute('xmlns', 'http://www.topografix.com/GPX/1/1');
            $root->setAttribute('creator', 'MapSource 6.13.7');
            $root->setAttribute('version', '1.1');
            $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $root->setAttribute('xsi:schemaLocation', 'http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensions/v3/GpxExtensionsv3.xsd http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd');

            foreach ($xml->Document->Folder->Folder->Placemark as $placemark) {
                list($lon, $lat) = explode(',', (string)$placemark->Point->coordinates);
                $name = (string)$placemark->name;
                $description = (string)$placemark->description;
                $link = (string)$placemark->description;

                $wpt = $root->appendChild($gpx->createElement('wpt'));
                $wpt->setAttribute('lat', $lat);
                $wpt->setAttribute('lon', $lon);

                foreach (['ele' => '0', 'name' => $name, 'cmt' => $description, 'desc' => $description] as $tag => $value)
                    $wpt->appendChild($gpx->createElement($tag, $value));

                $linkElement = $wpt->appendChild($gpx->createElement('link'));
                $linkElement->setAttribute('href', $link);

                $sym = $wpt->appendChild($gpx->createElement('sym', 'Flag, Green'));

                $waypointExtension = $wpt->appendChild($gpx->createElement('extensions'));
                $waypointExtension->appendChild($gpx->createElement('gpxx:WaypointExtension'))
                    ->appendChild($gpx->createElement('gpxx:DisplayMode', 'SymbolAndName'));
            }

            $gpx->save('files/out/parsed_' . $this->name . '.gpx');
            $this->message = 'KML to GPX file parsed successfully!';
        } catch (Exception $e) {
            $this->message = $e->getMessage();
        }
    }
}

new Parser();
