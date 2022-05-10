<?php
# error_reporting(0);

# document charset
header('Content-type: text/html; charset=utf-8');

# timezone
date_default_timezone_set('Europe/Berlin');

################# Initialize access data and variables ###
require('inc/ini.php');

# temp id
$id = 1234568;

$cid = trim(substr(filter_input(INPUT_GET, 'cid'), 0, 10));
$inbasket = trim(substr(filter_input(INPUT_GET, 'inbasket'), 0, 1));
$release = trim(substr(filter_input(INPUT_GET, 'release'), 0, 1));
$saveid = trim(substr(filter_input(INPUT_GET, 'saveid'), 0, 10));
$deletid = trim(substr(filter_input(INPUT_GET, 'deletid'), 0, 10));
$showid = trim(substr(filter_input(INPUT_GET, 'showid'), 0, 10));
$addfav = trim(substr(filter_input(INPUT_GET, 'addfav'), 0, 10));

if ($cid !== '') {
    if (intval($inbasket) === 1) {
        require("inc/fct.inbasket.php");
        updateContent($conn, $cid);
    }

    if (intval($release) === 1) {
        require("inc/fct.later.php");
        updateContentRelease($conn, $cid);
    }
    if (intval($release) === 2) {
        require("inc/fct.back.list.php");
        updateContentReleaseBack($conn, $cid);
    }
}
if ($saveid !== '') {
    require("inc/fct.save.list.php");
    saveBasket($conn, $saveid);
}
if ($deletid !== '') {
    require("inc/fct.deletid.list.php");
    deleteBasket($conn, $deletid);
}

if ($showid !== '') {
    require("inc/fct.show.list.php");
    showBasket($conn, $showid);
}


?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f1f1;
        }

        .wrapper {
            max-width: 960px;
            width: 80%;
            margin: auto;
        }

        h3,
        H4 {
            padding: 6px 12px;
        }

        h3 {
            background-color: #666;
            color: #f1f1f1;
            margin-top: 3em;
        }

        H4 {
            background-color: #ccc;
        }

        a {
            margin: auto;
            padding: 6px 12px;
        }

        .delete_list_last {
            _margin-bottom: 3em;
        }

        .delete_list a {
            padding: 0;
        }

        .open {
            display: block;
            padding: 20px;
            background-color: #fff;
        }
    </style>

</head>

<body>
    <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
        <div class="wrapper">

            <?php
            # Query database 'USER'
            $sql = "SELECT * FROM `user` WHERE `id` = '" . $id . "' AND `release` = '1'";
            $result = mysqli_query($conn, $sql);
            $row_user = mysqli_fetch_array($result);
            mysqli_free_result($result);

            $user_id = $row_user['id'];
            $user_name = $row_user['name'];

            echo '<h2>Benutzer: ' . $user_name . '</h2>';

            # Query database 'BASKET'
            $sql_basket = "SELECT * FROM `basket` WHERE `userid` = '" . $user_id . "' AND  `release` = '1'";
            $result_basket = mysqli_query($conn, $sql_basket);

            while ($row_basket = mysqli_fetch_array($result_basket)) {

                $sum_basket = 0;

                echo '<h3>Einkaufliste: ' . $row_basket['name'] . '</h3>';
                $basket_id = $row_basket['id'];

                # Query database 'CONTENT'
                $sql_content = "SELECT * FROM `content` WHERE `basketid` = '" . $basket_id . "' AND `done` = '0' AND  `release` = '1'";
                $result_content = mysqli_query($conn, $sql_content);
                $count_content = mysqli_num_rows($result_content);
                #echo $count_content . "<br>";

                $sum_basket += $count_content;

                if ($count_content >= 1) {

                    echo "<H4>noch offen</H4>";

                    echo "<ul>";
                    while ($row_content = mysqli_fetch_array($result_content)) {

                        $content_id = $row_content['id'];
                        $content_quantity = $row_content['quantity'];
                        echo '<li>' . $content_quantity . ' x ' . $row_content['product'] . ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id . '&amp;inbasket=1" target="_self">erledigt</a> <a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id . '&amp;release=1" target="_self">nicht vorhanden</a></li>';
                    }
                    echo "</ul>";
                }
                mysqli_free_result($result_content);

                # Query database 'CONTENT' DONE
                $sql_content_done = "SELECT * FROM `content` WHERE `basketid` = '" . $basket_id . "' AND `done` = '1' AND  `release` = '1'";
                $result_content_done = mysqli_query($conn, $sql_content_done);
                $count_content_done = mysqli_num_rows($result_content_done);
                #echo $count_content_done . "<br>";

                $sum_basket += $count_content_done;

                if ($count_content_done >= 1) {

                    echo "<H4>im Warenkorb</H4>";

                    echo "<ul>";
                    while ($row_content_done = mysqli_fetch_array($result_content_done)) {

                        $content_id_done = $row_content_done['id'];
                        $content_quantity_done = $row_content_done['quantity'];
                        $content_product_done = $row_content_done['product'];
                        echo '<li>' . $content_quantity_done . ' x ' . $content_product_done . ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id_done . '&amp;addfav=1" target="_self">zu Favoriten hinzuf&uuml;gen</a></li>';
                    }
                    echo "</ul>";
                }
                mysqli_free_result($result_content_done);

                # Query database 'CONTENT' LATER
                $sql_content_release = "SELECT * FROM `content` WHERE `basketid` = '" . $basket_id . "' AND `release` = '0'";
                $result_content_release = mysqli_query($conn, $sql_content_release);
                $count_content_release = mysqli_num_rows($result_content_release);
                #echo $count_content_release . "<br>";

                $sum_basket += $count_content_release;

                if ($count_content_release >= 1) {

                    echo "<H4>später</H4>";

                    echo "<ul>";
                    while ($row_content_release = mysqli_fetch_array($result_content_release)) {

                        $content_id_release = $row_content_release['id'];
                        $content_quantity_release = $row_content_release['quantity'];
                        $content_product_release = $row_content_release['product'];
                        echo '<li>' . $content_quantity_release . ' x ' . $content_product_release . ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id_release . '&amp;release=2" target="_self">wieder vorhanden</a></li>';
                    }
                    echo "</ul>";
                }
                mysqli_free_result($result_content_release);

                $diff = $sum_basket - $count_content_done;
                echo '<div class="open">Noch ' . $diff . ' von ' . $sum_basket . ' offen</div>';
                $sql_basketsave = "SELECT * FROM `basketsave` WHERE `basketid` = '" . $row_basket['id'] . "'";
                $result_basketsave = mysqli_query($conn, $sql_basketsave);
                $count_basketsave = mysqli_num_rows($result_basketsave);
                mysqli_free_result($result_basketsave);

                if ($count_basketsave === 0) {
                    echo '<H4 class="delete_list"><a href="' . $_SERVER['SCRIPT_NAME'] . '?saveid=' . $row_basket['id'] . '" target="_self">Einkaufliste: ' . $row_basket['name'] . ' speichern</a></H4>';
                    echo '<H4 class="delete_list delete_list_last"><a href="' . $_SERVER['SCRIPT_NAME'] . '?deletid=' . $row_basket['id'] . '" target="_self">Einkaufliste: ' . $row_basket['name'] . ' deaktivieren</a></H4>';
                } else {
                    echo '<H4>Die Liste ' . $row_basket['name'] . ' ist gespeichert</H4>';
                }

                #echo '<ul class="delete_list">';
                #echo '<li>Einkaufliste: <a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id_release . '&amp;release=2" target="_self">' . $row_basket['name'] . ' löschen</a></li>';
                #echo '</ul>';
            }

            mysqli_free_result($result_basket);

            /**
             * 
             * deaktivierte
             * 
             */


            $sql_basketsave_overview = "SELECT * FROM `basketsave`";
            $result_basketsave_overview = mysqli_query($conn, $sql_basketsave_overview);
            $count_basketsave_overview = mysqli_num_rows($result_basketsave_overview);

            if ($count_basketsave_overview >= 1) {

                echo '<H3>Diese Listen sind gespeichert</H3>';

                echo '<ul>';
                while ($row_basketsave_overview = mysqli_fetch_array($result_basketsave_overview)) {
                    echo '<li>' . $row_basketsave_overview['basketid'] . ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?showid=' . $row_basketsave_overview['basketid'] . '" target="_self">von Liste entfernen</a></li>';
                }
                echo '</ul>';
            }

            mysqli_free_result($result_basketsave_overview);

            /**
             * 
             * verborgene
             * 
             */

            $sql_basketdelet = "SELECT * FROM `basket` WHERE `release` = '0'";
            #echo $sql_basketdelet . '<br>';
            $result_basketdelet = mysqli_query($conn, $sql_basketdelet);

            $count_basketdelet = mysqli_num_rows($result_basketdelet);
            #echo $count_basketdelet . '<br>';

            if ($count_basketdelet >= 1) {
                echo '<H3>Diese Liste ist deaktiviert</H3>';

                echo '<ul>';
                while ($row_basketdelet = mysqli_fetch_array($result_basketdelet)) {
                    echo '<li>' . $row_basketdelet['name'] . ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?showid=' . $row_basketdelet['id'] . '" target="_self">wiederherstellen</a></li>';
                }
                echo '</ul>';
            }

            mysqli_free_result($result_basketdelet);
            ?>

        </div>
    </form>
</body>

</html>