<html>
	<head>
	
		<?php
			require("routeros_api.class.php");					// mikrotik api php class
			$API 				= new routeros_api();
			$API->debug 		= false;
			$user_mikrotik  	= "api-test";					// mikrotik user
			$password_mikrotik  = "api-test";					// mikrotik password
			$ip_mikrotik    	= "192.168.10.1";				// mikrotik IP address
			
			// set variable data
			$username 		= $_POST['username'];				// get data from post form
			$kode_keamanan 	= $_POST['kode_keamanan'];			// get data from post form
			$key			= "$kode_keamanan@omdrakula.net";	// because key saved in email field, so we must add valid email format.
		?>
		
		<title>Lupa Password User: <?php echo $username ?></title>
		
		<style>
			table, th, td {
				border: 1px solid black;
				border-collapse: collapse;
			}
		</style>
		
	</head>
	<body>

		<?php
			// connecting to mikrotik using api
			if($API->connect($ip_mikrotik, $user_mikrotik, $password_mikrotik)){

				// check security code
				$API->write('/ip/hotspot/user/print',false);
				$API->write('?name='.$username,true);
				$details_user = $API->read();
				
				foreach($details_user as $data){
					// if key is true
					if ($data['email'] == $key){
						// show user details
						$password 			= $data['password'];
						$waktu_online		= $data['uptime'];
						$kuota_terpakai 	= round(($data['bytes-in']+$data['bytes-out'])/(1024*1024*1024),2); //convert Bytes to GB
						$komen				= $data['comment'];
						
						echo "
						<b>Screenshot dan simpan halaman ini!</b><br><br>
						<table>
							<tr>
								<td>Username</td>
								<td>$username</td>
							</tr>
							<tr>
								<td>Password</td>
								<td>$password</td>
							</tr>
							<tr>
								<td>Kode Keamanan</td>
								<td><b>$kode_keamanan</b></td>
							</tr>
							<tr>
								<td>Waktu Online</td>
								<td>$waktu_online</td>
							</tr>
							<tr>
								<td>Kuota Terpakai</td>
								<td>$kuota_terpakai GB</td>
							</tr>
						</table>
						<br>
						<a href='#'><< LOGIN</a>"; //link back to login page
						
					// if key is false
					} else {
						echo "Kode keamanan salah";
					}
				}
				
			$API->disconnect();
			
			// if mikrotik api not connected
			} else {
				echo "Mikrotik tidak konek<br>Periksa konfigurasi koneksi API";
			}
		?>
	</body>
</html>
