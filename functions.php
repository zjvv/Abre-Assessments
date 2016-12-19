<?php
	
	/*
	* Copyright 2015 Hamilton City School District	
	* 		
	* This program is free software: you can redistribute it and/or modify
    * it under the terms of the GNU General Public License as published by
    * the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
	* 
    * This program is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    * GNU General Public License for more details.
	* 
    * You should have received a copy of the GNU General Public License
    * along with this program.  If not, see <http://www.gnu.org/licenses/>.
    */
	
	//Required configuration files
	require(dirname(__FILE__) . '/../../configuration.php');	
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php'); 
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
	
		function getCerticaToken()
		{
			$ch = curl_init();
			$sql = "SELECT *  FROM assessments_settings";
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
			$result = $db->query($sql);
			while($row = $result->fetch_assoc())
			{	
				$resturl=$row["Certica_URL"];
				$restkey=$row["Certica_AccessKey"];
			}
			curl_setopt($ch, CURLOPT_URL, "$resturl/tokens?unlimited=true");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: IC-TOKEN Credential=$restkey"));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			$json = json_decode($result,true);
			$token=$json['token'];
			curl_close($ch);		
			return $token;
		}
		
	}
	
?>