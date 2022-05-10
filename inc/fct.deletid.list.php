<?php
function deleteBasket($conn, $deleteid)
{
    # Query database 'BASKET'
    $sql = "UPDATE `basket` SET `release`= 0 WHERE `id` = '" . $deleteid . "'";
    #echo $sql . '<br>';
    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);
    header('Location: index.php');
}
