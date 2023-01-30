<?php session_start(); ?>

<html>
  <head>
      <!-- üst menü butonları -->
      <input type="button" onclick="location.href='logout.php';" value="Çıkış Yap" />
      <input type="button" onclick="location.href='../../_links/wordfinder/wordfinder.php';" value="Oyuna Dön" />
      <input type="button" onclick="location.href='../../index.html';" value="Index" />
      <!-- üst menü butonları -->

       <title> Admin Paneli </title>

       <style>
       body {background-color: #f2f2f2;}
       .container{
           border-radius: 5px;
           padding: 20px;
           margin-left:30px;
       }
       hr.first{
           margin-top:-10px;
           border: 0;
           height: 1px;
           background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
       }
       hr.second{
            border: 0;
            height: 1px;
            background-image: linear-gradient(to right, #f0f0f0, #0c2b00, #4c5449, #f0f0f0);
            margin-top:-10px;
       }
       .secform{
           margin-left:-19px;
       }
       .firstform{
           margin-top:-35px;
           margin-left:-19px;
       }
       table {
           margin-left: 32px;
           border-collapse: collapse;
           font-family: Tahoma, Geneva, sans-serif;
       }
       table td {
           padding: 15px;
       }
       table thead td {
           background-color: #54585d;
           color: #ffffff;
           font-weight: bold;
           font-size: 13px;
           border: 1px solid #54585d;
       }
       table tbody td {
           color: #636363;
           border: 1px solid black;
       }
       table tbody tr {
           background-color: #f9fafb;
        }
       table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
       input[type=text], select, textarea{
           padding: 5px;
           border: 1px solid #ccc;
           border-radius: 2px;
           box-sizing: border-box;
       }
       input[type=submit] {
           background-color: #54585d;
           color: white;
           padding: 5px 8px;
           border: none;
           border-radius: 4px;
           cursor: pointer;
       }
       input[type=submit].special {
           background-color: #54585d;
           color: white;
           padding: 5px 8px;
           border: none;
           border-radius: 4px;
           cursor: pointer;
           margin-top:-20px;
       }
       input[type=button] {
           background-color: #54585d;
           color: white;
           padding: 5px 8px;
           border: none;
           border-radius: 4px;
           cursor: pointer;
           float:right;
           margin-left:2px;

       }
       p{
           color:red;
       }
       </style>
  </head>
<body>

<?php
        //with pleasure :)
        #variables
        $isid=$_POST["isid"];
        $isword=$_POST["isword"];
        $isquestion=$_POST["isquestion"];
        $isword = str_replace(' ', '', $isword);
        $islength= mb_strlen($isword);
        $ispoint = $islength * 100;
        $infotext;
        $itemCount = 0;
        $isfilter = $_POST["isfilter"];
        #variables
      #CRITICAL
      if(!isset($_SESSION['useradmin'])) // kontrol
       {
           header("Location:login.php");  //login yapılmadıysa yönlendir
       }
       #CRITICAL

       $userinfo = $_SESSION['useradmin'];
       $infotext= "<p align='center'> $userinfo Login Sguccess</p>";

        #TABLO start
        $_conn=mysqli_connect("host","user","pw","db")
        or die('bağlantı hatası'.mysqli_error()); //bağlan

          if(!empty($isfilter)&&$isfilter<11)
          {
              $_command=mysqli_query($_conn,"select * from words_normal where islength='$isfilter'");
          }
          else
          {
          $_command=mysqli_query($_conn,"select * from words_normal"); // ÇEK USERS
          }

          $veri=mysqli_fetch_array($_command);

          echo "<div style='overflow:auto; height:650px; width:650px;'><table border='1'> 	<thead> <tr><td>id</td>	<td>kelime</td><td>soru</td><td>puan</td><td>uzunluk</td></tr> </thead>"; // TABLO BAŞLANGIÇ
          if(mysqli_num_rows($_command)>0) // EĞER VERİ ÇEKİLDİYSE
          {
              do
              {
                  $itemCount++;
                  echo "<tbody><tr> <td>".$veri['id']."</td><td>".$veri['isword']."</td><td>".$veri['isquestion']."</td><td>".$veri['ispoint']."</td><td>".$veri['islength']."</td></tr></tbody>"; // TABLOYA ÇEKİLEN VERİLERİ GİR
              }
              while($veri=mysqli_fetch_array($_command));
              echo "</table></div> <br> <p style='color:brown; text-align:center;'> $itemCount nesne listelendi </p>"; // TABLE BİTİŞ
          }

          else //VERİ ÇEKİLMEDİYSE
          {
              $infotext = "bir seyler ters gitti.."."<a href='management.php'>tekrar denemek için tıklayınız</a>";
          }

          #TABLO end


          #İSUPDATE start
          if(isset($_POST['isupdate'])) //İSUPDATE BUTONUNA TIKLANDIYSA
          {

              $_command=mysqli_query($_conn,"select * from words_normal where id='$isid'"); // BELİRTİLEN id'Yİ AL

              $veri=mysqli_fetch_array($_command);

              if(mysqli_num_rows($_command)>0) //İD BULUNDUYSA
              {
                  if(!empty($isid)&&!empty($isword)&&!empty($isquestion)&&!empty($ispoint)&&!empty($islength))
                  {
                      $_command=mysqli_query($_conn,"update words_normal set isword='$isword', isquestion='$isquestion', ispoint='$ispoint', islength='$islength' where id='$isid'"); // VERİYİ DEĞİŞTİR
                      echo '<script type="text/javascript"> window.open("management.php","_self");</script>'; // SAYFAYI YENİDEN YÜKLE
                  }
                  else
                  {
                      $infotext= "<p align='center'>boş alan bırakmayınız";
                  }
              }
              else //İD BULUNAMADIYSA
              {
                  $infotext= "<p align='center'>userid bulunamadı.";
              }
          }
          #İSUPDATE end


          #İSDELETE start
          if(isset($_POST['isdelete'])) //İSUPDATE BUTONUNA TIKLANDIYSA
          {
              $_command=mysqli_query($_conn,"select * from words_normal where id='$isid'"); // BELİRTİLEN USERİD'Yİ AL

              $veri=mysqli_fetch_array($_command);

              if(mysqli_num_rows($_command)>0) //USERİD BULUNDUYSA
              {
                  $_command=mysqli_query($_conn,"delete from words_normal where id='$isid'");
                  $_command=mysqli_query($_conn,"SET @autoid :=0;");
                  $_command=mysqli_query($_conn," UPDATE words_normal SET id = @autoid:=(@autoid+1);");
                  $_command=mysqli_query($_conn,"ALTER TABLE words_normal AUTO_INCREMENT=1;");

                  echo '<script type="text/javascript"> window.open("management.php","_self");</script>'; // SAYFAYI YENİDEN YÜKLE
              }
              else //USERİD BULUNAMADIYSA
              {
                  $infotext= "<p align='center'>userid bulunamadı.";
              }
          }
          #İSDELETE end

          #İSGETDATA start
          if(isset($_POST['isgetdata'])) //İSUPDATE BUTONUNA TIKLANDIYSA
          {

              $_command=mysqli_query($_conn,"select * from words_normal where id='$isid'");

              $veri=mysqli_fetch_array($_command);

              if(mysqli_num_rows($_command)>0) //USERİD BULUNDUYSA
              {
                  $isword = $veri['isword'];
                  $isquestion = $veri['isquestion'];
              }
              else //USERİD BULUNAMADIYSA
              {
                  $infotext= "<p align='center'>id bulunamadı.";
              }
          }
          #İSGETDATA end

          #İSADD start
          if(isset($_POST['isadd'])) //İSADD BUTONUNA TIKLANDIYSA
          {

              $_command=mysqli_query($_conn,"select * from words_normal where isword='$isword'");

              $veri=mysqli_fetch_array($_command);

              if(mysqli_num_rows($_command)>0) //kelime BULUNDUYSA
              {
                  $infotext= "<p align='center'> bu kelime zaten ekli";
              }
              else //kelime BULUNAMADIYSA
              {
                  $_command=mysqli_query($_conn,"insert into words_normal (isword, isquestion, ispoint, islength) values ('$isword', '$isquestion', '$ispoint', '$islength')");


                  echo '<script type="text/javascript"> window.open("management.php","_self");</script>';
              }
          }
          #İSADD end

          mysqli_close($_conn);
?>
    <!-- düzenleme form -->
<div class="container">
<div class="secform">
    <form method="post">
<script>
//veri girdikten sonra yenileme işleminde formların sağlıklı temizlenmesi ve yeniden form göndermemesi için
    if(window.history.replaceState)
    {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
        <input type="text" name="isword" placeholder="kelime" maxlength="10" value="<?php echo $isword; ?>">
        <input type="text" size="150" name="isquestion" placeholder="soru" value="<?php echo $isquestion; ?>">
        <input type="submit" class="special" name="isadd" value="ekle"/>
        <p>
        <input type="text" name="isid" placeholder="id" value="<?php echo $isid; ?>">
        <input type="submit" class="special" name="isgetdata" value="çek"/>
        <input type="submit" name="isupdate" value="güncelle"/>
        <input type="submit" class="special" name="isdelete" value="sil"/>
        </p>
        <p>
            <input type="text" name="isfilter" placeholder="uzunluk gir">
            <input type="submit" name="islisting" value="listele"/>

        </p>
    </form>
</div>
</div>
    <!-- düzenleme form -->

<hr class="second">

</body>

</html>

<?php
    echo $infotext;
?>
