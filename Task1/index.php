<?php
$dayS = range(1, 31);
$monthS = range(1, 12);
$yearS = array_reverse(range(1939, intval(date("Y"))));

$file = file_get_contents('data.json');
$tasksList = json_decode($file, true, JSON_UNESCAPED_UNICODE);

function showAlert($message)
{
    echo "<script>alert('$message')</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
    if (isset($_POST['submit'])) {
        $text = $_POST['text'];
        $day = $_POST['day'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        if (!checkdate($month, $day, $year))
            showAlert('Invalid date');
        else {
            $dateTime = $year . ' - ' . $month . ' - ' . $day;
            $tasksList[] = ['Date: ' => $dateTime, 'Task: ' => $text];
            asort($tasksList);
            file_put_contents("data.json", json_encode($tasksList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    } elseif (isset($_POST['delete'])) {
        $index = intval($_POST['key']);
        if (isset($tasksList[$index])) {
            unset($tasksList[$index]);
            file_put_contents("data.json", json_encode($tasksList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Task1</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <form action="index.php" method="post">

        <div class="centered">

            <label class="labels">
                <select name="year">
                    <option value="2023">Year</option>
                    <?php
                    foreach ($yearS as $year)
                        echo '<option value="' . $year . '">' . $year . '</option>';
                    ?>
                </select>
            </label>

            <label class="labels">
                <select name="month">
                    <option value="1">Month</option>
                    <?php
                    foreach ($monthS as $month)
                        echo '<option value="' . $month . '">' . $month . '</option>';
                    ?>
                </select>
            </label>

            <label class="labels">
                <select name="day">
                    <option value="1">Day</option>
                    <?php
                    foreach ($dayS as $day)
                        echo '<option value="' . $day . '">' . $day . '</option>';
                    ?>
                </select>
            </label>

        </div>

        <div class="centered submit">
            <h3>Task:</h3>
            <input type="text" name="text" autocomplete="off">
            <input value="SUBMIT" type="submit" text="submit" name="submit">
        </div>

        <br>
    </form>

    <div>

        <form action="index.php" method="post" class="formBruh">
            <?php
            foreach ($tasksList as $key => $value) {
                echo '<form action="index.php" method="post" class="formBruh"';
                foreach ($value as $valueItem)
                    echo '<p>' . $valueItem . '</p>';

                $ind = $key;
                echo '<input type="hidden" name="key" value = "' . $ind . '">';
                echo '<input value="Delete" type = "submit" name = "delete">';
                echo '</form>';
            }
            ?>
        </form>

    </div>

</body>

</html>