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
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
	
		$assessmentid=mysqli_real_escape_string($db, $_GET["assessmentid"]);
		
		//Delete Assessment
		$stmt = $db->stmt_init();
		$sql = "Delete from assessments where ID='$assessmentid'";
		$stmt->prepare($sql);
		$stmt->execute();
		$stmt->store_result();
		$num_rows = $stmt->num_rows;
		$stmt->close();
		
		//Delete questions in assessment
		$stmt = $db->stmt_init();
		$sql = "Delete from assessments_questions where Assessment_ID='$assessmentid'";
		$stmt->prepare($sql);
		$stmt->execute();
		$stmt->store_result();
		$num_rows = $stmt->num_rows;
		$stmt->close();
		
		//Delete status of assessment
		$stmt = $db->stmt_init();
		$sql = "Delete from assessments_status where Assessment_ID='$assessmentid'";
		$stmt->prepare($sql);
		$stmt->execute();
		$stmt->store_result();
		$num_rows = $stmt->num_rows;
		$stmt->close();
		
		//Delete scores of assessment
		$stmt = $db->stmt_init();
		$sql = "Delete from assessments_scores where Assessment_ID='$assessmentid'";
		$stmt->prepare($sql);
		$stmt->execute();
		$stmt->store_result();
		$num_rows = $stmt->num_rows;
		$stmt->close();
		
		$db->close();
		echo "The assessment has been deleted.";
	}
	
?>