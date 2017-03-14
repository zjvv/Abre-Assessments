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
		
		//Check for Editors field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Editors FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Editors` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Locked field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Locked FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Locked` INT NOT NULL DEFAULT '0';";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Share field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Shared FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Shared` INT NOT NULL DEFAULT '0';";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Verified field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Verified FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Verified` INT NOT NULL DEFAULT '0';";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Level field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Level FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Level` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for SessionID
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Session_ID FROM assessments"))
		{
			$sql = "ALTER TABLE `assessments` ADD `Session_ID` text NOT NULL;";
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
		if(!$db->query("SELECT Assessment_ID FROM assessments_questions"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Assessment_ID` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Question_Order field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Question_Order FROM assessments_questions"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Question_Order` int(11) NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Bank_ID field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Bank_ID FROM assessments_questions"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Bank_ID` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Points field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Points FROM assessments_questions"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Points` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Vendor_ID field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Vendor_ID FROM assessments_questions"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Vendor_ID` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Type field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Type FROM assessments_questions"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Type` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Difficulty field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Difficulty FROM assessments_questions"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Difficulty` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Standard field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Standard FROM assessments_questions"))
		{
			$sql = "ALTER TABLE `assessments_questions` ADD `Standard` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for assessments_standards table
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT * FROM assessments_standards"))
		{
			$sql = "CREATE TABLE `assessments_standards` (`ID` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			$sql .= "ALTER TABLE `assessments_standards` ADD PRIMARY KEY (`ID`);";
			$sql .= "ALTER TABLE `assessments_standards` MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";	
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Subject field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Subject FROM assessments_standards"))
		{
			$sql = "ALTER TABLE `assessments_standards` ADD `Subject` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Standard field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Standard FROM assessments_standards"))
		{
			$sql = "ALTER TABLE `assessments_standards` ADD `Standard` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for assessments_scores table
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT * FROM assessments_scores"))
		{
			$sql = "CREATE TABLE `assessments_scores` (`ID` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			$sql .= "ALTER TABLE `assessments_scores` ADD PRIMARY KEY (`ID`);";
			$sql .= "ALTER TABLE `assessments_scores` MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";	
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Assessment_ID field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Assessment_ID FROM assessments_scores"))
		{
			$sql = "ALTER TABLE `assessments_scores` ADD `Assessment_ID` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for User field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT User FROM assessments_scores"))
		{
			$sql = "ALTER TABLE `assessments_scores` ADD `User` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Score field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Score FROM assessments_scores"))
		{
			$sql = "ALTER TABLE `assessments_scores` ADD `Score` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for scoredOn field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT scoredOn FROM assessments_scores"))
		{
			$sql = "ALTER TABLE `assessments_scores` ADD `scoredOn` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for ItemID field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT ItemID FROM assessments_scores"))
		{
			$sql = "ALTER TABLE `assessments_scores` ADD `ItemID` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Response field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Response FROM assessments_scores"))
		{
			$sql = "ALTER TABLE `assessments_scores` ADD `Response` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Check for Score_GUID field
		require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
		if(!$db->query("SELECT Score_GUID FROM assessments_scores"))
		{
			$sql = "ALTER TABLE `assessments_scores` ADD `Score_GUID` text NOT NULL;";
			$db->multi_query($sql);
		}
		$db->close();
		
		//Write the Setup File
		$myfile = fopen("$portal_path_root/modules/Abre-Assessments/setup.txt", "w");
		fwrite($myfile, '');
		fclose($myfile);

	}
	
?>