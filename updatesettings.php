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
		
		//Update the database
		mysqli_query($db, "UPDATE assessments_settings set Certica_URL='$certicabaseurl', Certica_AccessKey='$certicaaccesskey'") or die (mysqli_error($db));

	}
	
?>