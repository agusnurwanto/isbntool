<?php

// http://mbahcoding.com/php/codeigniter/codeigniter-ajax-crud-using-bootstrap-modals-and-datatable.html

if(!empty($_GET['list'])){
    $allData = get_datatables();
    // echo "<pre>".print_r($data,1)."</pre>";die();
    $data = array();
    $orderColumn = $_POST["order"][0]["column"];
    $orderType = $_POST["order"][0]["dir"];
    switch ($orderColumn) {
    	case '1':
    		$k = "isbn_number";
    		break;
    	case '2':
    		$k = "custom_price";
    		break;
    	case '3':
    		$k = "real_price";
    		break;
    	case '4':
    		$k = "difference";
    		break;
    	default:
    		$k = "id";
    		break;
    }
    foreach ($allData["content"] as $dt) {
        $row = array();
        $row[] = $dt->id;
        $row[] = $dt->isbn_number;
        $row[] = $dt->custom_price;
        $row[] = $dt->real_price;
        $row[] = $dt->difference;

        //add html for action
        $row[] = '<a class="btn btn-sm btn-primary" onclick="edit_data('."'".$dt->id."'".'); return false;" href="javascript:void()" title="Edit"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
              <a class="btn btn-sm btn-danger" onclick="delete_data('."'".$dt->id."'".'); return false;" href="javascript:void()" title="Hapus"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
        $data[$dt->$k][] = $row;
    }
    // echo "<pre>".print_r($data, 1)."</pre>";
    if($orderType=="asc"){
    	ksort($data);
    }else{
    	krsort($data);
    }
    // echo "<pre>".print_r($data, 1)."</pre>";

    $newData = array();
    foreach ($data as $key => $value) {
    	foreach ($value as $k => $v) {
    		$newData[] = $v;
    	}
    }
    $data = $newData;

    $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $allData["countAll"],
            "recordsFiltered" => $allData["countFiltered"],
            "lastID" => $allData["lastID"],
            "data" => $data,
        );
    //output to json format
    die(json_encode($output));
}

function get_datatables($id=false){
	$content = file_get_contents('database/isbnNumbers.json');
	// $content = file_get_contents("https://fultonfile-agusnurwanto.rhcloud.com/tmp/json/isbnNumbers.json");
	$content = json_decode($content);
    if(!empty($_POST["search"]["value"])){
    	$search = $_POST["search"]["value"];
    	$newData = array();
    	foreach ($content as $k => $v) {
    		$cek = strchr($v->id, $search);
    		if($cek!=false){
    			$newData[] = $v; continue;
    		}
    		$cek = strchr($v->isbn_number, $search);
    		if($cek!=false){
				$newData[] = $v; continue;
    		}
    		$cek = strchr($v->custom_price, $search);
    		if($cek!=false){
				$newData[] = $v; continue;
    		}
    		$cek = strchr($v->real_price, $search);
    		if($cek!=false){
				$newData[] = $v; continue;
    		}
    		$cek = strchr($v->difference, $search);
    		if($cek!=false){
    			$newData[] = $v; continue;
    		}
    	}
    	$content = $newData;
    }
	if(!empty($id)){
		$data = array();
		foreach ($content as $key => $value) {
			if($value->id==$id){
				$data[] = $content[$key];
				break;
			}
		}
		return $data;
	}
	$data = array(
		"countAll" => count($content)
	);
	// echo "<pre>".print_r($content, 1)."</pre>";
	if(!empty($content)){
		$data["lastID"] = $content[count($content)-1]->id;
	}else{
		$data["lastID"] = 0;
	}
	if(!empty($_POST['length']) && $_POST['length'] != -1){
		$newContent = array();
		for( $i=$_POST["start"]; $i<$_POST["length"]; $i++) {
			if(!empty($content[$i])){
				$newContent[] = $content[$i];
			}
		}
		$content = $newContent;
	}
	$data["content"] = $content;
	$data["countFiltered"] = count($content);
	return $data;
}

if(!empty($_GET["add"])){
	$dt = get_datatables();
	$id = $_POST["id"];
	$isbn_number = $_POST["isbn_number"];
	$custom_price = $_POST["custom_price"];
	$real_price = $_POST["real_price"];
	$difference = $_POST["difference"];
	$data = array(
            "id" => $id,
            "isbn_number" => $isbn_number,
            "custom_price" => $custom_price,
            "real_price" => $real_price,
            "difference" => $difference,
        );
    $dt["content"][] = $data;
	putContent(array("folder"=>"json", "file"=>"isbnNumbers.json", "content"=>json_encode($dt["content"])));
    echo json_encode(array("status" => TRUE));
}

function getRealPrice($id){
	$option = array(
			"url" => "https://www.bookbyte.com/buyback2.aspx?isbns=".rawurlencode($id)
		);
	$data = array();
	$data["msg"] = request($option);
	$data["error"] = 0;
	die(json_encode($data));
}

if(!empty($_GET["getRealPrice"])){
	return getRealPrice($_GET["id"]);
}

if(!empty($_GET["edit"])){
	$data = get_datatables($_GET["id"]);
	die(json_encode($data));
}

if(!empty($_GET["update"])){
	$id = $_POST["id"];
	$isbn_number = $_POST["isbn_number"];
	$custom_price = $_POST["custom_price"];
	$real_price = $_POST["real_price"];
	$difference = $_POST["difference"];
	$data = get_datatables();
	foreach ($data["content"] as $k => $v) {
		if($v->id==$id){
			$data["content"][$k]->isbn_number = $isbn_number;
			$data["content"][$k]->custom_price = $custom_price;
			$data["content"][$k]->real_price = $real_price;
			$data["content"][$k]->difference = $difference;
		}
	}
	putContent(array("folder"=>"json", "file"=>"isbnNumbers.json", "content"=>json_encode($data["content"])));
	die(json_encode(array("status" => TRUE)));
}

if(!empty($_GET["replace"])){
	$id = $_POST["id"];
	$isbn_number = $_POST["isbn_number"];
	$custom_price = $_POST["custom_price"];
	$real_price = $_POST["real_price"];
	$difference = $_POST["difference"];
	$data = get_datatables();
	$cek = false;
	foreach ($data["content"] as $k => $v) {
		if($data["content"][$k]->isbn_number==$isbn_number){
			$data["content"][$k]->isbn_number = $isbn_number;
			if($custom_price!=0){
				$data["content"][$k]->custom_price = $custom_price;
				$data["content"][$k]->difference = $difference;
			}else{
				$difference = $real_price - $data["content"][$k]->custom_price;
				$data["content"][$k]->difference = $difference;
			}
			$data["content"][$k]->real_price = $real_price;
			$cek = true;
			break;
		}
	}
	if(empty($cek)){
		$data["content"][] = $_POST;
	}
	// echo "<pre>".print_r($data["content"],1)."</pre>";
	putContent(array("folder"=>"json", "file"=>"isbnNumbers.json", "content"=>json_encode($data["content"])));
	die(json_encode(array("status" => TRUE)));
}

if(!empty($_GET["delete"])){
	$id = $_GET["id"];
	$newData = array();
	if($id!="all"){
		$data = get_datatables();
		foreach ($data["content"] as $k => $v) {
			if($v->id==$id){
				unset($data["content"][$k]);
			}else{
				$newData[] = $v;
			}
		}
	}
	putContent(array("folder"=>"json", "file"=>"isbnNumbers.json", "content"=>json_encode($newData)));
	die(json_encode(array("status" => TRUE)));
}

function putContent($options){
	return file_put_contents("database/".$options["file"], $options["content"]);

	$url_base = "https://fultonfile-agusnurwanto.rhcloud.com";
	request(array(
		"url"	=> $url_base."/createFolders.php", 
		"param"	=> array(
			"folder"	=> $options["folder"],
			"file"		=> $options["file"],
			"output"	=> $options["content"]
		)
	));
}

if(!empty($_GET["getKeys"])){
	$keys = array();
	$data = get_datatables();
	foreach ($data["content"] as $k => $v) {
		$keys[] = $v->isbn_number;
	}
	die(json_encode($keys));
}

function request($option){
	$url = $option['url'];
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
	if(!empty($option['param'])){
		$param = http_build_query($option['param']);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
	}
  	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36");
  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	$server_output = curl_exec ($ch);
	// echo $server_output."cek123";
	curl_close ($ch);
	return $server_output;
}