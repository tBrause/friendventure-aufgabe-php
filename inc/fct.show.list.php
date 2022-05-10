<?php
function showBasket($conn, $showid)
{
    # Query database 'BASKET'
    $sql = "UPDATE `basket` SET `release`= 1 WHERE `id` = '" . $showid . "'";
    #echo $sql . '<br>';
    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);
    header('Location: index.php');
}
