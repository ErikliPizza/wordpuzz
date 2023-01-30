<?php

    $pname = $_POST["pname"];
    $pscore = $_POST["pscore"];
    $oldscore = 0;
    if($pname==null||empty($pname)||$pname==""||$pname==" ")
    {
        $pname="whatacrk";
    }
    $_conn=mysqli_connect("ip","user","pw","db_name")
    or die('bağlantı hatası'.mysqli_error()); //bağlan

    $_command=mysqli_query($_conn,"select * from leaderBoard");

        $veri=mysqli_fetch_array($_command);

        if(mysqli_num_rows($_command)>0)
        {
            do
            {
                $oldScore = $veri['isleaderScore'];
            }

            while($veri=mysqli_fetch_array($_command));
        }
        else
        {
            echo "tablo çekilemedi";
        }

        if($pscore>$oldscore)
        {
            $_command=mysqli_query($_conn,"update leaderBoard set isleaderName='$pname', isleaderScore='$pscore'");
        }
        echo "<script>window.close();</script>";

?>
