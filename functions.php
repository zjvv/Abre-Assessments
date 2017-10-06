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
			
			if($firstname && $lastname){ return "$firstname $lastname"; }else{ return "$emaillookup"; }
			
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
			if(isset($FirstName) && isset($LastName)){ return "$FirstName $LastName"; }else{ return $StudentID; }
			
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
		
		//Show Results of Assessment
		function ShowAssessmentResults($Assessment_ID,$User,$ResultName,$questioncount,$owner,$totalstudents,$studentcounter,$correctarray)
		{
			
			require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
			
			//See if student has completed
			$sqlcomplete = "SELECT * FROM assessments_status where Assessment_ID='$Assessment_ID' and User='$User'";
			$resultcomplete = $db->query($sqlcomplete);
			$completestatus=0;
			while($rowcomplete = $resultcomplete->fetch_assoc())
			{
				$completestatus=1;
				$Start_Time=htmlspecialchars($rowcomplete["Start_Time"], ENT_QUOTES);
				$End_Time=htmlspecialchars($rowcomplete["End_Time"], ENT_QUOTES);	
				$Start_Time=date("F j, Y, g:i A", strtotime($Start_Time));
				if($End_Time=="0000-00-00 00:00:00"){ $End_Time="In Progress"; }else{ $End_Time=date("F j, Y, g:i A", strtotime($End_Time)); }
			}
			
			//Check what questions the student got correct
			$sqlquestionsanswer = "SELECT * FROM assessments_scores where Assessment_ID='$Assessment_ID' and User='$User'";
			$resultquestionsanswer = $db->query($sqlquestionsanswer);
			$StudentScoresArray = array();
			while($rowquestionsanswer = $resultquestionsanswer->fetch_assoc())
			{
				$StudentItemID = htmlspecialchars($rowquestionsanswer["ItemID"], ENT_QUOTES);
				$StudentScore = htmlspecialchars($rowquestionsanswer["Score"], ENT_QUOTES);
				$StudentScoresArray[$StudentItemID] = $StudentScore;
			}
								
			echo "<tr class='assessmentrow'>";
				echo "<td>";
					echo "<b>$ResultName</b>";
				echo "</td>";
								
				if($completestatus==1)
				{
					$TimeDifference = (strtotime($End_Time) - strtotime($Start_Time))/60;
					$TimeDifference = sprintf("%02d", $TimeDifference);
					if($TimeDifference==1){ $TimeDifferenceText="$TimeDifference Minute"; }else{ $TimeDifferenceText="$TimeDifference Minutes"; }
					echo "<td>";
						if($End_Time=="In Progress"){ echo "<div id='status_$User' class='pointer'>In Progress</div>"; }else{ echo "<div id='status_$User' class='pointer'>Complete<br> <span style='font-size:11px;'>$TimeDifferenceText</span></div>"; }
						echo "<div class='mdl-tooltip mdl-tooltip--large' for='status_$User'><b>Start:</b> $Start_Time<br><b>End:</b> $End_Time</div>";
					echo "</td>";
				}
				else
				{
					echo "<td>Not Completed</td>";	
				}
								
				//Loop through each question on assessment
				$sqlquestions = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID' order by Question_Order";
				$resultquestions = $db->query($sqlquestions);
				$allquestionitemsArray = array();
				$totalquestions=mysqli_num_rows($resultquestions);
				$totalcorrect=0;
				$totalcorrectrubric=0;
				$questioncounter=1;
				while($rowquestions = $resultquestions->fetch_assoc())
				{
					$Bank_ID=htmlspecialchars($rowquestions["Bank_ID"], ENT_QUOTES);
					$PointsPossible=htmlspecialchars($rowquestions["Points"], ENT_QUOTES);
					$QuestionType=htmlspecialchars($rowquestions["Type"], ENT_QUOTES);
					if($PointsPossible==""){ $PointsPossible=1; }	
					
					$totalpossibleassessmentpoints=$totalpossibleassessmentpoints+$PointsPossible;
					
					$allquestionitemsArray[$questioncounter] = $Bank_ID;	
					
					if (isset($StudentScoresArray[$Bank_ID]))
					{
						$Score = $StudentScoresArray[$Bank_ID];
						
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
							$icon="<i class='material-icons' style='color:#0D47A1'>grade</i>";
							echo "<td class='center-align pointer questionviewerreponse' data-question='$Bank_ID' data-questiontitle='$ResultName - Question $questioncounter' data-questionscore='t' data-assessmentid='$Assessment_ID' data-user='$User' style='background-color:#2196F3'>$icon</td>";
						}
						if($Score!="" && $QuestionType=="Open Response")
						{
							
							//Find how many points student got
							$sqlrubricpoints = "SELECT * FROM assessments_scores where Assessment_ID='$Assessment_ID' and User='$User' and ItemID='$Bank_ID'";
							$resultquestionsrubric = $db->query($sqlrubricpoints);
							while($rowquestions2 = $resultquestionsrubric->fetch_assoc())
							{
								$RubricScore=htmlspecialchars($rowquestions2["Score"], ENT_QUOTES);
							}
							
							$icon="<i class='material-icons' style='color:#0D47A1'>grade</i>";
							$totalcorrectrubric=$totalcorrectrubric+$RubricScore;
							echo "<td class='center-align pointer questionviewerreponse' data-question='$Bank_ID' data-questiontitle='$ResultName - Question $questioncounter' data-questionscore='t' data-assessmentid='$Assessment_ID' data-user='$User' style='background-color:#2196F3'>$icon</td>";
						}		
					}
					else
					{
						echo "<td class='center-align' style='background-color:#FFC107'><i class='material-icons' style='color:#FF6F00;'>remove_circle</i></td>";
					}
					
					$questioncounter++;
					
				}
				
				//Auto Points
				$totalcorrectdouble=sprintf("%02d", $totalcorrect);
				if($totalcorrectdouble!="00"){ $totalcorrectdouble = ltrim($totalcorrectdouble, '0'); }
				if($totalcorrectdouble=="00"){ $totalcorrectdouble="0"; }
				echo "<td class='center-align'>$totalcorrectdouble</td>";
				
				//Rubric Points
				$Username=str_replace("@","",$User);
				$Username=str_replace(".","",$Username);
				echo "<td class='center-align' id='rubric-total-$Username'>$totalcorrectrubric</td>";
							
				//Score
				$rubricandtotalscored=$totalcorrectdouble+$totalcorrectrubric;
				echo "<td class='center-align' id='score-total-$Username'>$rubricandtotalscored/$totalpossibleassessmentpoints</td>";
				
				//Percentage
				$studentfinalpercentage=round((($rubricandtotalscored)/$totalpossibleassessmentpoints)*100);
				echo "<td class='center-align' id='percentage-total-$Username'>$studentfinalpercentage%</td>";
				
				if($owner==1 or superadmin() or AdminCheck($_SESSION['useremail']))
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
					echo "<tr style='background-color:".sitesettings("sitecolor").";'>";
					echo "<td colspan='2' style='color:#fff;' class='center-align'><b>Class Mastery</b></td>";
					

					foreach ($allquestionitemsArray as $value)
					{
						$counts = array_count_values($correctarray);
						if (isset($counts[$value])){ $correctcount=$counts[$value]; }else{ $correctcount=0; }
						$correctpercent=round(($correctcount/$totalstudents)*100);
						echo "<td class='center-align' style='color:#fff;'><b>$correctpercent%</b></td>";
					}
					
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "</tr>";
				
					//District Mastery
					echo "<tr style='background-color:".sitesettings("sitecolor").";'>";
					echo "<td colspan='2' style='color:#fff;' class='center-align'><b>District Mastery</b></td>";
					
					foreach ($allquestionitemsArray as $value)
					{
						if (isset($questionscoreArray[$value]))
						{
							$correctcount=$questionscoreArray[$value];
							$correctpercent=round(($correctcount/$totalassessedstudents)*100);
							echo "<td class='center-align' style='color:#fff;'><b>$correctpercent%</b></td>";
						}
						else
						{
							echo "<td class='center-align' style='color:#fff;'><b>0%</b></td>";
						}
					}
					
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