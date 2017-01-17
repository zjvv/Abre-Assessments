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
	require_once(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
	
		//Add the course
		if(isset($_POST["assessment_title"])){ $assessment_title=mysqli_real_escape_string($db, $_POST["assessment_title"]); }
		if(isset($_POST["assessment_description"])){ $assessment_description=mysqli_real_escape_string($db, $_POST["assessment_description"]); }
		if(isset($_POST["assessment_title"])){ $assessment_grade=$_POST["assessment_grade"]; $assessment_grade = implode (", ", $assessment_grade); }
		if(isset($_POST["assessment_subject"])){ $assessment_subject=$_POST["assessment_subject"]; }
		if(isset($_POST["assessment_level"])){ $assessment_level=$_POST["assessment_level"]; }
		if(isset($_POST["assessment_lock"])){ $assessment_lock=$_POST["assessment_lock"]; }else{ $assessment_lock=0; }
		if(isset($_POST["assessment_share"])){ $assessment_share=$_POST["assessment_share"]; }else{ $assessment_share=0; }
		if(isset($_POST["assessment_verified"])){ $assessment_verified=$_POST["assessment_verified"]; }else{ $assessment_verified=0; }
		if(isset($_POST["assessment_editors"])){ $assessment_editors=$_POST["assessment_editors"]; }
		if(isset($_POST["assessment_id"])){ $assessment_id=$_POST["assessment_id"]; }
		
		//Generate a unique, random class code
		$digits = 5;
		$Code=rand(pow(10, $digits-1), pow(10, $digits)-1);
		
		//Add or update the assessment
		if($assessment_id=="")
		{
			$stmt = $db->stmt_init();
			$sql = "INSERT INTO assessments (Owner, Title, Description, Subject, Level, Grade, Code, Editors, Locked, Shared, Verified) VALUES ('".$_SESSION['useremail']."', '$assessment_title', '$assessment_description', '$assessment_subject', '$assessment_level', '$assessment_grade', '$Code', '$assessment_editors', '$assessment_lock', '$assessment_share', '$assessment_verified');";
			$stmt->prepare($sql);
			$stmt->execute();
			$stmt->close();
			$db->close();
		}
		else
		{
			mysqli_query($db, "UPDATE assessments set Title='$assessment_title', Description='$assessment_description', Subject='$assessment_subject', Level='$assessment_level', Grade='$assessment_grade', Editors='$assessment_editors', Locked='$assessment_lock', Shared='$assessment_share', Verified='$assessment_verified' where ID='$assessment_id'") or die (mysqli_error($db));
		}
	
		//Give message
		echo "The assessment has been saved.";
	
	}
	
?>