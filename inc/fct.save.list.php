<?php
function saveBasket($conn, $saveid)
{
    $sql_basketsave = "SELECT * FROM `basketsave` WHERE `basketid` = '" . $saveid . "'";
    $result_basketsave = mysqli_query($conn, $sql_basketsave);
    $count_basketsave = mysqli_num_rows($result_basketsave);

    if ($count_basketsave === 0) {
        $sql = "INSERT INTO `basketsave` (`basketid`) VALUES ('" . $saveid . "')";
        #echo $sql . '<br>';
        $result = mysqli_query($conn, $sql);

        mysqli_close($conn);
        header('Location: index.php');
    }
}
