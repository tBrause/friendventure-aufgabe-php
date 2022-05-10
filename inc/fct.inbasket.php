<?php
function updateContent($conn, $cid)
{
    # Query database 'CONTENT'
    $sql = "UPDATE `content` SET `done`= 1 WHERE `id` = '" . $cid . "'";
    #echo $sql . '<br>';
    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);
    header('Location: index.php');
}
