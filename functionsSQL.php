<?php
include ('functionsHTML.php');
//mostrando noticias na home page
function showNews($conn){   //requer noticia-tum.jpg no diretorio imagens
	$sqlNews = mysqli_query($conn, "SELECT * FROM news WHERE status='show' ORDER BY id LIMIT 3");

	while($row = mysqli_fetch_array($sqlNews)){
		$icon = $row['icon'];
		$author = $row['author'];
		$title = $row['title'];
        $content = substr($row['content'], 0, 50);
        $dia = $row['dia'];
        $mes = $row['mes'];
        $id = $row['id'];
        $views = $row['views'];

        if($icon == ''){
        	$icon = "imagens/noticia-tumb.jpg";
        }
        newsListDisplay($icon, $title, $content, $author, $dia, $mes, $views, $id);
	}
}
//expandindo a noticia
function returnNew($conn, $id){
	$new = mysqli_query($conn, "SELECT * FROM news WHERE id='$id'");
	if($new != null){
		$noticia = mysqli_fetch_array($new, MYSQLI_ASSOC);
		$title = $noticia['title'];
	    $content = $noticia['content'];
	    $dia = $noticia['dia'];
	    $mes = $noticia['mes'];
	    $image = $noticia['image'];
	    if($image == ''){
	    	$image = "http://darkeden.ddns.net/imagens/Dark-Eden-Origin_Banner.jpg";
	    }
	    newsDisplay($image, $title, $content, $dia, $mes);	
		mysqli_query($conn,"UPDATE news SET views=views+1 WHERE id = '$id'");
	}

}
//mostrando lista de itens do market
function marketItems($conn, $kind){
	$item = mysqli_query($conn, "SELECT * FROM `GoodsListInfo` WHERE `Kind`='$kind' AND `Pay`='PAY' ORDER BY `GoodsID`");
	
	while ($row = mysqli_fetch_array($item)) {
		$iimg = "$row[Img]";
		$iname = "$row[Name]";
		$idesc = "$row[Description]";
		$ilim = "$row[Limited]";
		$hour = "$row[Hour]";
		$GoodsID = "$row[GoodsID]";
		if($ilim == "UNLIMITED") { 
			$ilim = "Unlimited"; 
		}
		if($ilim == "LIMITED") { 
			$ilim = "<font color='#f7ff7b'>$hour Hours</font>"; 
		}
		$inum = "$row[Num]";
		if($inum == "0") { 
			$inum = "1"; 
		}
		$iprice = "$row[Price]";
		$irace = "$row[Race]";

		if($iimg == ''){
			$iimg = "images/Market/006.jpg";
		}
		listItem($GoodsID, $iimg, $irace, $iname, $idesc, $hour, $ilim, $inum, $iprice);
	}

}
//mostrando um item selecionado do market
function showItem($conn, $GoodsID, $login){
	$itemViewQ = mysqli_query($conn, "SELECT * FROM GoodsListInfo WHERE GoodsID='$GoodsID'");
	$itemView = mysqli_fetch_array($itemViewQ, MYSQLI_ASSOC);
	$item_img = $itemView['Img'];
	$item_name = $itemView['Name'];
	$item_desc = $itemView['Description'];
	$item_ilim = $itemView['Limited'];
	$item_num = $itemView['Num'];
	$item_price = $itemView['Price'];
	$hour = $itemView['Hour'];
	$item_race = $itemView['Race'];
	if($item_race == ''){
		$item_race = "Common";
	}
	displayItem($GoodsID, $item_img, $item_race, $item_name, $item_desc, $hour, $item_ilim, $item_num, $item_price);
	selectChar($conn, $login, $GoodsID);
	
}
//realizando a compra do item
function buyItem($conn, $login, $password, $char, $item){
    $selectAcc = mysqli_query($conn, "SELECT * FROM Player WHERE PlayerID = '$login' and Password = PASSWORD('$password')");
    $account = mysqli_fetch_array($selectAcc, MYSQLI_ASSOC);
   	$selectItem = mysqli_query($conn, "SELECT * FROM GoodsListInfo WHERE GoodsID = '$item' ");
	$itemSelected = mysqli_fetch_array($selectItem, MYSQLI_ASSOC);
    if($account && $itemSelected){

    	$item_sel_price = $itemSelected['Price'];
		$item_sel_race = $itemSelected['Race'];
		$selectRaces = $itemSelected['Description'];
    	$selectRace = mysqli_query($conn, "SELECT * FROM Slayer WHERE PlayerID = '$login' AND Name = '$char' ");
    	$raceSel = mysqli_fetch_array($selectRace, MYSQLI_ASSOC);
    	$ccRace = $raceSel['Race'];
  
    	if(($account['ShopPoints'] >= $item_sel_price) && ($ccRace == $item_sel_race || $item_sel_race == "COMMON")){
    		$buy1 = "INSERT INTO `GoodsListObject` (`BuyID`,`ID`,`World`,`PlayerID`,`Name`,`GoodsID`,`Num`,`Status`) VALUES ('$item','',1,'".$login."','$char','$item',1,'NOT')";
    		mysqli_query($conn, "UPDATE `Player` SET `ShopPoints` = `ShopPoints`-$item_sel_price WHERE `PlayerID` = '$login'");
    		mysqli_query($conn, $buy1);

    		echo '</br></br></br></br><h1>You bought it!</h1> </br></br>
    		<div class="bnt-titulo">
								<a href="marketBuy.php?item='.$item.'">Go back</a>
							</div>
    		';
    	}
    	elseif($account['ShopPoints'] < $item_sel_price){
    		echo '</br></br></br></br>You dont have enough points to buy this item </br></br>
    		<div class="bnt-titulo">
								<a href="marketBuy.php?item='.$item.'">Go back</a>
							</div>
    		';
    	}
    	else{
    		echo '</br></br></br></br>This item is not for the race of the selected character </br></br>
    		<div class="bnt-titulo">
								<a href="marketBuy.php?item='.$item.'">Go back</a>
							</div>
    		';
    	}          		
	}
}
//escolhendo o personagem a receber o item
function selectChar($conn, $login, $item){
	$active = "ACTIVE";
	$charSelection = mysqli_query($conn, "SELECT Name, Race FROM Slayer WHERE PlayerID = '$login' AND Active = '$active' ");
	$itemQ = mysqli_query($conn, "SELECT * FROM GoodsListInfo WHERE GoodsID = '$item' ");
	$itemARR = mysqli_fetch_array($itemQ, MYSQLI_ASSOC);
	$itemRace = $itemARR['Race'];

	while ($row = mysqli_fetch_array($charSelection)) {
		$cName = "$row[Name]";
		$raceName = "$row[Race]";
		$cRace = substr("$row[Race]", 0, 4);
		if(($itemRace == $raceName) || ($itemRace == "COMMON")){
			echo ' 
					</br><div class="bnt-titulo">
									<a href="marketFinish.php?item='.$item.'&&char='.$cName.'">Send to '.$cRace.' - '.$cName.'</a>
								</div></br></br>';
		}
	}
}

function marketCategories(){
	listCategories();
}
 //mostrando todos os personagens de classe em ordem descendente
function rankSlay($conn, $limit){
    $sqlResult = mysqli_query($conn, "SELECT Name, BladeLevel+SwordLevel+GunLevel+EnchantLevel+HealLevel+AdvancementClass AS Level, Rank FROM Slayer WHERE Active='ACTIVE' ORDER BY Level DESC LIMIT $limit");   

    while($row = mysqli_fetch_array($sqlResult)){
        $name = $row['Name'];
        $level = $row['Level'];
        listRank('slayer', $name, $level);
    }
}  
//mostrando todos os personagens de classe em ordem descendente
function rankVamp($conn, $limit){
    $sqlResult =  mysqli_query($conn, "SELECT Name, Level+AdvancementClass AS Level, Rank FROM Vampire WHERE Active='ACTIVE'  ORDER BY Level DESC LIMIT $limit");

    while($row = mysqli_fetch_array($sqlResult)){ 
        $name = $row['Name'];
        $level = $row['Level'];
        listRank('vampire', $name, $level);
    }        
}
//mostrando todos os personagens de classe em ordem descendente
function rankOust($conn, $limit){
    $sqlResult =  mysqli_query($conn, "SELECT Name, Level+AdvancementClass AS Level, Rank FROM Ousters WHERE Active='ACTIVE'  ORDER BY Level DESC LIMIT $limit");

    while($row = mysqli_fetch_array($sqlResult)){  
        $name = $row['Name'];
        $level = $row['Level'];
        listRank('ouster', $name, $level);
    }       
}
//confirmação de pagamento
function comfirmTransaction($conn, $PlayerID, $key){
	if($PlayerID != null && $key != ''){
		mysqli_query($conn, "INSERT INTO DonateKey (PlayerID, TransactionID, STATUS) VALUES ('$PlayerID', '$key', 'PENDING')");
		alertScript("Sent, wait for an operator confirm your donate and insert your Bloody Points!");
		
	}else{
		alertScript("Insert a proper key!");
		echo '</br></br><center><h1>Insert a proper key</h1><center>';
	}
}
//verificar privilégios
function verifyAccountLevel($conn, $account, $password){
	$sqlConta = "SELECT * FROM Player WHERE PlayerID = '".$account."' and Password = PASSWORD('".$password."')";
	$account = mysqli_query($conn,$sqlConta);
	$accData = mysqli_fetch_array($account, MYSQLI_ASSOC);

	if($accData["GM_ID"] >= 1 && $accData["GM_ID"] < 100){
		return "ADM";
	}elseif($accData["GM_ID"] >= 100 && $accData["GM_ID"] < 200){
		return "GM";
	}elseif($accData["GM_ID"] >= 200 && $accData["GM_ID"] < 300){
		return "HELPER";
	}else{
		return "PLAYER";
	}
}
//autenticar conta
function verifyAccountData($conn, $account, $password){
	$sqlConta = "SELECT * FROM Player WHERE PlayerID = '".$account."' and Password = PASSWORD('".$password."')";
	$account = mysqli_query($conn,$sqlConta);
	$accData = mysqli_fetch_array($account, MYSQLI_ASSOC);

	return $accData;
}
//mostrar doações pendentes de confirmação
function showPendingDonates($conn){
	$donates = mysqli_query($conn, "SELECT * FROM DonateKey WHERE STATUS = 'PENDING' ");
	echo '
			<table style="width:100%;">
			  <tr>
			    <th>Name</th>
			    <th>Key</th>
			    <th>Status</th>
			  </tr>
		';
		
	while ($row = mysqli_fetch_array($donates)) {
		$player = $row['PlayerID'];
		$key = $row['TransactionID'];
		$status = $row['STATUS'];


		tablesDonateContent($player, $key, $status);				
	}
	echo '</table>';
}

?>
