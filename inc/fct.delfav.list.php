<?php
function deleteFav($conn, $cid)
{
    $sql = "DELETE FROM `fav` WHERE `contentid` = '" . $cid . "'";
    #echo $sql . '<br>';
    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);
    header('Location: index.php');
}
