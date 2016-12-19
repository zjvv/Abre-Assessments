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
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
	
	//Update system settings
	if(superadmin())
	{
		//Retrieve settings and group as json
		$certicabaseurl=$_POST["certicabaseurl"];
		$certicaaccesskey=$_POST["certicaaccesskey"];
		
		//Check to see if there are saved settings
		$sql = "SELECT * FROM assessments_settings";
		$result = $db->query($sql);
		$rowcount=mysqli_num_rows($result);
		
		if($rowcount!=0)
		{
			//Update the database
			mysqli_query($db, "UPDATE assessments_settings set Certica_URL='$certicabaseurl', Certica_AccessKey='$certicaaccesskey'") or die (mysqli_error($db));
		}
		else
		{
			//Add to the database
			mysqli_query($db, "INSERT INTO assessments_settings (ID, Certica_URL, Certica_AccessKey) VALUES (NULL, '$certicabaseurl', '$certicaaccesskey')") or die (mysqli_error($db));
		}

	}
	
?>