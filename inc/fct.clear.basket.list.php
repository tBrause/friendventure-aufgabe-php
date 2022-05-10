<?php
function clearBasketList($conn, $clearid)
{
    # Query database 'BASKET'
    $sql = "UPDATE `content` SET `done`= 0 WHERE `basketid` = '" . $clearid . "'";
    #echo $sql . '<br>';
    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);
    header('Location: index.php');
}
