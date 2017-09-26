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
	require_once(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{

		//Add the topic
		$questionid=$_GET["questionid"];
		$assessmentid=$_GET["assessmentid"];
		$questionvendorid=$_GET["vendorid"];
		$questiontype=$_GET["type"];
		$questiondifficulty=$_GET["difficulty"];
		$questionstandard=$_GET["standard"];
		$stmt = $db->stmt_init();
		$sql = "INSERT INTO assessments_questions (Assessment_ID, Bank_ID, Vendor_ID, Type, Difficulty, Standard) VALUES ('$assessmentid', '$questionid', '$questionvendorid', '$questiontype', '$questiondifficulty', '$questionstandard');";
		$stmt->prepare($sql);
		$stmt->execute();
		$stmt->close();
		$db->close();
	
		$person = array("questionid"=>$questionid,"message"=>"The link has been added.");
		header("Content-Type: application/json");
		echo json_encode($person);
		
	}
	
?>