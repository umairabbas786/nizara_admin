<?php 
    $conn =  $conn = mysqli_connect("localhost",'root','','monkeycr_nizara_admin');
    if(isset($_POST['brand_id'])){
        $brand_id = $_POST['brand_id'];
        $sql = "SELECT * FROM subbrand WHERE brand_id=" . $brand_id;
        $r = $conn->query($sql);
        while($row = mysqli_fetch_assoc($r)){
            echo "<option value=" . $row['id'] . ">" . $row['name'] . "</option>";
        }
    }
?>