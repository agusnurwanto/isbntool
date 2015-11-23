<?php

if(!empty($_GET['list'])){
    $allData = get_datatables();
    // echo "<pre>".print_r($data,1)."</pre>";die();
    $data = array();
    $no = $_POST['start'];
    foreach ($allData["content"] as $dt) {
        $no++;
        $row = array();
        $row[] = $dt->id;
        $row[] = $dt->isbn_number;
        $row[] = $dt->custom_price;
        $row[] = $dt->real_price;
        $row[] = $dt->difference;

        //add html for action
        $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void()" title="Edit" onclick="edit_person('."'".$dt->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
              <a class="btn btn-sm btn-danger" href="javascript:void()" title="Hapus" onclick="delete_person('."'".$dt->id."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

        $data[] = $row;
    }

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

function get_datatables(){
	$content = file_get_contents('database/isbnNumbers.json');
	$content = json_decode($content);
	$data = array(
		"countAll" => count($content)
	);
	$data["lastID"] = $content[count($content)-1]->id;
	if($_POST['length'] != -1){
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
	$id = $dt["lastID"];
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
    $dt[] = $data;
    file_put_contents("database/isbnNumbers.json", json_encode($dt));
    echo json_encode(array("status" => TRUE));
}

function getRealPrice($id){
	$option = array(
			"url" => "https://www.bookbyte.com/buyback2.aspx?isbns=".rawurlencode($id)
		);
	$data = array();
	$data["msg"] = request($option);
	$data["error"] = 0;
	return $data;
}

if(!empty($_GET["getRealPrice"])){
	return getRealPrice($_GET["id"]);
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
	curl_close ($ch);
	return $server_output;
}