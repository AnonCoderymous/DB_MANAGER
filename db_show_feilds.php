<?php
	if($_SERVER['REQUEST_METHOD'] === 'POST') {
		require_once 'config.php';
		$feild_names=array();
		if(isset($_POST['db_exec_query']) && $_POST['db_query']) {
		extract($_POST);
		if(empty($db_query))
			echo 'Empty Query';

		}elseif(isset($_POST['table_name'])){
			$i=0;
			$fetchFieldNamesQuery='SELECT * FROM '.$_POST['table_name'];
			$fetchFieldNamesHandler=mysqli_query($conn, $fetchFieldNamesQuery);
			//echo 'Fetching Rows...';
			while ($property = mysqli_fetch_field($fetchFieldNamesHandler)) {
				// array_push($feild_names, $property->type);
				$feild_names[$i]=array('tbl_name' => $property->name, 'tbl_size' => $property->type);
				$i++;
			}

			// echo json_encode($feild_names=array('name'=>$feild_names)));
			echo json_encode($feild_names);
		}
	}

	exit;
?>