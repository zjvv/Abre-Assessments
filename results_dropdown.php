<?php
	
	/*
	* Copyright (C) 2016-2017 Abre.io LLC
	*
	* This program is free software: you can redistribute it and/or modify
    * it under the terms of the Affero General Public License version 3
    * as published by the Free Software Foundation.
	*
    * This program is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    * GNU Affero General Public License for more details.
	*
    * You should have received a copy of the Affero General Public License
    * version 3 along with this program.  If not, see https://www.gnu.org/licenses/agpl-3.0.en.html.
    */
	
	//Required configuration files
	require(dirname(__FILE__) . '/../../configuration.php'); 
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php'); 
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
	require_once('../../core/abre_functions.php');
	require_once('functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		
		$category=htmlspecialchars($_GET["category"], ENT_QUOTES);
		$StaffIDDropdown=htmlspecialchars($_GET["staffid"], ENT_QUOTES);
		$StaffId=GetStaffID($_SESSION['useremail']);
		$CurrentSememester=GetCurrentSemester();
		$emailencrypted=encrypt($_SESSION['useremail'],"");
		$email=$_SESSION['useremail'];
		
		//If Course, Display all Courses
		if($category=="course")
		{
			echo "<option value='' disabled selected>Choose a Course</option>";
			$query = "SELECT * FROM Abre_StaffSchedules where StaffID='$StaffId' and (TermCode='$CurrentSememester' or TermCode='Year') order by Period";
			$dbreturn = databasequery($query);
			foreach ($dbreturn as $value)
			{	
				$CourseCode=$value['CourseCode'];
				$SchoolCode=$value['SchoolCode'];
				$SectionCode=$value['SectionCode'];
				$CourseName=$value['CourseName'];
				$Period=$value['Period'];
				
				echo "<option value='$CourseCode,$SectionCode'>$CourseName (Period: $Period)</option>";
			}
		}
		
		//If Course, Display all Courses
		if($category=="courseteacher")
		{
			echo "<option value='' disabled selected>Choose a Course</option>";
			$query = "SELECT * FROM Abre_StaffSchedules where StaffID='$StaffIDDropdown' and (TermCode='$CurrentSememester' or TermCode='Year') order by Period";
			$dbreturn = databasequery($query);
			foreach ($dbreturn as $value)
			{	
				$CourseCode=$value['CourseCode'];
				$SchoolCode=$value['SchoolCode'];
				$SectionCode=$value['SectionCode'];
				$CourseName=$value['CourseName'];
				$Period=$value['Period'];
				
				echo "<option value='$CourseCode,$SectionCode'>$CourseName (Period: $Period)</option>";
			}
		}
		
		//If Group, Display all Groups
		if($category=="group")
		{
			echo "<option value='' disabled selected>Choose a Group</option>";
			$query = "SELECT * FROM students_groups where StaffId='$StaffId'";
			$dbreturn = databasequery($query);
			foreach ($dbreturn as $value)
			{	
				$GroupName=$value['Name'];
				$GroupID=$value['ID'];
				
				echo "<option value='$GroupID'>$GroupName</option>";
			}
		}
		
		//If Teacher, Display all Students for Teacher
		if($category=="teacher")
		{
			//Find what building the admin has access to
			echo "<option value='' disabled selected>Choose a Teacher</option>";
				$query = "SELECT * FROM Abre_VendorLink_SIS_Staff where EmailList LIKE '%$email%' LIMIT 1";
				$dbreturn = databasequery($query);
				$usersfound=count($dbreturn);
				foreach ($dbreturn as $value)
				{	
					$SchoolName=$value['SchoolName'];
					
					//Find all students in the building
					$query2 = "SELECT * FROM Abre_VendorLink_SIS_Staff where SchoolName LIKE '%$SchoolName%' group by LocalId order by LastName";
					$dbreturn2 = databasequery($query2);
					foreach ($dbreturn2 as $value2)
					{
						$LocalId=$value2['LocalId'];
						$LastName=$value2['LastName'];
						$FirstName=$value2['FirstName'];
						
						echo "<option value='$LocalId'>$LastName, $FirstName</option>";
					}
						
				}	
				
				if($usersfound==0)
				{
					$query2 = "SELECT * FROM Abre_VendorLink_SIS_Staff group by LocalId order by LastName";
					$dbreturn2 = databasequery($query2);
					foreach ($dbreturn2 as $value2)
					{
						$LocalId=$value2['LocalId'];
						$LastName=$value2['LastName'];
						$FirstName=$value2['FirstName'];
							
						echo "<option value='$LocalId'>$LastName, $FirstName</option>";
					}	
				}
		}
		
		//If Building, Display all Buildings
		if($category=="building")
		{
			echo "<option value='' disabled selected>Choose a Building</option>";
			$query = "SELECT SchoolName, SchoolCode FROM Abre_Students group by SchoolName order by SchoolName";
			$dbreturn = databasequery($query);
			foreach ($dbreturn as $value)
			{	
				$SchoolName=$value['SchoolName'];
				$SchoolCode=$value['SchoolCode'];
				
				echo "<option value='$SchoolCode'>$SchoolName</option>";
			}
		}
		
	}

?>