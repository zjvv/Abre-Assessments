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
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
	
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
		
		//Show Results of Assessment
		function ShowAssessmentResults($Assessment_ID,$User,$ResultName,$questioncount,$owner)
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
								
			echo "<tr class='assessmentrow'>";
				echo "<td><b>$ResultName</b></td>";
								
				if($completestatus==1)
				{
					echo "<td><b>Start:</b> $Start_Time<br><b>End:</b> $End_Time</td>";
				}
				else
				{
					echo "<td>Not Completed</td>";	
				}
										
				//Loop through each question on assessment
				$sqlquestions = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID' order by Question_Order";
				$resultquestions = $db->query($sqlquestions);
				$totalcorrect=0;
				while($rowquestions = $resultquestions->fetch_assoc())
				{
											
					$Bank_ID=htmlspecialchars($rowquestions["Bank_ID"], ENT_QUOTES);
											
					//Find if student was right or wrong
					$sqlquestionsanswer = "SELECT * FROM assessments_scores where Assessment_ID='$Assessment_ID' and User='$User' and ItemID='$Bank_ID'";
					$resultquestionsanswer = $db->query($sqlquestionsanswer);
					$answerfound=0;
					while($rowquestionsanswer = $resultquestionsanswer->fetch_assoc())
					{
						$answerfound=1;
						$Score=htmlspecialchars($rowquestionsanswer["Score"], ENT_QUOTES);
						if($Score==0)
						{
							$icon="<i class='material-icons' style='color:#F44336'>cancel</i>";
						}
						else
						{
							$icon="<i class='material-icons' style='color:#4CAF50'>check_circle</i>"; $totalcorrect++;
						}
						echo "<td class='center-align'>$icon</td>"; 
					}
						
					if($answerfound==0){ echo "<td class='center-align'><i class='material-icons' style='color:#FFC107'>remove_circle</i></td>"; }

				}
										
				//Find the Total correct for student
				echo "<td class='center-align'>$totalcorrect/$questioncount</td>";
				
				if($owner==1 or superadmin())
				{
					echo "<td class='center-align'><a href='modules/".basename(__DIR__)."/removestudentresult.php?assessmentid=".$Assessment_ID."&student=".$User."' class='mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect mdl-color-text--grey-600 removeresult'><i class='material-icons'>delete</i></a></td>";
				}
				else
				{
					echo "<td class='center-align'></td>";
				}
				
			echo "</tr>";
		}		
		
		
	}
	
?>