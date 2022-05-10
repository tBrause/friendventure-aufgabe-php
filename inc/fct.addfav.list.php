<?php
function saveFav($conn, $cid)
{
    $sql_fav = "SELECT * FROM `fav` WHERE `contentid` = '" . $cid . "'";
    $result_fav = mysqli_query($conn, $sql_fav);
    $count_fav = mysqli_num_rows($result_fav);

    if ($count_fav === 0) {
        $sql = "INSERT INTO `fav` (`contentid`) VALUES ('" . $cid . "')";
        #echo $sql . '<br>';
        $result = mysqli_query($conn, $sql);

        mysqli_close($conn);
        header('Location: index.php');
    }
}
