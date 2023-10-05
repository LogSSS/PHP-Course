<?php
class Parser
{

    private $message = '';

    private $name = '';

    public function __construct()
    {
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        $this->name = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);

        if ($extension == 'txt') {
            $this->parseToJson(file($_FILES['file']['tmp_name']));
            goto bob;
        }


        $this->message = 'File type not supported';

        bob:
        $this->goBackHtml();
    }

    private function goBackHtml()
    {
        echo "
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
            <h1>File Uploader</h1>
            <div class=\"container\">
                <div class=\"container-child\">$this->message</div>
                <div>
                    <form>
                        <button type=\"button\" onclick=\"location.href='index.php';\" class=\"custom-button\">Go Back</button>
                        <button type=\"button\" onclick=\"location.href='edit.php?file=$this->name';\" class=\"custom-button\">Edit Json</button>
                    </form>
                </div>
            </div>
        </body>

        </html>
       ";
    }

    private function parseToJson($file)
    {

        try {
            $table_data = [];
            foreach ($file as $row) {
                $cells = explode('|', $row);
                $table_data[] = $cells;
            }
            $table_data = array_filter($table_data, function ($row) {
                return !str_contains($row[0], '-');
            });
            $header = array_shift($table_data);

            $header = array_map('trim', $header);

            $table_json = [];
            foreach ($table_data as $row) {
                $cleaned_row = array_map('trim', $row);
                $table_json[] = array_combine($header, $cleaned_row);
            }
            $json_output = json_encode($table_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents('files/out/parsed_' . $this->name . '.json', $json_output);
            $this->message = 'File parsed successfully';
        } catch (Exception $e) {
            $this->message = $e->getMessage();
        }
    }
}

new Parser();
