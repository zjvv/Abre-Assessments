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
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
	require_once('../../core/abre_functions.php');
	require_once('functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		
		$category=htmlspecialchars($_GET["category"], ENT_QUOTES);
		$StaffId=GetStaffID($_SESSION['useremail']);
		$CurrentSememester=GetCurrentSemester();
		
		//If Course, Display all Courses
		if($category=="course")
		{
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
		
		//If Group, Display all Groups
		if($category=="group")
		{
			$query = "SELECT * FROM students_groups where StaffId='$StaffId'";
			$dbreturn = databasequery($query);
			foreach ($dbreturn as $value)
			{	
				$GroupName=$value['Name'];
				$GroupID=$value['ID'];
				
				echo "<option value='$GroupID'>$GroupName</option>";
			}
		}
		
	}

?>