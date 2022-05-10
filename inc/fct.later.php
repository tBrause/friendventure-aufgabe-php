<?php
function updateContentRelease($conn, $cid)
{
    # Query database 'CONTENT'
    $sql = "UPDATE `content` SET `release`= 0 WHERE `id` = '" . $cid . "'";
    #echo $sql . '<br>';
    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);
    header('Location: index.php');
}
