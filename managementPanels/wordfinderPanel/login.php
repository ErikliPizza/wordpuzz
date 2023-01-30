<html>
<head>
<meta charset="utf-8">
<title> Admin Girişi </title>

<script>
//boşluk kontrolü
function validateForm() {
    var x = document.forms["loginform"]["userid"].value;
    var y = document.forms["loginform"]["userpw"].value;
    if (x == "" || y == "") {
      alert("Lütfen alanları doldurun");
      return false;
    }
  }
//boşluk kontrolü
</script>

</head>

<body>

    <!-- login form -->
    <form name="loginform" action="" onsubmit="return validateForm()" method="post" required>
        <p align="center"><input type="text" placeholder="Id" name="userid">
        <p align="center"><input type="password" placeholder="Şifre" name="userpw">
        <p align="center"><input type="submit" value="giriş yap" name="login">
    </form>
    <!-- login form -->

    <!-- navigate -->
    <footer>
        <hr>
        <a href="../../index.html"> <button>Anasayfa</button> </a>
    </footer>
    <!-- navigate -->
</body>

</html>


<!-- php başla-->
<?php

//session kontrol
session_start();
if(isset($_SESSION['useradmin'])) //daha önce giriş yapıldı ise direkt yönlendir
 {
    header("Location:management.php");
 }
//session kontrol

//veri al
$userid=$_POST["userid"];
$userpw=$_POST["userpw"];
//veri al


if($userid!="" && $userpw!="") // alanlar boş değilse
{

$_conn=mysqli_connect("ip","user","pw","db_name")
or die('bağlantı hatası'.mysqli_error()); //bağlan

$_command=mysqli_query($_conn,"select * from users where userid='$userid'and userpw='$userpw'"); //query yolla

$veri=mysqli_fetch_array($_command); //çek

    if(mysqli_num_rows($_command)>0) //çekilen veri 0'dan fazla mı
    {
        $_SESSION['useradmin']=$userid; //session tanımla
        mysqli_close($_conn); //bağlantıyı kapat
        echo '<script type="text/javascript"> window.open("management.php","_self");</script>'; //managementi yükle
    }
    else //veri çekilmediyse
    {
        echo "<p align='center'>hatalı giriş";
        mysqli_close($_conn);
    }
}
?>

<!-- php bit-->
