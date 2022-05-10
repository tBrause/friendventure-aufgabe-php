<?php

# Query database 'CONTENT' OPEN
function fff($conn, $basket_id)
{
    $sql_content = "SELECT * FROM `content` WHERE `basketid` = '" . $basket_id . "' AND `done` = '0' AND `release` = '1'";
    $result_content = mysqli_query($conn, $sql_content);
    $count_content = mysqli_num_rows($result_content);
    #echo $count_content . "<br>";

    if ($count_content >= 1) {

        echo "<ul>";
        while ($row_content = mysqli_fetch_array($result_content)) {

            $content_id = $row_content['id'];
            $content_quantity = $row_content['quantity'];
            echo '<li>' . $content_quantity . ' x ' . $row_content['product'] . ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id . '&amp;inbasket=1" target="_self">gekauft</a> <a href="' . $_SERVER['SCRIPT_NAME'] . '?cid=' . $content_id . '&amp;release=1" target="_self">sp&auml;ter</a></li>';
        }
        echo "</ul>";
    }
    mysqli_free_result($result_content);
}
