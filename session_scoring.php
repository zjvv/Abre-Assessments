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
	
	//Variables
	$assessmentid=mysqli_real_escape_string($db, $_POST["assessmentid"]);
	$score=mysqli_real_escape_string($db, $_POST["score"]);
	$scoredOn=mysqli_real_escape_string($db, $_POST["scoredOn"]);
	$itemId=mysqli_real_escape_string($db, $_POST["itemId"]);
	$itemResponse=mysqli_real_escape_string($db, $_POST["itemResponse"]);
	$scoreGUID=mysqli_real_escape_string($db, $_POST["scoreGUID"]);
	
	//Check to see if item was already scored
	$sqllookup2 = "SELECT * FROM assessments_scores where Assessment_ID='$assessmentid' and User='".$_SESSION['useremail']."' and ItemID='$itemId'";
	$result3 = $db->query($sqllookup2);
	$scorecount=mysqli_num_rows($result3);
	
	if($scorecount==0)
	{
		//Add the score record
		$stmt = $db->stmt_init();
		$sql = "INSERT INTO assessments_scores (Assessment_ID, User, Score, ScoredOn, ItemID, Response, Score_GUID) VALUES ('$assessmentid', '".$_SESSION['useremail']."', '$score', '$scoredOn', '$itemId', '$itemResponse', '$scoreGUID');";
		$stmt->prepare($sql);
		$stmt->execute();
		$stmt->close();
		$db->close();
	}
	else
	{
		mysqli_query($db, "UPDATE assessments_scores set Score='$score', scoredOn='$scoredOn', ItemID='$itemId', Response='$itemResponse', Score_GUID='$scoreGUID' where Assessment_ID='$assessmentid' and ItemID='$itemId' and User='".$_SESSION['useremail']."'") or die (mysqli_error($db));
	}
	
?>