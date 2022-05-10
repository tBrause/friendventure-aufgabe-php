<?php
function deleteBasketList($conn, $delbasketid)
{
    $sql = "DELETE FROM `basketsave` WHERE `basketid` = '" . $delbasketid . "'";
    #echo $sql . '<br>';
    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);
    header('Location: index.php');
}
