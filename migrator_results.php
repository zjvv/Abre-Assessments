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
	
	//Loop through each assessment
	$sql = "SELECT * FROM assessments where ID='172'";
	$result = $db->query($sql);
	while($row = $result->fetch_assoc())
	{
		
		$Assessment_ID=htmlspecialchars($row["ID"], ENT_QUOTES);
		
		//Look up possible points
		$sqlposs = "SELECT Points FROM `assessments_questions` where Assessment_ID='$Assessment_ID'";
		$resultposs = $db->query($sqlposs);
		$Possible_Points=0;
		while($rowposs = $resultposs->fetch_assoc())
		{
			$Points=htmlspecialchars($rowposs["Points"], ENT_QUOTES);
			if($Points==""){ $Points=1; }
			$Possible_Points=$Possible_Points+$Points;
		}
			
		//Loop through each student who took assessment
		$sql2 = "SELECT * from assessments_status where Assessment_ID='$Assessment_ID' group by User";	
		$result2 = $db->query($sql2);
		$PointsEarned=0;
		while($row2 = $result2->fetch_assoc())
		{
			
			$User=htmlspecialchars($row2["User"], ENT_QUOTES);
			
			//Find out how student did
			$sql2studentid = "SELECT assessments_scores.Score, assessments_questions.Points, assessments_questions.Type FROM `assessments_scores` LEFT JOIN assessments_questions on assessments_scores.ItemID=assessments_questions.Bank_ID where assessments_scores.Assessment_ID='$Assessment_ID' and assessments_questions.Assessment_ID='$Assessment_ID' and assessments_scores.User='$User'";		
			$result2studentid = $db->query($sql2studentid);
			$PointsEarned=0;
			while($rowstudentid = $result2studentid->fetch_assoc())
			{
				$PointsPossibleQuestion=htmlspecialchars($rowstudentid["Points"], ENT_QUOTES);
				$EarnedPointsQuestion=htmlspecialchars($rowstudentid["Score"], ENT_QUOTES);
				$QuestionType=htmlspecialchars($rowstudentid["Type"], ENT_QUOTES);
				if($PointsPossibleQuestion==""){ $PointsPossibleQuestion=1; }
				if($EarnedPointsQuestion==""){ $EarnedPointsQuestion=0; }	
				if($QuestionType=="Open Response")
				{
					$PointsEarned=$PointsEarned+$EarnedPointsQuestion;
				}
				else
				{
					$PointsEarned=$PointsEarned+($EarnedPointsQuestion*$PointsPossibleQuestion);
				}				
			}
				
				
			//Look up studentid given email
			$StudentID="";
			$IEP="";
			$ELL="";
			$Gifted="";
			$sql3 = "SELECT * FROM `Abre_AD` where Email='$User'";
			$result3 = $db->query($sql3);
			while($row3 = $result3->fetch_assoc())
			{
				$StudentID=htmlspecialchars($row3["StudentID"], ENT_QUOTES);
					
				//Look up student Information
				$sql4 = "SELECT * FROM `Abre_Students` where StudentId='$StudentID'";
				$result4 = $db->query($sql4);
				while($row4 = $result4->fetch_assoc())
				{
					$IEP=htmlspecialchars($row4["IEP"], ENT_QUOTES);
					$ELL=htmlspecialchars($row4["ELL"], ENT_QUOTES);
					$Gifted=htmlspecialchars($row4["Gifted"], ENT_QUOTES);
				}		
			}	
				
			//Add Record to Database if it doesn't already exist
			$querystring="SELECT * FROM assessments_results where User='$User' and Assessment_ID='$Assessment_ID'";
			$querystringresult = $db->query($querystring);
			$rowcount=mysqli_num_rows($querystringresult);
			if($rowcount!=1)
			{
				$stmt = $db->stmt_init();
				$sql = "INSERT INTO assessments_results (Assessment_ID, User, Student_ID, Score, Possible_Points, IEP, ELL, Gifted) VALUES ('$Assessment_ID', '$User', '$StudentID', '$PointsEarned', '$Possible_Points', '$IEP', '$ELL', '$Gifted');";
				$stmt->prepare($sql);
				$stmt->execute();
				$stmt->close();
			}
			else
			{
				mysqli_query($db, "UPDATE assessments_results set Student_ID='$StudentID', Score='$PointsEarned', Possible_Points='$Possible_Points', IEP='$IEP', ELL='$ELL', Gifted='$Gifted' where Assessment_ID='$Assessment_ID' and Student_ID='$StudentID'") or die (mysqli_error($db));
			}			
					
		}

	}
	
	$db->close();

?>