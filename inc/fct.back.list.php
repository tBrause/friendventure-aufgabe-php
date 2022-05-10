<?php
function updateContentReleaseBack($conn, $cid)
{
    # Query database 'CONTENT'
    $sql = "UPDATE `content` SET `release`= 1 WHERE `id` = '" . $cid . "'";
    #echo $sql . '<br>';
    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);
    header('Location: index.php');
}
