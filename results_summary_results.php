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
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		
		$token=getCerticaToken();
		
		?>
		<script src='https://cdn.certicasolutions.com/sdk/js/sdk.itemconnect.min.js?x-ic-credential=<?php echo $token; ?>'></script>
		<script src='https://cdn.certicasolutions.com/player/js/player.itemconnect.min.js'></script>
		<link rel="stylesheet" href='https://cdn.certicasolutions.com/player/css/player.itemconnect.min.css'>	
		<?php
		
		//Include Fixed Table JS
		?><script src='modules/<?php echo basename(__DIR__); ?>/js/tableHeadFixer.js'></script><?php
		
		//Get Passed Variables
		$Assessment_ID=htmlspecialchars($_GET["assessmentid"], ENT_QUOTES);
		
		//Look up StaffID and Semester
		$StaffId=GetStaffID($_SESSION['useremail']);
		$CurrentSememester=GetCurrentSemester();
		
		//Check if verified assessment
		$sql = "SELECT * FROM assessments where ID='$Assessment_ID' and Owner='".$_SESSION['useremail']."'";
		$result = $db->query($sql);
		$owner = $result->num_rows;
		
		if(isset($_GET["course"]))
		{ 
			$course=$_GET["course"]; 
			list($CourseCode, $SectionCode) = explode(",",$course); 
			$sql = "SELECT * FROM Abre_StudentSchedules where CourseCode='$CourseCode' and SectionCode='$SectionCode' and StaffId='$StaffId' and (TermCode='$CurrentSememester' or TermCode='Year') group by StudentID order by LastName";
		}
		if(isset($_GET["groupid"]))
		{
			$groupid=$_GET["groupid"];
			$sql = "SELECT * FROM students_groups where ID='$groupid'";
		}
		if(isset($_GET["staffid"]))
		{
			$staffcode=$_GET["staffid"];
			$sql = "SELECT * FROM Abre_StudentSchedules where StaffId='$staffcode' and (TermCode='$CurrentSememester' or TermCode='Year') group by StudentID order by LastName";
		}
		if(!isset($course) && !isset($groupid) && !isset($staffcode))
		{
			$sql = "SELECT * FROM assessments_status where Assessment_ID='$Assessment_ID'";
		}
		
		$result = $db->query($sql);
		$rowcount=mysqli_num_rows($result);
		
		if($rowcount!=0)
		{
			?>
			<div class='mdl-shadow--4dp'>
			<div class='page' style='padding:30px;'>
			<div id='searchresults'>	
			<div class='row'><div class='tableholder'>
			<table id='myTable' class='tablesorter bordered thintable'>
			<thead>
			<tr class='pointer'>
				<th><div style='width:180px;'>Student</div></th>
				<th><div style='width:140px;'>Status</div></th>
										
				<?php
					
					$sqlheader = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID'";
					$resultheader = $db->query($sqlheader);
					$questioncount=0;
					while($row = $resultheader->fetch_assoc())
					{
						$questioncount++;
						$Standard=htmlspecialchars($row["Standard"], ENT_QUOTES);
						$Standard_Text = str_replace("CCSS.Math.Content.","",$Standard);
						$Standard_Text = str_replace("CCSS.ELA-Literacy.","",$Standard_Text);
						echo "<th style='min-width:60px;'><div class='center-align' id='standard_$questioncount'>$questioncount</div><div class='mdl-tooltip mdl-tooltip--large' for='standard_$questioncount'>$Standard_Text</div></th>";
					}
				?>
										
				<th style='min-width:100px;'><div class='center-align'>Score</div></th>	
				<th style='min-width:100px;'><div class='center-align'>Percentage</div></th>
				<th style='max-width:30px;'></th>					
			</tr>
			</thead>
			<tbody>
								
				<?php
					
					//View All Groups
					if(isset($groupid))
					{
						$sql = "SELECT * FROM students_groups_students LEFT JOIN Abre_AD ON students_groups_students.Student_ID=Abre_AD.StudentID where students_groups_students.Group_ID='$groupid'";
						$result = $db->query($sql);
						$totalstudents=mysqli_num_rows($result);
						$studentcounter=0;
						$totalresultsbystudentarray = array();
						while($row = $result->fetch_assoc())
						{
							$studentcounter++;
							$User=htmlspecialchars($row["Email"], ENT_QUOTES);
							if($User!=NULL)
							{
								$ResultName=getNameGivenEmail($User);
							}
							else
							{
								$StudentID=htmlspecialchars($row["Student_ID"], ENT_QUOTES);
								$ResultName=getStudentNameGivenStudentID($StudentID);
							}
							
							//Loop through each kid and create an array with user match with item they got correct
							$sql = "SELECT * FROM assessments_scores where Assessment_ID='$Assessment_ID' and User='$User' and Score='1'";
							$result2 = $db->query($sql);
							while($row2 = $result2->fetch_assoc())
							{
								$ItemID=htmlspecialchars($row2["ItemID"], ENT_QUOTES);
								array_push($totalresultsbystudentarray, $ItemID);
							}
							
							ShowAssessmentResults($Assessment_ID,$User,$ResultName,$questioncount,$owner,$totalstudents,$studentcounter,$totalresultsbystudentarray);
						}
					}
					
					//View All Courses
					if(isset($course))
					{
						$sql = "SELECT * FROM Abre_StudentSchedules where CourseCode='$CourseCode' and SectionCode='$SectionCode' and StaffId='$StaffId' and (TermCode='$CurrentSememester' or TermCode='Year') group by StudentID order by LastName";
						$result = $db->query($sql);
						$totalstudents=mysqli_num_rows($result);
						$studentcounter=0;
						$totalresultsbystudentarray = array();
						while($row = $result->fetch_assoc())
						{
							$studentcounter++;
							$StudentID=htmlspecialchars($row["StudentID"], ENT_QUOTES);
							$ResultName=getStudentNameGivenStudentID($StudentID);
							$User=getEmailGivenStudentID($StudentID);
							
							//Loop through each kid and create an array with user match with item they got correct
							$sql = "SELECT * FROM assessments_scores where Assessment_ID='$Assessment_ID' and User='$User' and Score='1'";
							$result2 = $db->query($sql);
							while($row2 = $result2->fetch_assoc())
							{
								$ItemID=htmlspecialchars($row2["ItemID"], ENT_QUOTES);
								array_push($totalresultsbystudentarray, $ItemID);
							}
							
							ShowAssessmentResults($Assessment_ID,$User,$ResultName,$questioncount,$owner,$totalstudents,$studentcounter,$totalresultsbystudentarray);
						}
					}
					
					//View By Teacher
					if(isset($staffcode))
					{
						$sql = "SELECT * FROM Abre_StudentSchedules where StaffId='$staffcode' and (TermCode='$CurrentSememester' or TermCode='Year') group by StudentID order by LastName";
						$result = $db->query($sql);
						$totalstudents=mysqli_num_rows($result);
						$studentcounter=0;
						$totalresultsbystudentarray = array();
						while($row = $result->fetch_assoc())
						{
							$studentcounter++;
							$StudentID=htmlspecialchars($row["StudentID"], ENT_QUOTES);
							$ResultName=getStudentNameGivenStudentID($StudentID);
							$User=getEmailGivenStudentID($StudentID);
							
							//Loop through each kid and create an array with user match with item they got correct
							$sql = "SELECT * FROM assessments_scores where Assessment_ID='$Assessment_ID' and User='$User' and Score='1'";
							$result2 = $db->query($sql);
							while($row2 = $result2->fetch_assoc())
							{
								$ItemID=htmlspecialchars($row2["ItemID"], ENT_QUOTES);
								array_push($totalresultsbystudentarray, $ItemID);
							}
							
							ShowAssessmentResults($Assessment_ID,$User,$ResultName,$questioncount,$owner,$totalstudents,$studentcounter,$totalresultsbystudentarray);
						}
					}
					
					//View All
					if(!isset($course) && !isset($groupid) && !isset($staffcode))
					{												
						$sql = "SELECT * FROM assessments_status where Assessment_ID='$Assessment_ID' order by User";
						$result = $db->query($sql);
						$totalstudents=mysqli_num_rows($result);
						$studentcounter=0;
						$totalresultsbystudentarray = array();
						while($row = $result->fetch_assoc())
						{
							$studentcounter++;
							$User=htmlspecialchars($row["User"], ENT_QUOTES);
							$ResultName=getNameGivenEmail($User);		
							
							//Loop through each kid and create an array with user match with item they got correct
							$sql = "SELECT * FROM assessments_scores where Assessment_ID='$Assessment_ID' and User='$User' and Score='1'";
							$result2 = $db->query($sql);
							while($row2 = $result2->fetch_assoc())
							{
								$ItemID=htmlspecialchars($row2["ItemID"], ENT_QUOTES);
								array_push($totalresultsbystudentarray, $ItemID);
							}
									
							ShowAssessmentResults($Assessment_ID,$User,$ResultName,$questioncount,$owner,$totalstudents,$studentcounter,$totalresultsbystudentarray);
						}
						
					}

			echo "</table>";
			echo "</div>";
			echo "</div>";
			echo "</div>";
			echo "</div>";
		}
		else
		{
			echo "<div class='row center-align'><div class='col s12'><h6>There are no results for this assessment</h6></div></div>";
		}
		
	}

?>
		
<script>
			
	//Responsive fixed table header
	$(function()
	{
		$("#myTable").tableHeadFixer({ 'head' : true, 'left' : 1, 'foot' : true });
		$("#myTable").tablesorter({ sortList: [[0,0]] });
		
		//Check Window Width
		tableContainer();
		$(window).resize(function(){ tableContainer(); });
		function tableContainer()
		{
			var height=$(".mdl-layout__content").height();
			height=height-210;
			height=height+'px';
			$(".tableholder").css("max-height", height);
		}
		
		//Remove Student Result
		$( ".removeresult" ).unbind().click(function()
		{
			event.preventDefault();
			var result = confirm("Delete this student assessment?");
			if (result) {
				$(this).closest("tr").hide();
				var address = $(this).attr("href");
				$.ajax({
					type: 'POST',
					url: address,
					data: '',
				})	
			}
		});
		
		//Question Viewer
		$(".questionviewerreponse").unbind().click(function()
		{
			event.preventDefault();
			var Question = $(this).data('question');
			var QuestionTitle = $(this).data('questiontitle');
			$("#questionresponse_title").html(QuestionTitle);
			var QuestionScore = $(this).data('questionscore');
			if(QuestionScore=="1")
			{
				questionverbage="<div class='card white-text' style='background-color:#4CAF50; padding:20px;'>The response was correct</div>";
			}
			if(QuestionScore=="0")
			{
				questionverbage="<div class='card white-text' style='background-color:#F44336; padding:20px;'>The response was incorrect</div>";
			}
			if(QuestionScore=="t")
			{
				questionverbage="<div class='card white-text' style='background-color:#2196F3; padding:20px;'>This question is teacher graded</div>";
			}
			$("#questionresponse_score").html(questionverbage);
			var AssessmentID = $(this).data('assessmentid');
			var User = $(this).data('user');
			$("#questionholderresponse").hide();
			
			$(".modal-content #questionholderresponse").load( "modules/<?php echo basename(__DIR__); ?>/response_viewer.php?id="+Question+"&assessmentid="+AssessmentID+"&user="+User, function(){
				$("#questionholderresponse").show();
			});
						
			$('#questionresponse').openModal({
				in_duration: 0,
				out_duration: 0,
			});
		});
		
					
	});
	
				
</script>