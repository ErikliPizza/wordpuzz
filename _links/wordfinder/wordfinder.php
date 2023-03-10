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
    or die('ba??lant?? hatas??'.mysqli_error()); //ba??lan
    ?>
    <script>

    //dinamik
    var str, newscore=0, highscore=0, questionText; //hedef de??i??kenler
    //dinamik
    //ba??lang????
    var readytogo = false;
    var gameTime, timeleft, wrongAnswer = 13, playerName, pasRequest=3;
    //ba??lang????
    //getword
    var isLevel=0;
    var items=[];
    var oldWord = 0;
    //getword
    var isRecording = false;
    var audio;

    //sayfa y??klendi??inde
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
        document.getElementById("ishighscore").innerHTML = playerName+"-"+highscore; //y??ksek skoru g??ncelle
    }
    function onAwake()
    {
    getLeader();
    focusAnswer(); //focus ol
    wrongAud = new Audio('../../sounds/isWrong.mp3');
    correctAud = new Audio('../../sounds/isCorrect.mp3');

    }


    //starta bas??l??rsa
    function startButtonClicked()
    {
        getWord(); //leveli ba??lat ve kelimeyi al
        if(str != null && str != "") //kelime al??nd??ysa
        {
        timeleft = 180; //saniye
        gameTime = setInterval(function(){
        if(timeleft <= 0)
        {
            clearInterval(gameTime); //intervali durdur
            document.getElementById("countdownText").innerHTML = "S??ren Bitti!";
            endGame("timeOut"); //oyunu bitir
        }
        else
        {
            document.getElementById("countdownText").innerHTML = timeleft; //kalan zaman?? yazd??r
        }
        timeleft -= 1; //1 saniye azalt
        }, 1000); //her 1 saniyede fonksiyona gir

        //haz??r?? true yap, kelimeyi doldur, butonlar?? ayarla
        readytogo = true;
        document.getElementById("startbtn").disabled = true; //ba??lat butonunu kapat
        document.getElementById("exitbtn").disabled = false; //exit butonunu a??
        document.getElementById("answerbtn").disabled = false; //cevap verme butonunu a??
        document.getElementById("pasbtn").disabled = false; //pas butonunu a??
        fillBox();
        }

        else
        {
            alert("kelime veri tabanlar??m??zdan al??namad?????? i??in oyun ba??lat??lam??yor!");
            endGame("errorHere");
        }
    }



    function clearBox() //kutular?? ve bilgiyi temizle
    {
        document.getElementById("isinfo").innerHTML="";
        for(i = 1; i<=10; i++)
        {
            document.getElementById("box"+i).className = "btn ms-1 btn-danger opacity-25";
            document.getElementById("box"+i).innerHTML = "";
            document.getElementById("box"+i).disabled = true;
        }
    }

    function fillBox() //kutular?? doldur ve soruyu de??i??tir
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
            alert("Oyun iptal edildi, puan hesaplamas?? yap??lamad??.");
            aborted=true;
        }
        else if(stiuation == "timeOut")
        {
            newscore = newscore - 1200;
            alert("S??reniz sona erdi, cezal?? puan??n??z: " + newscore);
        }
        else if(stiuation == "normalEnd")
        {
            newscore = newscore + timeleft * 10;
            alert("Ba??ka kelime kalmad??, puan??n??z: " + newscore);
        }
        else if(stiuation == "answersDied")
        {
            aborted=true;
            alert("??ok fazla deneme yapt??n??z, puan hesaplamas?? yap??lamad??.");
        }
        else if(stiuation == "errorHere" ||newscore > 10000) //k??t?? ama??l?? kullan??mda veri taban??n?? korumak i??in skor kontrol??
        {
            window.location.reload();
        }

        readytogo = false;
        clearInterval(gameTime); //geri say??m?? durdur
        document.getElementById("isinfo").innerHTML = null; //bilgiyi temizle
        document.getElementById("islist").innerHTML = null; //listeyi temizle
        document.getElementById("startbtn").disabled = false; //start butonunu a??
        document.getElementById("exitbtn").disabled = true; //exit butonunu kapat
        document.getElementById("answerbtn").disabled = true; //cevapla butonunu kapat
        document.getElementById("pasbtn").disabled = true; //pas butonunu kapat
        document.getElementById("countdownText").innerHTML = null; //countdowntexti temizle
        document.getElementById("isquestion").innerHTML = "Selam!"; //soruyu temizle
        document.getElementById("questionBar").value = null; //soru bar??n?? temizle
        document.getElementById("wrongBar").value = null; //wrong bar??n?? temizle
        document.getElementById("isscore").innerHTML = "0"; //mevcut skor
        if(newscore > highscore && aborted == false)
        {
            isRecording = true;
            document.getElementById("saveScore").value = newscore;
            window.location.href = '#popup1';
            //veri taban??na kaydet
        }
        isLevel=0; //leveli s??f??rla
        newscore=0; //skoru s??f??rla
        pasRequest=3
        wrongAnswer=13; //resetle
        enableButtons();
        clearBox();
    }
    //sonland??r


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
            updateScore(answer.length*100);//??nce puan?? art??r
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
            document.getElementById("isinfo").innerHTML = "<span style='color:red'>YANLI?? CEVAP</span>";
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
        document.getElementById("isscore").innerHTML = newscore; //skoru g??ncelle
        if(newscore > highscore)
        {
            document.getElementById("ishighscore").innerHTML = newscore; //y??ksek skoru g??ncelle
        }
    }

    //harfleri yerle??tirme
    function lookingForKey(isletter, myid)
    {
        if(readytogo) //haz??r m??y??z? Starta bas??ld??ysa
        {
        timeleft-=2; //zaman cezas??
        document.getElementById(myid).disabled=true; //bas??lan key butonunu disabled et
        var searcher = new RegExp(isletter,'g'); //harfi ara
        let matcher = str.match(searcher); //bulunan harflerin hepsini matcher i??ine yazd??r
        if(matcher!=null && isletter !="") //e??er harf bulunduysa ve girilen de??er bo??luk de??ilse
        {
            let letterNumber = matcher.length; //ayn?? harften ka?? tane bulundu
            document.getElementById("isinfo").innerHTML = letterNumber + "<span style='color:green; text-transform:uppercase;'> harf buldun: " + "\"" +isletter+"\""  + "</span>"; //harf buldun yazd??r

            if(letterNumber == 1) //sadece 1 tane harf bulunduysa ??unu yap:
            {
                let b = (str.indexOf(isletter) + 1); //bulunan harfin kelimedeki s??ras??n?? b'ye aktar (ahmet, h ise b=2)
                document.getElementById("box"+b).innerHTML = isletter;
                document.getElementById("box"+b).className = "btn ms-1 btn-success";
                //let a = document.getElementById("isword").value; //kelimenin kal??b??n?? a'ya aktar (1234..)
                //let newText = a.replace(b, isletter); //bulunan s??ran??n stringiyle girilen harfi yer de??i??tir (12345, h ise b=2 ve 1h345 olacak)
                //document.getElementById("isword").value = newText; //de??i??tirilmi?? veriyi fielde ge??ir

                li = document.createElement('li'); //listeye veriyi d??nd??r, ekle
                li.innerHTML = "<li class='list-group-item bg-secondary'><span style='color:yellow; text-transform:uppercase; font-weight:bold;'>"+ isletter +"</span>" + " harfi <span style='color:white;'>" + b +"</span>. s??rada</li>"; //log d??nd??r
                document.getElementById('islist').insertBefore(li, document.getElementById('islist').firstChild); //listeye veriyi d??nd??r, ekle
            }
            else if (letterNumber>1) //e??er ayn?? harften birden fazla bulunduysa ??unu yap:
            {
                let i = letterNumber; //bulunun harf say??s??n?? aktar
                let order = 0; //tan??mla
                while(i > 0) //oldu??u m??ddet??e:
                {
                    order = (str.indexOf(isletter, order) + 1); //??nceki bulunan kelime ka????nc?? s??rada? prensip: indexOf("a", 3) olursa, kelimenin 3. harfinden sonra a harfi arar. Sonraki giri??inden, a'dan sonraki a'lar?? arayacak, indexof("a", ??ncekiaharfininsiranumarasi + 1)

                    li = document.createElement('li'); //listeye veriyi d??nd??r, ekle
                    li.innerHTML = "<li class='list-group-item bg-secondary'><span style='color:yellow; text-transform:uppercase; font-weight:bold;'>"+ isletter +"</span>" + " harfi <span style='color:white;'>" + order +"</span>. s??rada</li>";
                    document.getElementById('islist').insertBefore(li, document.getElementById('islist').firstChild); //listeye veriyi d??nd??r, ekle

                    let b = order; //bulunan harfin s??ra numaras??n?? aktar
                    document.getElementById("box"+b).innerHTML = isletter;
                    document.getElementById("box"+b).className = "btn ms-1 btn-success";
                    //let a = document.getElementById("isword").value; //kelimenin kal??b??n?? aktar
                    //let newText = a.replace(b, isletter); //bulunan s??ra numaras??yla isletter'i de??i??tir, (3, a) = 12a45
                    //document.getElementById("isword").value = newText; //de??i??tirilmi?? kal??b?? fielde ge??ir
                    //console.log(order);
                    i--; //1 harf girildi, azalt
                }
            }
        }
        //harfleri yerle??tirme bitti

        else //herhangi bir karakter e??le??mediyse
        {
            document.getElementById("isinfo").innerHTML = "<span style='color:red; text-transform:uppercase;'>e??le??me yok</span><br>"; //e??le??me yok yazd??r
            li = document.createElement('li'); //listeye veriyi d??nd??r, ekle
            li.innerHTML = "<li class='list-group-item bg-secondary'><span style='color:red; text-transform:uppercase; font-weight:bold;'>"+ isletter +"</span><span style='color:brown; text-transform:uppercase; font-weight:bold;'> -100P</span></li>";
            document.getElementById('islist').insertBefore(li, document.getElementById('islist').firstChild); //listeye veriyi d??nd??r, ekle
            newscore-=100;
            document.getElementById("isscore").innerHTML = newscore;
        }

        }
    }
        function getWord()
    {
        isLevel++; //levele ba??la (0++)
        document.getElementById("questionBar").value = isLevel;
        switch (isLevel)  //ta?? ka????t makas?? belirle
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

              items = levelOne.split('#44666'); //kelime ve sorular?? b??t??n olarak b??l

              var questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //??nceki se??ilenle ayn?? olmad??????n?? garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu par??ala ve ????kar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //??nceki se??ileni al
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

              items = levelOne.split('#44666'); //kelime ve sorular?? b??t??n olarak b??l

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //??nceki se??ilenle ayn?? olmad??????n?? garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu par??ala ????kar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //??nceki se??ileni al
            break;

            //alt?? harfliler
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

              items = levelOne.split('#44666'); //kelime ve sorular?? b??t??n olarak b??l

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //??nceki se??ilenle ayn?? olmad??????n?? garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu par??ala ????kar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //??nceki se??ileni al
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

              items = levelOne.split('#44666'); //kelime ve sorular?? b??t??n olarak b??l

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //??nceki se??ilenle ayn?? olmad??????n?? garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu par??ala ????kar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //??nceki se??ileni al
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

              items = levelOne.split('#44666'); //kelime ve sorular?? b??t??n olarak b??l

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //??nceki se??ilenle ayn?? olmad??????n?? garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu par??ala ????kar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //??nceki se??ileni al
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

              items = levelOne.split('#44666'); //kelime ve sorular?? b??t??n olarak b??l

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //??nceki se??ilenle ayn?? olmad??????n?? garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu par??ala ????kar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //??nceki se??ileni al
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

              items = levelOne.split('#44666'); //kelime ve sorular?? b??t??n olarak b??l

              questSelector =  Math.floor(Math.random()*(items.length-1));

              if(questSelector == oldWord) //??nceki se??ilenle ayn?? olmad??????n?? garantile
              {
                  if(questSelector<=0)
                  questSelector++;
                  else
                  questSelector--;
              }

              questionText = items[questSelector].substring(items[questSelector].indexOf("*") + 1, items[questSelector].lastIndexOf("#3355")); //soruyu par??ala ????kar

              str = items[questSelector].split('*')[0]; //kelimeyi al

              oldWord = questSelector; //??nceki se??ileni al
            break;

            default:
            endGame("normalEnd");
            break;
        }
        str = str.toLocaleUpperCase('tr-TR'); //kelimeyi b??y??k harf yap
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

    <!-- ??st panel, start button, exit button, isinfo-->
    <div class="d-flex justify-content-center">

        <div class="btn-toolbar row">
            <div class="btn-group mx-auto w-auto">
                <button class="btn ms-1 mt-1 btn-danger" id="reportbtn" disabled>HATA B??LD??R</button>
                <button class="btn ms-1 mt-1 btn-primary" onclick="startButtonClicked()" id="startbtn">ba??lat</button>
                <button class="btn ms-1 mt-1 btn-primary" onclick="exitButtonClicked()" id="exitbtn" disabled>????k</button>
                <button class="btn ms-1 mt-1 btn-success" id="mobilbtn" disabled>MOB??L S??R??M</button>
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
    <!-- ??st panel -->
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
                    <button class="btn ms-1 btn-primary" id="11" onclick="lookingForKey('??', this.id)" >??</button>
                    <button class="btn ms-1 btn-primary" id="12" onclick="lookingForKey('??', this.id)" >??</button>
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
                    <button class="btn ms-1 btn-primary" id="22" onclick="lookingForKey('??', this.id)" >??</button>
                    <button class="btn ms-1 btn-primary" id="23" onclick="lookingForKey('??', this.id)" >??</button>
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
                    <button class="btn ms-1 btn-primary" id="31" onclick="lookingForKey('??', this.id)" >??</button>
                    <button class="btn ms-1 btn-primary" id="32" onclick="lookingForKey('??', this.id)" >??</button>
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
<!-- popup BA??LANGI?? -->

  <div id="popup1" class="overlay">
    <div class="popup">
      <h2>Rekor K??rd??n??z!</h2>
      <div class="cnt">
        <form target="_blank" action="sendtoLeaderboard.php" method="post">
        <input type="text" name="pname" id="isPlayerName" maxlength="13" placeholder="kullan??c?? ad??"></input>
        <input type="text" name="pscore" id="saveScore" readonly> </input>
        <input type="submit" id="savebtn" value="YOLLA" onclick="dataRecording()"> </input>
        </form>
      </div>
    </div>
  </div>
  <!-- popup B??T???? -->
</html>
