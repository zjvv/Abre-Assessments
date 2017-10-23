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
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
	require_once('functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
	
		//Retrieve settings and group as json
		$itemid=$_POST["itemid"];
		$rubricquestionvalue=$_POST["rubricquestionvalue"];
		$assessmentid=$_POST["assessmentid"];
		$user=$_POST["user"];
		
		mysqli_query($db, "UPDATE assessments_scores set Score='$rubricquestionvalue' where Assessment_ID='$assessmentid' and ItemID='$itemid' and User='$user'") or die (mysqli_error($db));
		
		//Find how many rubric points for this user has been awarded on this assessment
		$sql = "SELECT SUM(Score) FROM assessments_questions LEFT JOIN assessments_scores ON assessments_questions.Bank_ID = assessments_scores.ItemID where assessments_scores.User='$user' and assessments_questions.Assessment_ID='$assessmentid' and assessments_questions.Type='Open Response'";
		$result = $db->query($sql);
		while($row = $result->fetch_assoc())
		{
			$RubricPoints=htmlspecialchars($row["SUM(Score)"], ENT_QUOTES);
		}
		
		//Find how many possible points on assessment
		$TotalPossiblePoints=0;
		$sql = "SELECT Points FROM assessments_questions where Assessment_ID='$assessmentid'";
		$result = $db->query($sql);
		while($row = $result->fetch_assoc())
		{
			$Points=htmlspecialchars($row["Points"], ENT_QUOTES);
			if($Points==""){ $Points=1; }
			$TotalPossiblePoints=$TotalPossiblePoints+$Points;
		}		
		
		//Find out how many points have been awarded to user for this assessment
		$sql = "SELECT assessments_questions.Points, assessments_scores.Score, assessments_questions.Type FROM assessments_questions LEFT JOIN assessments_scores ON assessments_questions.Bank_ID = assessments_scores.ItemID where assessments_scores.User='$user' and assessments_questions.Assessment_ID='$assessmentid'";
		$result = $db->query($sql);
		$TotalPointsEarned=0;
		while($row = $result->fetch_assoc())
		{
			$ScoreEarned=htmlspecialchars($row["Score"], ENT_QUOTES);
			$PointsEarned=htmlspecialchars($row["Points"], ENT_QUOTES);
			if($PointsEarned==0){ $PointsEarned=1; }
			$Type=htmlspecialchars($row["Type"], ENT_QUOTES);
			
			if($Type!="Open Response")
			{
				$TotalPointsEarned=$TotalPointsEarned+($PointsEarned*$ScoreEarned);
			}
			else
			{
				$TotalPointsEarned=$TotalPointsEarned+$ScoreEarned;
			}
			
		}
		
		//Find Percentage
		$studentfinalpercentage=round(($TotalPointsEarned/$TotalPossiblePoints)*100);
		
		//Save final assessment result
		AssessmentResultsScore($assessmentid, $user);
		
		header('Content-Type: application/json');
		echo json_encode(array('RubricPoints' => $RubricPoints, 'Score' => "$TotalPointsEarned/$TotalPossiblePoints", 'Percentage' => "$studentfinalpercentage%"));
		
		
	}
	
?>