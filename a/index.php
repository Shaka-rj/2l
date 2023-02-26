<?php
session_start();
include "db.php";
include "../lib/to_64/to_64.php";

$to = new to();

$request = $_SERVER['REQUEST_URI'];
$request = substr($request, 3);

if ($request == ""){
    $type = 1; //index
} elseif ($request == "create"){
    $type = 2; //yaratilgani
} else {//yullanma
    $sql = "SELECT full_link, short_link FROM a_link WHERE short_link='$request' LIMIT 1";
    $result = $baza->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $full_link = $row['full_link'];
        header("Location: $full_link");
        $type = 4;
    } else {
        $type = 3;
    }
}


if(count($_POST)>0){
	if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] === $_POST['keystring']){
	    if (strlen($_POST['url']) > 3){
            $full_link = $baza -> real_escape_string($_POST['url']);
            $sql = "INSERT INTO a_link(full_link) VALUES ('$full_link')";
            if ($baza->query($sql) === TRUE){
                $last_id = $baza->insert_id;
                $short_link = $to -> encode($last_id);
                $sql = "UPDATE a_link SET short_link='$short_link' WHERE id=$last_id";
                if ($baza->query($sql) === TRUE){
                    $type = 2;
                } else {
                    $xato = "Bizning bazamizga yangilashda xatolik bo'ldi.";
		            $type = 1;
                }
            } else {
                $xato = "Bizning bazamizga kiritishda xatolik bo'ldi.";
		        $type = 1;
            }
	    } else {
	        $xato = "Url manzil juda qisqa.";
		    $type = 1;
	    }
	}else{
		$xato = "Tekshiruv kodini xato yozdingiz";
		$type = 1;
	}
	unset($_SESSION['captcha_keystring']);
} else {
    $type = 1;
}


?>
<html>
	<head>
		<title>
			Havolalarni qisqartirish
		</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="UTF-8">
		<meta name="description" content="Uzun internet manzillarini qisqartirish sayti.">
         <meta name="keywords" content="2l, 2link, url, shortlink, qisqa link">
         <meta name="author" content="Shaka_rj">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="a.css">
		<link rel="icon" type="image/x-icon" href="favicon.ico">
	</head>
	<body>
		<div class="asosiy">
			<div class="b1"><b>2L.uz</b> uzun internet manzillarini qisqartirish sayti.</div>
			<br>
			<?php
			if ($type == 1){
			?>
			<div class="v1">
				<p>Oddiy url manzillar uchun qisqartirish</p>
				<p>Manzilni kiriting: 
				<br>
                <b style="color:red;"><?php echo $xato ?></b></p>
				<script>
					function jsqoyish(){
						navigator.clipboard.readText()
						.then(txt => {
							document.getElementById("url").value = txt;
						})
					}
            	</script>
				<form autocomplete="off" action="create" method="POST">
					<input type="text" id="url" name="url" value="<?php echo $_POST['url']?>" required>
					<input type="button" id="qoyish" value="Qo'yish" onclick="jsqoyish()"/><br>
                    <img src="../lib/kcaptcha/?<?php echo session_name()?>=<?php echo session_id()?>"><br>
					<input type="text" id="rand" name="keystring" placeholder="Kodni yozing">
					<input type="submit" name="submit" id="yaratish" value="Yaratish">
				</form>
			</div>
			<?php
			} elseif ($type == 2){
			?>
			<div class="natija">
                <script>
                    function myFunction() {
                      var copyText = document.getElementById("qisqa");
                      copyText.select();
                      copyText.setSelectionRange(0, 99999);
                      navigator.clipboard.writeText(copyText.value);
                      alert("Nusxalandi: " + copyText.value);
                    }
                </script>
                    <p>Sizning qisqartirilgan url manzil:</p>
                    <input type="text" value="<?="https://2l.uz/a/".$short_link?>" id="qisqa">
                    <button onclick="myFunction()">Nusxa</button>
                <div class="uzun_url">
                    <p>Sizning url manzilingiz:</p>
                    <div class="url" style="word-wrap: break-word;">
                        <?=$_POST['url']?>
                    </div>
                </div>
                <br>
                <a href="https://2l.uz" id="yana">Yana yaratish</a>
            </div>
			<?php
			} elseif ($type == 3){
			?>
			<div class="natija">
                <p>Ushbu manzil topilmadi:</p>
                <br>
                <a href="https://2l.uz" id="yana">Yangi yaratish</a>
            </div>
			<?php
			} elseif ($type == 4){
			?>
			<div class="natija">
                <p>Kechirasiz yunaltira olmadim pastdagi havola orqali kerakligiga kiring.</p>
                <br>
                <a href="<?=$full_link?>" id="yana"><?=$full_link?></a>
            </div>
			<?php
			}
			?>
		</div>
	</body>
</html>
