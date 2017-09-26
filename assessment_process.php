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
		if(isset($_POST["assessment_sessionid"])){ $assessment_sessionid=$_POST["assessment_sessionid"]; }else{ $assessment_sessionid=""; }
		if(isset($_POST["assessment_id"])){ $assessment_id=$_POST["assessment_id"]; }
		
		//Create session key
		if($assessment_sessionid=="")
		{
			$timedate=time();
			$string=$timedate.$_SESSION['useremail'];
			$sessionid=sha1($string);
		}
		else
		{
			$sessionid=$assessment_sessionid;
		}
		
		//Add or update the assessment
		if($assessment_id=="")
		{
			$stmt = $db->stmt_init();
			$sql = "INSERT INTO assessments (Owner, Title, Description, Subject, Level, Grade, Editors, Locked, Shared, Verified, Session_ID) VALUES ('".$_SESSION['useremail']."', '$assessment_title', '$assessment_description', '$assessment_subject', '$assessment_level', '$assessment_grade', '$assessment_editors', '$assessment_lock', '$assessment_share', '$assessment_verified', '$sessionid');";
			$stmt->prepare($sql);
			$stmt->execute();
			$stmt->close();
			$db->close();
		}
		else
		{
			mysqli_query($db, "UPDATE assessments set Title='$assessment_title', Description='$assessment_description', Subject='$assessment_subject', Level='$assessment_level', Grade='$assessment_grade', Editors='$assessment_editors', Locked='$assessment_lock', Shared='$assessment_share', Verified='$assessment_verified', Session_ID='$sessionid' where ID='$assessment_id'") or die (mysqli_error($db));
		}
	
		//Give message
		echo "The assessment has been saved.";
	
	}
	
?>