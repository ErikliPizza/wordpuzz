<html>
<head>
    <style>
        /*Thanks from creator vkash8574 from geeksforgeeks, copied and pasted <3*/
        /* For Firefox */
        progress.redbar::-moz-progress-bar {
            background: red;
        }

        /* For Chrome or Safari */
        progress.redbar::-webkit-progress-value {
            background: red;
        }

        /* For IE10 */
        progress.redbar{
            background: red;
        }

        progress.greenbar::-moz-progress-bar {
            background: green;
        }

        /* For Chrome or Safari */
        progress.greenbar::-webkit-progress-value {
            background: green;
        }

        /* For IE10 */
        progress.greenbar{
            background: green;
        }

        /* popups start */
.overlay {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.7);
  visibility: hidden;
  opacity: 0;
}
.overlay:target {
  visibility: visible;
  opacity: 1;
}

.popup {
  margin: 70px auto;
  padding: 20px;
  background: rgba(225,219,185,255);
  border-radius: 10px;
  width: 30%;
  position: relative;
}

.popup h2 {
  margin-top: 0;
  color: rgb(65, 43, 43);
  font-family: Tahoma, Arial, sans-serif;
}
.popup .close {
  position: absolute;
  bottom: 5px;
  right: 15px;

  font-size: 30px;
  font-weight: bold;
  text-decoration: none;
  color: #333;
}
.popup .close:hover {
  color: #06D85F;
}
.popup .cnt {
  font-family:'Roboto';
  max-height: 30%;
  overflow: auto;
  color: rgb(56, 54, 54);
  font-weight: bold;
  letter-spacing: 2px;
  line-height: 25px;
  }
@media screen and (max-width: 700px){
  .box{
    width: 70%;
  }
  .popup{
    width: 70%;
  }
}
/* popups end */
    </style>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <?php
    $_conn=mysqli_connect("host","user","pw","db_name")
    or die('bağlantı hatası'.mysqli_error()); //bağlan
    ?>
    <script>

    //dinamik
    var str, newscore=0, highscore=0, questionText; //hedef değişkenler
    //dinamik
    //başlangıç
    var readytogo = false;
    var gameTime, timeleft, wrongAnswer = 13, playerName, pasRequest=3;
    //başlangıç
    //getword
    var isLevel=0;
    var items=[];
    var oldWord = 0;
    //getword
    var isRecording = false;
    var audio;

    //sayfa yüklendiğinde
    function getLeader()
    {
        highscore =
        "<?php
        $_command=null;
        $highscore=null;
        $veri=null;
        $_command=mysqli_query($_conn,"select * from leaderBoard");

        $veri=mysqli_fetch_array($_command);
        do
            {
                $highscore = $veri['isleaderScore'];
            }

            while($veri=mysqli_fetch_array($_command));
            echo $highscore;
        ?>";
        playerName =
        "<?php
        $_command=null;
        $name=null;
        $veri=null;
        $_command=mysqli_query($_conn,"select * from leaderBoard");

        $veri=mysqli_fetch_array($_command);
        do
            {
                $name = $veri['isleaderName'];
            }

            while($veri=mysqli_fetch_array($_command));
            echo $name;
        ?>";
        document.getElementById("ishighscore").innerHTML = playerName+"-"+highscore; //yüksek skoru güncelle
    }
    function onAwake()
    {
    getLeader();
    focusAnswer(); //focus ol
    wrongAud = new Audio('../../sounds/isWrong.mp3');
    correctAud = new Audio('../../sounds/isCorrect.mp3');

    }


    //starta basılırsa
    function startButtonClicked()
    {
        getWord(); //leveli başlat ve kelimeyi al
        if(str != null && str != "") //kelime alındıysa
        {
        timeleft = 180; //saniye
        gameTime = setInterval(function(){
        if(timeleft <= 0)
        {
            clearInterval(gameTime); //intervali durdur
            document.getElementById("countdownText").innerHTML = "Süren Bitti!";
            endGame("timeOut"); //oyunu bitir
        }
        else
        {
            document.getElementById("countdownText").innerHTML = timeleft; //kalan zamanı yazdır
        }
        timeleft -= 1; //1 saniye azalt
        }, 1000); //her 1 saniyede fonksiyona gir

        //hazırı true yap, kelimeyi doldur, butonları ayarla
        readytogo = true;
        document.getElementById("startbtn").disabled = true; //başlat butonunu kapat
        document.getElementById("exitbtn").disabled = false; //exit butonunu aç
        document.getElementById("answerbtn").disabled = false; //cevap verme butonunu aç
        document.getElementById("pasbtn").disabled = false; //pas butonunu aç
        fillBox();
        }

        else
        {
            alert("kelime veri tabanlarımızdan alınamadığı için oyun başlatılamıyor!");
            endGame("errorHere");
        }
    }



    function clearBox() //kutuları ve bilgiyi temizle
    {
        document.getElementById("isinfo").innerHTML="";
        for(i = 1; i<=10; i++)
        {
            document.getElementById("box"+i).className = "btn ms-1 btn-danger opacity-25";
            document.getElementById("box"+i).innerHTML = "";
            document.getElementById("box"+i).disabled = true;
        }
    }

    function fillBox() //kutuları doldur ve soruyu değiştir
    {
        document.getElementById("isquestion").innerHTML = questionText;
        for(i = 1; i<=str.length; i++)
        {
            document.getElementById("box"+i).disabled = false;
            document.getElementById("box"+i).innerHTML = "-";
            document.getElementById("box"+i).className = "btn ms-1 btn-warning";
        }
    }

    function enableButtons()
    {
        for(i = 1; i<=32; i++)
        {
            document.getElementById(i).disabled=false;
        }
    }
    function endGame(stiuation)
    {
        let aborted = false;
        if(stiuation == "aborted")
        {
            alert("Oyun iptal edildi, puan hesaplaması yapılamadı.");
            aborted=true;
        }
        else if(stiuation == "timeOut")
        {
            newscore = newscore - 1200;
            alert("Süreniz sona erdi, cezalı puanınız: " + newscore);
        }
        else if(stiuation == "normalEnd")
        {
            newscore = newscore + timeleft * 10;
            alert("Başka kelime kalmadı, puanınız: " + newscore);
        }
        else if(stiuation == "answersDied")
        {
            aborted=true;
            alert("Çok fazla deneme yaptınız, puan hesaplaması yapılamadı.");
        }
        else if(stiuation == "errorHere" ||newscore > 10000) //kötü amaçlı kullanımda veri tabanını korumak için skor kontrolü
        {
            window.location.reload();
        }

        readytogo = false;
        clearInterval(gameTime); //geri sayımı durdur
        document.getElementById("isinfo").innerHTML = null; //bilgiyi temizle
        document.getElementById("islist").innerHTML = null; //listeyi temizle
        document.getElementById("startbtn").disabled = false; //start butonunu aç
        document.getElementById("exitbtn").disabled = true; //exit butonunu kapat
        document.getElementById("answerbtn").disabled = true; //cevapla butonunu kapat
        document.getElementById("pasbtn").disabled = true; //pas butonunu kapat
        document.getElementById("countdownText").innerHTML = null; //countdowntexti temizle
        document.getElementById("isquestion").innerHTML = "Selam!"; //soruyu temizle
        document.getElementById("questionBar").value = null; //soru barını temizle
        document.getElementById("wrongBar").value = null; //wrong barını temizle
        document.getElementById("isscore").innerHTML = "0"; //mevcut skor
        if(newscore > highscore && aborted == false)
        {
            isRecording = true;
            document.getElementById("saveScore").value = newscore;
            window.location.href = '#popup1';
            //veri tabanına kaydet
        }
        isLevel=0; //leveli sıfırla
        newscore=0; //skoru sıfırla
        pasRequest=3
        wrongAnswer=13; //resetle
        enableButtons();
        clearBox();
    }
    //sonlandır


    function exitButtonClicked()
    {
        endGame("aborted");
        document.getElementById("isanswer").value=null;
    }

    function passQuestion()
    {
        if(pasRequest>0)
        {
            getWord();
            if(isLevel<14&&isLevel!=0)
            {
                clearBox();
                fillBox();
                enableButtons();
                document.getElementById("isanswer").value=null;
            }
        }
        pasRequest--;
        if(pasRequest===0)
        document.getElementById("pasbtn").disabled = true;
    }

    function answerQuestion(answer)
    {
        answer = answer.toLocaleUpperCase('tr-TR');
        if(str===answer)
        {
            correctAud.play();
            updateScore(answer.length*100);//önce puanı artır
            getWord();
            if(isLevel<14&&isLevel!=0)
            {
                clearBox();
                fillBox();
                enableButtons();
            }
        }
        else
        {
            wrongAud.play();
            document.getElementById("isinfo").innerHTML = "<span style='color:red'>YANLIŞ CEVAP</span>";
            wrongAnswer--;
            document.getElementById("wrongBar").value = 13 - wrongAnswer;
            if(wrongAnswer<=0)
            {
                endGame("answersDied");
            }
        }
        document.getElementById("isanswer").value=null;
    }

    function updateScore(ispoint)
    {
        newscore += ispoint;
        document.getElementById("isscore").innerHTML = newscore; //skoru güncelle
        if(newscore > highscore)
        {
            document.getElementById("ishighscore").innerHTML = newscore; //yüksek skoru güncelle
        }
    }

    //harfleri yerleştirme
    function lookingForKey(isletter, myid)
    {
        if(readytogo) //hazır mıyız? Starta basıldıysa
        {
        timeleft-=2; //zaman cezası
        document.getElementById(myid).disabled=true; //basılan key butonunu disabled et
        var searcher = new RegExp(isletter,'g'); //harfi ara
        let matcher = str.match(searcher); //bulunan harflerin hepsini matcher içine yazdır
        if(matcher!=null && isletter !="") //eğer harf bulunduysa ve girilen değer boşluk değilse
        {
            let letterNumber = matcher.length; //aynı harften kaç tane bulundu
            document.getElementById("isinfo").innerHTML = letterNumber + "<span style='color:green; text-transform:uppercase;'> harf buldun: " + "\"" +isletter+"\""  + "</span>"; //harf buldun yazdır

            if(letterNumber == 1) //sadece 1 tane harf bulunduysa şunu yap:
            {
                let b = (str.indexOf(isletter) + 1); //bulunan harfin kelimedeki sırasını b'ye aktar (ahmet, h ise b=2)
                document.getElementById("box"+b).innerHTML = isletter;
                document.getElementById("box"+b).className = "btn ms-1 btn-success";
                //let a = document.getElementById("isword").value; //kelimenin kalıbını a'ya aktar (1234..)
                //let newText = a.replace(b, isletter); //bulunan sıranın stringiyle girilen harfi yer değiştir (12345, h ise b=2 ve 1h345 olacak)
                //document.getElementById("isword").value = newText; //değiştirilmiş veriyi fielde geçir

                li = document.createElement('li'); //listeye veriyi döndür, ekle
                li.innerHTML = "<li class='list-group-item bg-secondary'><span style='color:yellow; text-transform:uppercase; font-weight:bold;'>"+ isletter +"</span>" + " harfi <span style='color:white;'>" + b +"</span>. sırada</li>"; //log döndür
                document.getElementById('islist').insertBefore(li, document.getElementById('islist').firstChild); //listeye veriyi döndür, ekle
            }
            else if (letterNumber>1) //eğer aynı harften birden fazla bulunduysa şunu yap:
            {
                let i = letterNumber; //bulunun harf sayısını aktar
                let order = 0; //tanımla
                while(i > 0) //olduğu müddetçe:
                {
                    order = (str.indexOf(isletter, order) + 1); //önceki bulunan kelime kaçıncı sırada? prensip: indexOf("a", 3) olursa, kelimenin 3. harfinden sonra a harfi arar. Sonraki girişinden, a'dan sonraki a'ları arayacak, indexof("a", öncekiaharfininsiranumarasi + 1)

                    li = document.createElement('li'); //listeye veriyi döndür, ekle
                    li.innerHTML = "<li class='list-group-item bg-secondary'><span style='color:yellow; text-transform:uppercase; font-weight:bold;'>"+ isletter +"</span>" + " harfi <span style='color:white;'>" + order +"</span>. sırada</li>";
                    document.getElementById('islist').insertBefore(li, document.getElementById('islist').firstChild); //listeye veriyi döndür, ekle

                    let b = order; //bulunan harfin sıra numarasını aktar
                    document.getElementById("box"+b).innerHTML = isletter;
                    document.getElementById("box"+b).className = "btn ms-1 btn-success";
                    //let a = document.getElementById("isword").value; //kelimenin kalıbını aktar
                    //let newText = a.replace(b, isletter); //bulunan sıra numarasıyla isletter'i değiştir, (3, a) = 12a45
                    //document.getElementById("isword").value = newText; //değiştirilmiş kalıbı fielde geçir
                    //console.log(order);
                    i--; //1 harf girildi, azalt
                }
            }
        }
        //harfleri yerleştirme bitti

        else //herhangi bir karakter eşleşmediyse
        {
            document.getElementById("isinfo").innerHTML = "<span style='color:red; text-transform:uppercase;'>eşleşme yok</span><br>"; //eşleşme yok yazdır
            li = document.createElement('li'); //listeye veriyi döndür, ekle
            li.innerHTML = "<li class='list-group-item bg-secondary'><span style='color:red; text-transform:uppercase; font-weight:bold;'>"+ isletter +"</span><span style='color:brown; text-transform:uppercase; font-weight:bold;'> -100P</span></li>";
            document.getElementById('islist').insertBefore(li, document.getElementById('islist').firstChild); //listeye veriyi döndür, ekle
            newscore-=100;
            document.getElementById("isscore").innerHTML = newscore;
        }

        }
    }
        function getWord()
    {
        isLevel++; //levele başla (0++)
        document.getElementById("questionBar").value = isLevel;
        switch (isLevel)  //taş kağıt makası belirle
        {

            //4 harfliler
            case 1:
            case 2:
                var levelOne = "<?php
                $t=null;
                $_command=mysqli_query($_conn,"select * from words_normal where islength='4'");
                $veri=mysqli_fetch_array($_command);
                do
                {
                    $t .= $veri['isword']."*".$veri['isquestion']."#3355"."#44666";
                }
              while($veri=mysqli_fetch_array($_command));
              echo $t ?>";

              items = levelOne.split('#44666'); //kelime ve soruları bütün olarak böl

              var questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //önceki seçilenle aynı olmadığını garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu parçala ve çıkar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //önceki seçileni al
            break;

            //5 harfliler
            case 3:
            case 4:
                levelOne = "<?php
                $t=null;
                $_command=mysqli_query($_conn,"select * from words_normal where islength='5'");
                $veri=mysqli_fetch_array($_command);
                do
                {
                    $t .= $veri['isword']."*".$veri['isquestion']."#3355"."#44666";
                }
              while($veri=mysqli_fetch_array($_command));
              echo $t ?>";

              items = levelOne.split('#44666'); //kelime ve soruları bütün olarak böl

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //önceki seçilenle aynı olmadığını garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu parçala çıkar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //önceki seçileni al
            break;

            //altı harfliler
            case 5:
            case 6:
                levelOne = "<?php
                $t=null;
                $_command=mysqli_query($_conn,"select * from words_normal where islength='6'");
                $veri=mysqli_fetch_array($_command);
                do
                {
                    $t .= $veri['isword']."*".$veri['isquestion']."#3355"."#44666";
                }
              while($veri=mysqli_fetch_array($_command));
              echo $t ?>";

              items = levelOne.split('#44666'); //kelime ve soruları bütün olarak böl

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //önceki seçilenle aynı olmadığını garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu parçala çıkar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //önceki seçileni al
            break;

            //yedi harfliler
            case 7:
            case 8:
                levelOne = "<?php
                $t=null;
                $_command=mysqli_query($_conn,"select * from words_normal where islength='7'");
                $veri=mysqli_fetch_array($_command);
                do
                {
                    $t .= $veri['isword']."*".$veri['isquestion']."#3355"."#44666";
                }
              while($veri=mysqli_fetch_array($_command));
              echo $t ?>";

              items = levelOne.split('#44666'); //kelime ve soruları bütün olarak böl

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //önceki seçilenle aynı olmadığını garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu parçala çıkar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //önceki seçileni al
            break;

            //sekiz harfliler
            case 9:
                levelOne = "<?php
                $t=null;
                $_command=mysqli_query($_conn,"select * from words_normal where islength='8'");
                $veri=mysqli_fetch_array($_command);
                do
                {
                    $t .= $veri['isword']."*".$veri['isquestion']."#3355"."#44666";
                }
              while($veri=mysqli_fetch_array($_command));
              echo $t ?>";

              items = levelOne.split('#44666'); //kelime ve soruları bütün olarak böl

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //önceki seçilenle aynı olmadığını garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu parçala çıkar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //önceki seçileni al
            break;

            //dokuz harfliler
            case 10:
            case 11:
                levelOne = "<?php
                $t=null;
                $_command=mysqli_query($_conn,"select * from words_normal where islength='9'");
                $veri=mysqli_fetch_array($_command);
                do
                {
                    $t .= $veri['isword']."*".$veri['isquestion']."#3355"."#44666";
                }
              while($veri=mysqli_fetch_array($_command));
              echo $t ?>";

              items = levelOne.split('#44666'); //kelime ve soruları bütün olarak böl

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //önceki seçilenle aynı olmadığını garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu parçala çıkar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //önceki seçileni al
            break;

            //on harfliler
            case 12:
            case 13:
                levelOne = "<?php
                $t=null;
                $_command=mysqli_query($_conn,"select * from words_normal where islength='10'");
                $veri=mysqli_fetch_array($_command);
                do
                {
                    $t .= $veri['isword']."*".$veri['isquestion']."#3355"."#44666";
                }
              while($veri=mysqli_fetch_array($_command));
              echo $t ?>";

              items = levelOne.split('#44666'); //kelime ve soruları bütün olarak böl

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //önceki seçilenle aynı olmadığını garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu parçala çıkar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //önceki seçileni al
            break;

            default:
            endGame("normalEnd");
            break;
        }
        str = str.toLocaleUpperCase('tr-TR'); //kelimeyi büyük harf yap
        questionText = questionText.toLocaleUpperCase('tr-TR');

    }

    function focusAnswer()
    {
        if(isRecording==false)
        document.getElementById("isanswer").focus();
    }
    function sendwithKey(e)
    {
        if(e.keyCode === 13 && readytogo == true)
        {
            document.getElementById("answerbtn").click();
        }
    }
    function dataRecording()
    {
        window.location.href = '#';
        let recordLatency= setInterval(function(){
            window.location.reload();
            clearInterval(recordLatency);
        }, 2000);
    }
    </script>
</head>


<body onload="onAwake()" class="d-flex flex-column min-vh-100" style="background-color: #f9f4ee;">

    <!-- üst panel, start button, exit button, isinfo-->
    <div class="d-flex justify-content-center">

        <div class="btn-toolbar row">
            <div class="btn-group mx-auto w-auto">
                <button class="btn ms-1 mt-1 btn-danger" id="reportbtn" disabled>HATA BİLDİR</button>
                <button class="btn ms-1 mt-1 btn-primary" onclick="startButtonClicked()" id="startbtn">başlat</button>
                <button class="btn ms-1 mt-1 btn-primary" onclick="exitButtonClicked()" id="exitbtn" disabled>çık</button>
                <button class="btn ms-1 mt-1 btn-success" id="mobilbtn" disabled>MOBİL SÜRÜM</button>
            </div>
        </div>
    </div>


    <div class="d-flex justify-content-center mt-4">

        <div class="border rounded-pill rounded-bottom p-3 border-warning" style="background-color: #464442; height: 175px; width: 550px;">
            <div class="btn-toolbar row">
                <div class="btn-group mx-auto mt-5 w-auto">
                    <button class="btn ms-1 btn-danger opacity-25" disabled="false" id="box1"></button>
                    <button class="btn ms-1 btn-danger opacity-25" disabled="true" id="box2"></button>
                    <button class="btn ms-1 btn-danger opacity-25" disabled="true" id="box3"></button>
                    <button class="btn ms-1 btn-danger opacity-25" disabled="true" id="box4"></button>
                    <button class="btn ms-1 btn-danger opacity-25" disabled="true" id="box5"></button>
                    <button class="btn ms-1 btn-danger opacity-25" disabled="true" id="box6"></button>
                    <button class="btn ms-1 btn-danger opacity-25" disabled="true" id="box7"></button>
                    <button class="btn ms-1 btn-danger opacity-25" disabled="true" id="box8"></button>
                    <button class="btn ms-1 btn-danger opacity-25" disabled="true" id="box9"></button>
                    <button class="btn ms-1 btn-danger opacity-25" disabled="true" id="box10"></button>
                </div>
            </div>


            <br>
            <p id="isinfo" align="center" class="mt-auto"></p>
        </div>
    </div>
    <!-- üst panel -->
    <!-- klavye -->
    <div class="d-flex justify-content-center">
        <div class="border rounded-pill rounded-top p-3 overflow-auto border-warning" style="background-color: #464442; height: 175px; width: 550px;">
            <div class="btn-toolbar row">
                <div class="btn-group mx-auto w-auto">
                    <button class="btn ms-1 btn-primary" id="1" onclick="lookingForKey('Q', this.id)" >Q</button>
                    <button class="btn ms-1 btn-primary" id="2" onclick="lookingForKey('W', this.id)" >W</button>
                    <button class="btn ms-1 btn-primary" id="3" onclick="lookingForKey('E', this.id)" >E</button>
                    <button class="btn ms-1 btn-primary" id="4" onclick="lookingForKey('R', this.id)" >R</button>
                    <button class="btn ms-1 btn-primary" id="5" onclick="lookingForKey('T', this.id)" >T</button>
                    <button class="btn ms-1 btn-primary" id="6" onclick="lookingForKey('Y', this.id)" >Y</button>
                    <button class="btn ms-1 btn-primary" id="7" onclick="lookingForKey('U', this.id)" >U</button>
                    <button class="btn ms-1 btn-primary" id="8" onclick="lookingForKey('I', this.id)" >I</button>
                    <button class="btn ms-1 btn-primary" id="9" onclick="lookingForKey('O', this.id)" >O</button>
                    <button class="btn ms-1 btn-primary" id="10" onclick="lookingForKey('P', this.id)" >P</button>
                    <button class="btn ms-1 btn-primary" id="11" onclick="lookingForKey('Ğ', this.id)" >Ğ</button>
                    <button class="btn ms-1 btn-primary" id="12" onclick="lookingForKey('Ü', this.id)" >Ü</button>
                </div>
            </div>
            <div class="btn-toolbar row">
                <div class="btn-group mx-auto mt-1 w-auto">
                    <button class="btn ms-1 btn-primary" id="13" onclick="lookingForKey('A', this.id)" >A</button>
                    <button class="btn ms-1 btn-primary" id="14" onclick="lookingForKey('S', this.id)" >S</button>
                    <button class="btn ms-1 btn-primary" id="15" onclick="lookingForKey('D', this.id)" >D</button>
                    <button class="btn ms-1 btn-primary" id="16" onclick="lookingForKey('F', this.id)" >F</button>
                    <button class="btn ms-1 btn-primary" id="17" onclick="lookingForKey('G', this.id)" >G</button>
                    <button class="btn ms-1 btn-primary" id="18" onclick="lookingForKey('H', this.id)" >H</button>
                    <button class="btn ms-1 btn-primary" id="19" onclick="lookingForKey('J', this.id)" >J</button>
                    <button class="btn ms-1 btn-primary" id="20" onclick="lookingForKey('K', this.id)" >K</button>
                    <button class="btn ms-1 btn-primary" id="21" onclick="lookingForKey('L', this.id)" >L</button>
                    <button class="btn ms-1 btn-primary" id="22" onclick="lookingForKey('Ş', this.id)" >Ş</button>
                    <button class="btn ms-1 btn-primary" id="23" onclick="lookingForKey('İ', this.id)" >İ</button>
                </div>
            </div>
            <div class="btn-toolbar row">
                <div class="btn-group mx-auto mt-1 w-auto">
                    <button class="btn ms-1 btn-primary" id="24" onclick="lookingForKey('Z', this.id)" >Z</button>
                    <button class="btn ms-1 btn-primary" id="25" onclick="lookingForKey('X', this.id)" >X</button>
                    <button class="btn ms-1 btn-primary" id="26" onclick="lookingForKey('C', this.id)" >C</button>
                    <button class="btn ms-1 btn-primary" id="27" onclick="lookingForKey('V', this.id)" >V</button>
                    <button class="btn ms-1 btn-primary" id="28" onclick="lookingForKey('B', this.id)" >B</button>
                    <button class="btn ms-1 btn-primary" id="29" onclick="lookingForKey('N', this.id)" >N</button>
                    <button class="btn ms-1 btn-primary" id="30" onclick="lookingForKey('M', this.id)" >M</button>
                    <button class="btn ms-1 btn-primary" id="31" onclick="lookingForKey('Ö', this.id)" >Ö</button>
                    <button class="btn ms-1 btn-primary" id="32" onclick="lookingForKey('Ç', this.id)" >Ç</button>
                </div>
            </div>
        </div>
    </div>
    <!-- klavye -->

    <div style="text-align: center;">
        <span id="countdownText" class="text-dark">timer</span>
    </div>
    <div class="d-flex justify-content-center">
        <div style="height: 20px; width: 600px;">
            <div class="float-start">
                <progress class="greenbar" value="0" max="13" id="questionBar"></progress>
            </div>
            <div class="float-end">
                <progress class="redbar" value="0" max="12" id="wrongBar"></progress>
            </div>
        </div>
    </div>

    <!-- question bar, isquestion, mainpagebtn, answerbtn, pasbtn-->
    <div class="d-flex justify-content-center">
        <div class="border border-dark rounded-pill rounded-bottom p-3 d-flex justify-content-center overflow-auto" style="background-color: #464442; height: 165px; width: 925px;">
            <!-- question -->
            <span class="lead p-5" style="color: gray; font-size: larger;" id="isquestion">Selam!</span>
            <hr>
            <!-- butonlar -->
        </div>
    </div>
    <!-- question bar -->
    <div class="d-flex justify-content-center">
        <div class="border border-dark rounded-pill rounded-top" style="background-color: #464442; height: 50px; width: 925px;">
            <div class="d-flex justify-content-between p-1">
                <a href="../../index.html"><button class="btn btn-outline-info ms-5" id="mainpagebtn">MENU</button></a>
                <button class="btn btn-outline-warning me-4" id="answerbtn" onclick="answerQuestion(isanswer.value)" disabled>CEVAPLA</button>
                <button class="btn btn-outline-danger me-5" id="pasbtn" onclick="passQuestion()" disabled>PAS</button>
            </div>
        </div>
    </div>

    <!-- log list, islist-->
    <div class="d-flex justify-content-center">
        <div class="border rounded p-3 overflow-auto" style="background-color: #464442; height: 150px; width: 350px;">
            <ul class="list-group" id="islist" style="list-style-type: none;">

            </ul>
        </div>
    </div>
    <!-- log list -->

    <!-- FOOTER, isscore, isanswer, ishighscore-->
    <footer class="page-footer font-small purple pt-4 fixed-bottom">
        <div class="text-center p-1 rounded-2" style="background-color: #ecd17e;">

            <div class="d-flex justify-content-between ">

                <span class="lead ms-3 mt-2" id="isscore">0</span>
                <input type="text" class="ms-5 bg-success rounded-2" id="isanswer" onblur="focusAnswer()" maxlength="10" style="text-align: center; text-transform:uppercase;" onkeypress="sendwithKey(event)">
                <span class="lead ms-3 mt-2 text-success" id="ishighscore">Samo-600</span>

            </div>

        </div>

    </footer>
    <!-- FOOTER -->

</body>
<!-- popup BAŞLANGIÇ -->

  <div id="popup1" class="overlay">
    <div class="popup">
      <h2>Rekor Kırdınız!</h2>
      <div class="cnt">
        <form target="_blank" action="sendtoLeaderboard.php" method="post">
        <input type="text" name="pname" id="isPlayerName" maxlength="13" placeholder="kullanıcı adı"></input>
        <input type="text" name="pscore" id="saveScore" readonly> </input>
        <input type="submit" id="savebtn" value="YOLLA" onclick="dataRecording()"> </input>
        </form>
      </div>
    </div>
  </div>
  <!-- popup BİTİŞ -->
</html>
