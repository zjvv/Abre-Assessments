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
	require_once('permissions.php');
	
		function getCerticaToken()
		{
			$ch = curl_init();
			$sql = "SELECT *  FROM assessments_settings";
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
			$result = $db->query($sql);
			while($row = $result->fetch_assoc())
			{	
				$resturl=$row["Certica_URL"];
				$restkey=$row["Certica_AccessKey"];
			}
			curl_setopt($ch, CURLOPT_URL, "$resturl/tokens?unlimited=true");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: IC-TOKEN Credential=$restkey"));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			$json = json_decode($result,true);
			$token=$json['token'];
			curl_close($ch);		
			return $token;
		}
		
		function getNameGivenEmail($emaillookup)
		{
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
			$firstname=""; $lastname=""; $found=0;
			
			//Look for Student Given Student Email
			$sql = "SELECT * FROM Abre_AD where Email='$emaillookup'";
			$result = $db->query($sql);
			while($row = $result->fetch_assoc())
			{
				$StudentID=$row["StudentID"];
				
				$sql2 = "SELECT * FROM Abre_Students where StudentId='$StudentID'";
				$result2 = $db->query($sql2);
				while($row2 = $result2->fetch_assoc())
				{
					$found==1;
					$firstname=$row2["FirstName"];
					$lastname=$row2["LastName"];
				}
			}
			
			//Look for Staff in Staff Directory Given Email
			if($found==0)
			{
				$emailencrypted=encrypt($emaillookup, ""); 
				$sql = "SELECT * FROM directory where email='$emailencrypted'";
				$result = $db->query($sql);
				while($row = $result->fetch_assoc())
				{
					$found==1;
					$firstname=$row["firstname"];
					$firstname=decrypt($firstname, ""); 
					$lastname=$row["lastname"];
					$lastname=decrypt($lastname, ""); 
				}
			}
			
			//Look for Staff in SIS Given Email
			if($found==0)
			{
				$sql = "SELECT * FROM Abre_Staff where EMail1='$emaillookup'";
				$result = $db->query($sql);
				while($row = $result->fetch_assoc())
				{
					$found==1;
					$firstname=$row["FirstName"];
					$lastname=$row["LastName"];
				}
			}
			
			if($firstname && $lastname){ return "$lastname, $firstname"; }else{ return "$emaillookup"; }
			
		}
		
		//Final Score the Assessment
		function AssessmentResultsScore($Assessment_ID,$User)
		{
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
			
			//Look up possible points on assessment
			$sqlposs = "SELECT Points FROM `assessments_questions` where Assessment_ID='$Assessment_ID'";
			$resultposs = $db->query($sqlposs);
			$Possible_Points=0;
			while($rowposs = $resultposs->fetch_assoc())
			{
				$Points=htmlspecialchars($rowposs["Points"], ENT_QUOTES);
				if($Points==""){ $Points=1; }
				$Possible_Points=$Possible_Points+$Points;
			}
			
			//Loop through student scores of assessment and calculate
			$sql2 = "SELECT assessments_scores.Score, assessments_questions.Points, assessments_questions.Type FROM `assessments_scores` LEFT JOIN assessments_questions on assessments_scores.ItemID=assessments_questions.Bank_ID where assessments_scores.Assessment_ID='$Assessment_ID' and assessments_questions.Assessment_ID='$Assessment_ID' and assessments_scores.User='$User'";		
			$result2 = $db->query($sql2);
			$PointsEarned=0;
			while($row2 = $result2->fetch_assoc())
			{
				$PointsPossibleQuestion=htmlspecialchars($row2["Points"], ENT_QUOTES);
				$EarnedPointsQuestion=htmlspecialchars($row2["Score"], ENT_QUOTES);
				$QuestionType=htmlspecialchars($row2["Type"], ENT_QUOTES);
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
		
		//Get Staff Name Given StaffID
		function getStaffNameGivenStaffID($StaffID)
		{
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
			$sql = "SELECT * FROM Abre_Staff where StaffID='$StaffID' LIMIT 1";
			$result = $db->query($sql);
			while($row = $result->fetch_assoc())
			{
				$FirstName=$row["FirstName"];
				$LastName=$row["LastName"];
			}
			if($FirstName && $LastName){ return "$FirstName $LastName"; }else{ return "$StaffID"; }
			
		}
		
		//Get Email Given StudentID
		function getEmailGivenStudentID($StudentID)
		{
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
			$sql = "SELECT * FROM Abre_AD where StudentID='$StudentID'";
			$result = $db->query($sql);
			while($row = $result->fetch_assoc())
			{
				$Email=$row["Email"];
			}
			if(isset($Email)){ return $Email; }else{ return $StudentID; }
			
		}
		
		//Get Student Name Given StudentID
		function getStudentNameGivenStudentID($StudentID)
		{
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
			$sql = "SELECT * FROM Abre_Students where StudentId='$StudentID'";
			$result = $db->query($sql);
			while($row = $result->fetch_assoc())
			{
				$FirstName=$row["FirstName"];
				$LastName=$row["LastName"];
			}
			if(isset($FirstName) && isset($LastName)){ return "$LastName, $FirstName"; }else{ return $StudentID; }
			
		}
		
		//Get StaffID Given Email
		function GetStaffID($email){
			$email = strtolower($email);
			$query = "SELECT StaffID FROM Abre_Staff where EMail1 LIKE '$email' LIMIT 1";
			$dbreturn = databasequery($query);
			foreach ($dbreturn as $value)
			{ 
				$StaffId=htmlspecialchars($value["StaffID"], ENT_QUOTES);
				return $StaffId;
			}
		}
		
		//Get Current Semester
		function GetCurrentSemester(){
			$currentMonth = date("F");
			if(	$currentMonth=="January" 	or 
				$currentMonth=="February" 	or 
				$currentMonth=="March" 		or 
				$currentMonth=="April" 		or 
				$currentMonth=="May" 		or 
				$currentMonth=="June" 		or 
				$currentMonth=="July" 		or 
				$currentMonth=="August"
			)
			{
				return "Sem2";
			}
			else
			{
				return "Sem1";
			}
		}
		
		//Admin Check
		function AdminCheck($email){
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
			$email=encrypt($email, "");
			$contract=encrypt('Administrator', "");
			$sql = "SELECT *  FROM directory where email='$email' and contract='$contract'";
			$result = $db->query($sql);
			$count = $result->num_rows;
			if($count>=1)
			{
				return true;
			}
			else
			{
				return false;
			}
			$db->close();
		}
		
		//Return all Results for Assessment
		function GetCorrectResponsesforAssessment($Assessment_ID)
		{
			
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
			
			//Check what questions the student got correct
			$sqlquestionsanswer = "SELECT * FROM assessments_scores where Assessment_ID='$Assessment_ID'";
			$resultquestionsanswer = $db->query($sqlquestionsanswer);
			$StudentScoresArray = array();
			while($rowquestionsanswer = $resultquestionsanswer->fetch_assoc())
			{
				$StudentItemID = htmlspecialchars($rowquestionsanswer["ItemID"], ENT_QUOTES);
				$StudentScore = htmlspecialchars($rowquestionsanswer["Score"], ENT_QUOTES);
				$StudentUser = htmlspecialchars($rowquestionsanswer["User"], ENT_QUOTES);
				$StudentScoresArray[$StudentItemID][$StudentUser] = $StudentScore;
			}
			
			return $StudentScoresArray;
			
		}
		
		//Return all Status for Assessment
		function GetAssessmentStatus($Assessment_ID)
		{
			
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
			
			$sqlcomplete = "SELECT * FROM assessments_status where Assessment_ID='$Assessment_ID'";
			$resultcomplete = $db->query($sqlcomplete);
			$completestatus=0;
			$StudentStatusArray = array();
			while($rowcomplete = $resultcomplete->fetch_assoc())
			{
				$completestatus=1;
				$User=htmlspecialchars($rowcomplete["User"], ENT_QUOTES);
				$Start_Time=htmlspecialchars($rowcomplete["Start_Time"], ENT_QUOTES);
				$End_Time=htmlspecialchars($rowcomplete["End_Time"], ENT_QUOTES);	
				$Start_Time=date("F j, Y, g:i A", strtotime($Start_Time));
				if($End_Time=="0000-00-00 00:00:00"){ $End_Time="In Progress"; }else{ $End_Time=date("F j, Y, g:i A", strtotime($End_Time)); }
				$StudentStatusArray[$User] = array('StartTime' => $Start_Time, 'EndTime' => $End_Time);
			}
			return $StudentStatusArray;
			
		}
		
		//Get Email Given StudentID
		function getTeacherRoster($StaffID)
		{
			
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
			$Students=array();
			$CurrentSemester=GetCurrentSemester();
			$sql = "SELECT StudentID, FirstName, LastName FROM Abre_StudentSchedules where StaffId='$StaffID' and (TermCode='$CurrentSemester' or TermCode='Year') group by StudentID order by LastName";
			$result = $db->query($sql);
			while($row = $result->fetch_assoc())
			{
				$StudentID=$row["StudentID"];
				$FirstName=$row["FirstName"];
				$LastName=$row["LastName"];
				$Students[] = array("StudentID" => $StudentID, "FirstName" => $FirstName, "LastName" => $LastName);
			}
			return $Students;

		}
		
		//Get all scores by teacher
		function getTeacherRosterScoreBreakdown($StaffID,$AssessmentID)
		{
			
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
			$Students=array();
			$CurrentSemester=GetCurrentSemester();
			$sql = "SELECT StudentID, FirstName, LastName FROM Abre_StudentSchedules where StaffId='$StaffID' and (TermCode='$CurrentSemester' or TermCode='Year') group by StudentID order by LastName";
			$result = $db->query($sql);
			while($row = $result->fetch_assoc())
			{
				$StudentID=$row["StudentID"];
				$FirstName=$row["FirstName"];
				$LastName=$row["LastName"];
				
				//Find how they did on assessment
				$sql2 = "SELECT * FROM assessments_results where Student_ID='$StudentID' and Assessment_ID='$AssessmentID'";
				$result2 = $db->query($sql2);
				while($row2 = $result2->fetch_assoc())
				{
					$IEP=$row2["IEP"];
					$ELL=$row2["ELL"];
					$Gifted=$row2["Gifted"];
					$Score=$row2["Score"];
					$Possible_Points=$row2["Possible_Points"];
					
					$Students[] = array("StudentID" => $StudentID, "FirstName" => $FirstName, "LastName" => $LastName, "IEP" => $IEP, "ELL" => $ELL, "Gifted" => $Gifted, "Score" => $Score, "PossiblePoints" => $Possible_Points);
				}
				
			}
			return $Students;

		}
		
		//Get all Scores by Assessment
		function getAllScoresByAssessment($AssessmentID)
		{
			
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
			$Students=array();
			$sql = "SELECT * FROM assessments_results where Assessment_ID='$AssessmentID'";
			$result = $db->query($sql);
			while($row = $result->fetch_assoc())
			{
				$StudentID=$row["Student_ID"];
				$IEP=$row["IEP"];
				$ELL=$row["ELL"];
				$Gifted=$row["Gifted"];
				$Score=$row["Score"];
				$Possible_Points=$row["Possible_Points"];
					
				$Students[] = array("StudentID" => $StudentID, "IEP" => $IEP, "ELL" => $ELL, "Gifted" => $Gifted, "Score" => $Score, "PossiblePoints" => $Possible_Points);				
			}
			return $Students;

		}
		
		//Show Results of Assessment
		function ShowAssessmentResults($Assessment_ID,$User,$ResultName,$IEP,$ELL,$Gifted,$questioncount,$owner,$totalstudents,$studentcounter,$correctarray,$StudentScoresArray,$StudentStatusArray,$StudentsInClass,$QuestionDetails)
		{
			
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
			
			$Username=str_replace("@","",$User);
			$Username=str_replace(".","",$Username);
								
			echo "<tr class='assessmentrow'>";
				echo "<td><b>$ResultName</b></td>";
				
				if (!empty($StudentStatusArray[$User]))
				{				
					$StudentStatusVerb=$StudentStatusArray[$User];
					if($StudentStatusVerb['EndTime']!="In Progress"){
						
						$TimeDifference = (strtotime($StudentStatusVerb['EndTime']) - strtotime($StudentStatusVerb['StartTime']))/60;
						$TimeDifference = sprintf("%02d", $TimeDifference);
						if($TimeDifference==1){ $TimeDifferenceText="$TimeDifference Minute"; }else{ $TimeDifferenceText="$TimeDifference Minutes"; }
						echo "<td>";
							echo "<div id='status_$User' class='pointer'>Completed</div>";
							echo "<div class='mdl-tooltip mdl-tooltip--large' for='status_$User'><b>Completed:<br>"; echo $StudentStatusVerb['EndTime']; echo "</b><br><br>Total Time:<br>$TimeDifferenceText</div>";
						echo "</td>";
					}else{ 
						echo "<td>";
							echo "<div id='status_$User' class='pointer'>In Progress</div>";
							echo "<div class='mdl-tooltip mdl-tooltip--large' for='status_$User'><b>Start Time:<br>"; echo $StudentStatusVerb['StartTime']; echo "</b></div>";
						echo "</td>";					
					}
				}
				else
				{
					echo "<td>Has Not Started</td>";
				}
								
				//Loop through each question on assessment
				$sqlquestions = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID' order by Question_Order";
				$resultquestions = $db->query($sqlquestions);
				$allquestionitemsArray = array();
				$totalquestions=mysqli_num_rows($resultquestions);
				$totalcorrect=0;
				$totalcorrectrubric=0;
				$questioncounter=1;
				$totalpossibleassessmentpoints=NULL;
				while($rowquestions = $resultquestions->fetch_assoc())
				{
					$Bank_ID=htmlspecialchars($rowquestions["Bank_ID"], ENT_QUOTES);
					$PointsPossible=htmlspecialchars($rowquestions["Points"], ENT_QUOTES);
					$QuestionType=htmlspecialchars($rowquestions["Type"], ENT_QUOTES);
					if($PointsPossible==""){ $PointsPossible=1; }	
					
					$totalpossibleassessmentpoints=$totalpossibleassessmentpoints+$PointsPossible;
					
					$allquestionitemsArray[$questioncounter] = $Bank_ID;	
					
					if (isset($StudentScoresArray[$Bank_ID][$User]))
					{
						$Score = $StudentScoresArray[$Bank_ID][$User];
						
						if($Score=="0" && $QuestionType!="Open Response")
						{
							$icon="<i class='material-icons' style='color:#B71C1C'>cancel</i>";
							echo "<td class='center-align pointer questionviewerreponse' data-question='$Bank_ID' data-questiontitle='$ResultName - Question $questioncounter' data-questionscore='0' data-assessmentid='$Assessment_ID' data-user='$User' style='background-color:#F44336'>$icon</td>"; 
						}
						if($Score=="1" && $QuestionType!="Open Response")
						{
							$icon="<i class='material-icons' style='color:#1B5E20'>check_circle</i>"; 
							$totalcorrect=$totalcorrect+$PointsPossible;
							echo "<td class='center-align pointer questionviewerreponse' data-question='$Bank_ID' data-questiontitle='$ResultName - Question $questioncounter' data-questionscore='1' data-assessmentid='$Assessment_ID' data-user='$User' style='background-color:#4CAF50'>$icon</td>";
						}	
						if($Score=="" && $QuestionType=="Open Response")
						{
							$icon="<i class='material-icons' style='color:#0D47A1'>star_border</i>";
							echo "<td class='center-align pointer questionviewerreponse' id='rubric-question-$Username-$Bank_ID' data-question='$Bank_ID' data-questiontitle='$ResultName - Question $questioncounter' data-questionscore='t' data-assessmentid='$Assessment_ID' data-user='$User' style='background-color:#2196F3'>$icon</td>";
						}
						if($Score!="" && $QuestionType=="Open Response")
						{						
							$icon="<i class='material-icons' style='color:#0D47A1'>star</i>";
							$totalcorrectrubric=$totalcorrectrubric+$Score;
							echo "<td class='center-align pointer questionviewerreponse' data-question='$Bank_ID' data-questiontitle='$ResultName - Question $questioncounter' data-questionscore='t' data-assessmentid='$Assessment_ID' data-user='$User' style='background-color:#1565C0'>$icon</td>";
						}	
					}
					else
					{
						echo "<td class='center-align' style='background-color:#FFC107'><i class='material-icons' style='color:#FF6F00;'>remove_circle</i></td>";
					}
					
					$questioncounter++;
					
				}
				
				//IEP,ELL,Gifted
				if($IEP==""){ $IEP="N"; }
				if($ELL==""){ $ELL="N"; }
				if($Gifted==""){ $Gifted="N"; }
				echo "<td class='center-align'><b>$IEP</b></td>";
				echo "<td class='center-align'><b>$ELL</b></td>";
				echo "<td class='center-align'><b>$Gifted</b></td>";
				
				//Auto Points
				$totalcorrectdouble=sprintf("%02d", $totalcorrect);
				if($totalcorrectdouble!="00"){ $totalcorrectdouble = ltrim($totalcorrectdouble, '0'); }
				if($totalcorrectdouble=="00"){ $totalcorrectdouble="0"; }
				echo "<td class='center-align'>$totalcorrectdouble</td>";				
				
				//Rubric Points
				echo "<td class='center-align' id='rubric-total-$Username'>$totalcorrectrubric</td>";
							
				//Score
				$rubricandtotalscored=$totalcorrectdouble+$totalcorrectrubric;
				echo "<td class='center-align' id='score-total-$Username'>$rubricandtotalscored/$totalpossibleassessmentpoints</td>";
				
				//Percentage
				$studentfinalpercentage=round((($rubricandtotalscored)/$totalpossibleassessmentpoints)*100);
				echo "<td class='center-align' id='percentage-total-$Username'>$studentfinalpercentage%</td>";	
				
				//Delete Assessment Button
				if($owner==1 or superadmin())
				{
					echo "<td class='center-align'><a href='modules/".basename(__DIR__)."/removestudentresult.php?assessmentid=".$Assessment_ID."&student=".$User."' class='mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect mdl-color-text--grey-600 removeresult'><i class='material-icons'>delete</i></a></td>";
				}
				else
				{
					echo "<td class='center-align'></td>";
				}

				
			echo "</tr>";
			
			if($totalstudents==$studentcounter)
			{
				
				//How many district students took assessment
				$sqlquestions = "SELECT * FROM assessments_scores where Assessment_ID='$Assessment_ID' group by User";
				$resultquestions = $db->query($sqlquestions);
				$totalassessedstudents=mysqli_num_rows($resultquestions);
				
				//Score Breakdown
				$sqlquestions = "SELECT ItemID, Count(*) FROM `assessments_scores` WHERE `Assessment_ID` LIKE '$Assessment_ID' and Score=1 group by ItemID";
				$resultquestions = $db->query($sqlquestions);
				$questionscoreArray = array();
				while($rowquestions = $resultquestions->fetch_assoc())
				{
					$ItemIDScore=htmlspecialchars($rowquestions["ItemID"], ENT_QUOTES);
					$ItemIDCount=htmlspecialchars($rowquestions["Count(*)"], ENT_QUOTES);
					$questionscoreArray[$ItemIDScore] = $ItemIDCount;
				}
				
				
				
				
				echo "</tbody>";
				
				echo "<tfoot>";
				
					//Class Mastery
					if(!empty($StudentsInClass)){
						
						echo "<tr style='background-color:".sitesettings("sitecolor").";'>";
						echo "<td colspan='2' style='color:#fff;' class='center-align'><b>Class Mastery</b></td>";	
	
						foreach ($allquestionitemsArray as $Bank_ID)
						{
							
							$QuestionType=$QuestionDetails[$Bank_ID];
							$AnswersCorrect=0;
							
							//Find out how many students got question correct
							if($QuestionType!="Open Response"){
								$StudentsWhoAnswered=0;
								foreach ($StudentsInClass as $Email)
								{
									if(isset($StudentScoresArray[$Bank_ID][$Email])){ $StudentsWhoAnswered++; }
									$AnswersCorrect=$AnswersCorrect+$StudentScoresArray[$Bank_ID][$Email];
								}							
								$correctpercent=round(($AnswersCorrect/$totalstudents)*100);
								echo "<td class='center-align' style='color:#fff;'><b>$correctpercent%</b></td>";
							}
							else
							{
								echo "<td class='center-align' style='color:#fff;'><b>NA</b></td>";
							}
						}
						
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "</tr>";
					}
				
					//District Mastery
					echo "<tr style='background-color:".sitesettings("sitecolor").";'>";
					echo "<td colspan='2' style='color:#fff;' class='center-align'><b>District Mastery</b></td>";
					
					foreach ($allquestionitemsArray as $Bank_ID)
					{
						$QuestionType=$QuestionDetails[$Bank_ID];
						
						if($QuestionType!="Open Response"){
							if (isset($questionscoreArray[$Bank_ID]))
							{
								$correctcount=$questionscoreArray[$Bank_ID];
								$correctpercent=round(($correctcount/$totalassessedstudents)*100);
								echo "<td class='center-align' style='color:#fff;'><b>$correctpercent%</b></td>";
							}
							else
							{
								echo "<td class='center-align' style='color:#fff;'><b>0%</b></td>";
							}
						}
						else
						{
							echo "<td class='center-align' style='color:#fff;'><b>NA</b></td>";
						}
						
					}
					
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "</tr>";
					
				echo "</tfoot>";
				
				
			}
			
			
		}
	
?>