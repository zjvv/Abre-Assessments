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
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php');
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	
	if(superadmin() && !file_exists("$portal_path_root/modules/Abre-Assessments/setup.txt"))
	{
		
		//Check for assessments_settings table
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT * FROM assessments_settings"))
		{
			$sql = "CREATE TABLE `assessments_settings` (`ID` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			$sql .= "ALTER TABLE `assessments_settings` ADD PRIMARY KEY (`ID`);";
			$sql .= "ALTER TABLE `assessments_settings` MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";	
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Certica_URL field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Certica_URL FROM assessments_settings"))
		{
			$sql = "ALTER TABLE `assessments_settings` ADD `Certica_URL` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Certica_AccessKey field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Certica_AccessKey FROM assessments_settings"))
		{
			$sql = "ALTER TABLE `assessments_settings` ADD `Certica_AccessKey` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for assessments table
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT * FROM assessments"))
		{
			$sql = "CREATE TABLE `assessments` (`ID` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			$sql .= "ALTER TABLE `assessments` ADD PRIMARY KEY (`ID`);";
			$sql .= "ALTER TABLE `assessments` MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";	
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Owner field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Owner FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Owner` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Title field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Title FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Title` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Description field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Description FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Description` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Subject field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Subject FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Subject` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Grade field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Grade FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Grade` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Code field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Code FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Code` int(11) NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for assessments_questions table
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT * FROM assessments_questions"))
		{
			$sql = "CREATE TABLE `assessments_questions` (`ID` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			$sql .= "ALTER TABLE `assessments_questions` ADD PRIMARY KEY (`ID`);";
			$sql .= "ALTER TABLE `assessments_questions` MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";	
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Assessment_ID field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Assessment_ID FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Assessment_ID` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Question_Order field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Question_Order FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Question_Order` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Bank_ID field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Bank_ID FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Bank_ID` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Write the Setup File
		$myfile = fopen("$portal_path_root/modules/Abre-Assessments/setup.txt", "w");
		fwrite($myfile, '');
		fclose($myfile);

	}
	
?>