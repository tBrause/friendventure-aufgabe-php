<?php
# error_reporting(0);

# document charset
header('Content-type: text/html; charset=utf-8');

# timezone
date_default_timezone_set('Europe/Berlin');

################# Initialize access data and variables ###
require('inc/ini.php');

# temp user id
$id = 1234568;

$cid = trim(substr(filter_input(INPUT_GET, 'cid'), 0, 10));
$inbasket = trim(substr(filter_input(INPUT_GET, 'inbasket'), 0, 1));
$release = trim(substr(filter_input(INPUT_GET, 'release'), 0, 1));
$saveid = trim(substr(filter_input(INPUT_GET, 'saveid'), 0, 10));
$deletid = trim(substr(filter_input(INPUT_GET, 'deletid'), 0, 10));
$showid = trim(substr(filter_input(INPUT_GET, 'showid'), 0, 10));
$addfav = trim(substr(filter_input(INPUT_GET, 'addfav'), 0, 10));
$delfav = trim(substr(filter_input(INPUT_GET, 'delfav'), 0, 10));
$delbasketid = trim(substr(filter_input(INPUT_GET, 'delbasketid'), 0, 10));
$clearid = trim(substr(filter_input(INPUT_GET, 'clearid'), 0, 10));


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
    if (intval($addfav) === 1) {
        require("inc/fct.addfav.list.php");
        saveFav($conn, $cid);
    }
    if (intval($delfav) === 1) {
        require("inc/fct.delfav.list.php");
        deleteFav($conn, $cid);
    }
}
if ($saveid !== '') {
    require("inc/fct.save.list.php");
    saveBasket($conn, $saveid);
}
if ($showid !== '') {
    require("inc/fct.show.list.php");
    showBasket($conn, $showid);
}
if ($deletid !== '') {
    require("inc/fct.deletid.list.php");
    deleteBasket($conn, $deletid);
}
if ($delbasketid !== '') {
    require("inc/fct.del.basket.list.php");
    deleteBasketList($conn, $delbasketid);
}
if ($clearid !== '') {
    require("inc/fct.clear.basket.list.php");
    clearBasketList($conn, $clearid);
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EinkaufslisteApp</title>
    <link rel="stylesheet" href="default.css">
</head>

<body>
    <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
        <div class="wrapper">

            <?php
            /**
             * 
             * user
             * 
             */

            # Query database 'USER'
            $sql = "SELECT * FROM `user` WHERE `id` = '" . $id . "' AND `release` = '1'";
            $result = mysqli_query($conn, $sql);
            $row_user = mysqli_fetch_array($result);
            mysqli_free_result($result);

            $user_id = $row_user['id'];
            $user_name = $row_user['name'];

            echo '<h2>Benutzer: ' . $user_name . '</h2>';

            /**
             * 
             * basket 
             * 
             */

            # Query database 'BASKET'
            $sql_basket = "SELECT * FROM `basket` WHERE `userid` = '" . $user_id . "' AND  `release` = '1'";
            $result_basket = mysqli_query($conn, $sql_basket);

            while ($row_basket = mysqli_fetch_array($result_basket)) {

                echo '<section>';

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
                        echo '<li>' . $content_quantity . ' x ' . $row_content['product'] . ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id . '&amp;inbasket=1" target="_self">erledigt &#10004;</a> <a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id . '&amp;release=1" target="_self">nicht vorhanden &#10006;</a></li>';
                    }
                    echo "</ul>";
                }
                mysqli_free_result($result_content);


                /**
                 * 
                 * content
                 * 
                 */

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
                        echo '<li>' . $content_quantity_done . ' x ' . $content_product_done . ' ';

                        $sql_fav = "SELECT * FROM `fav` WHERE `contentid` = '" . $content_id_done . "'";
                        $result_fav = mysqli_query($conn, $sql_fav);
                        $count_fav = mysqli_num_rows($result_fav);
                        mysqli_free_result($result_fav);

                        if ($count_fav === 0) {
                            echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id_done . '&amp;addfav=1" target="_self" alt="zu Favoriten hinzuf&uuml;gen" title="zu Favoriten hinzuf&uuml;gen">zu Favoriten hinzuf&uuml;gen &#10084;</a>';
                        } else {
                            echo '<span class="ok">Favorit &#10084;</span>';
                        }

                        echo '</li>';
                    }
                    echo "</ul>";
                }
                mysqli_free_result($result_content_done);


                /**
                 * 
                 * später
                 * 
                 */

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
                        echo '<li>';
                        echo $content_quantity_release . ' x ' . $content_product_release . '<a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id_release . '&amp;release=2" target="_self">wieder vorhanden &#10004;</a>';
                        #echo '<a href="index.php" target="_self">in eine andere Liste verschieben &#10132;</a>';
                        echo 'in eine andere Liste verschieben &#10132;';
                        echo '</li>';
                    }
                    echo "</ul>";
                }
                mysqli_free_result($result_content_release);

                /**
                 * 
                 * info
                 * 
                 */
                $diff = $sum_basket - $count_content_done;

                if ($diff === 0) {
                    echo '<div class="open">Erledigt</div>';
                    echo '<H4 class="delete_list"><a href="' . $_SERVER['SCRIPT_NAME'] . '?clearid=' . $row_basket['id'] . '" target="_self">Warenkorb ' . $row_basket['name'] . ' leeren &#9744;</a></H4>';
                } else {
                    echo '<div class="open">Noch ' . $diff . ' von ' . $sum_basket . ' offen</div>';
                }

                /**
                 * 
                 * speichern & deaktivieren
                 * 
                 */
                $sql_basketsave = "SELECT * FROM `basketsave` WHERE `basketid` = '" . $row_basket['id'] . "'";
                $result_basketsave = mysqli_query($conn, $sql_basketsave);
                $count_basketsave = mysqli_num_rows($result_basketsave);
                mysqli_free_result($result_basketsave);

                if ($count_basketsave === 0) {
                    echo '<H4 class="delete_list"><a href="' . $_SERVER['SCRIPT_NAME'] . '?saveid=' . $row_basket['id'] . '" target="_self">Einkaufliste: ' . $row_basket['name'] . ' speichern &#9745;</a></H4>';
                    echo '<H4 class="delete_list delete_list_last"><a href="' . $_SERVER['SCRIPT_NAME'] . '?deletid=' . $row_basket['id'] . '" target="_self">Einkaufliste: ' . $row_basket['name'] . ' deaktivieren &#10061;</a></H4>';
                } else {
                    echo '<H4>Die Liste ' . $row_basket['name'] . ' ist gespeichert</H4>';
                }

                echo '</section>';
            }

            mysqli_free_result($result_basket);

            /**
             * 
             * deaktivierte
             * 
             */
            $sql_basketsave_overview = "SELECT  
            bs.basketid,
            b.name AS bname 
        FROM
        basketsave AS bs
        INNER JOIN basket AS b ON b.id = bs.basketid
        ORDER BY bname";

            #$sql_basketsave_overview = "SELECT * FROM `basketsave`";
            $result_basketsave_overview = mysqli_query($conn, $sql_basketsave_overview);
            $count_basketsave_overview = mysqli_num_rows($result_basketsave_overview);

            if ($count_basketsave_overview >= 1) {

                echo '<H3 class="subnav">Diese Listen sind gespeichert</H3>';

                echo '<ul>';
                while ($row_basketsave_overview = mysqli_fetch_array($result_basketsave_overview)) {
                    echo '<li>' . $row_basketsave_overview['bname'] . ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?delbasketid=' . $row_basketsave_overview['basketid'] . '" target="_self">von Liste entfernen</a></li>';
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
                echo '<H3 class="subnav">Diese Liste ist deaktiviert</H3>';

                echo '<ul>';
                while ($row_basketdelet = mysqli_fetch_array($result_basketdelet)) {
                    echo '<li>' . $row_basketdelet['name'] . ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?showid=' . $row_basketdelet['id'] . '" target="_self">wiederherstellen</a></li>';
                }
                echo '</ul>';
            }

            mysqli_free_result($result_basketdelet);


            /**
             * 
             * Favoriten
             * 
             */
            $sql_fav_all = "SELECT  
                f.contentid,
                c.product AS product,
                c.basketid AS basketid, 
                b.id,
                b.name AS bname 
            FROM
                fav AS f
            INNER JOIN content AS c ON c.id = f.contentid
            INNER JOIN basket AS b ON b.id = c.basketid 
            ORDER BY c.product";

            #echo $sql_fav_all;

            $result_fav_all = mysqli_query($conn, $sql_fav_all);
            $count_fav_all = mysqli_num_rows($result_fav_all);

            if ($count_fav_all >= 1) {
                echo '<H3 class="subnav">Favoriten Liste</H3>';

                echo '<ul>';
                while ($row_fav_all = mysqli_fetch_array($result_fav_all)) {
                    echo '<li>' . $row_fav_all['product'] . ' @ ' . $row_fav_all['bname'] . ' ' . $row_fav_all['contentid'] . ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $row_fav_all['contentid'] . '&amp;delfav=1" target="_self">entfernen</a></li>';
                }
                echo '</ul>';
            }


            mysqli_free_result($result_fav_all);

            ?>

        </div>
    </form>
</body>

</html>