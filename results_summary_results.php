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
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php'); 
	require_once('../../core/abre_functions.php');
	require_once('functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
		
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
		if(!isset($course) && !isset($groupid))
		{
			$sql = "SELECT * FROM assessments_status where Assessment_ID='$Assessment_ID'";
		}
		
		$result = $db->query($sql);
		$rowcount=mysqli_num_rows($result);
		if($rowcount!=0)
		{
			?>
			<div class='mdl-shadow--4dp'>
			<div class='page'>
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
									
			<th style='min-width:120px;'><div class='center-align'>Score</div></th>		
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
						while($row = $result->fetch_assoc())
						{
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
							ShowAssessmentResults($Assessment_ID,$User,$ResultName,$questioncount,$owner);
						}
					}
					
					//View All Courses
					if(isset($course))
					{
						$sql = "SELECT * FROM Abre_StudentSchedules where CourseCode='$CourseCode' and SectionCode='$SectionCode' and StaffId='$StaffId' and (TermCode='$CurrentSememester' or TermCode='Year') group by StudentID order by LastName";
						$result = $db->query($sql);
						while($row = $result->fetch_assoc())
						{
							$StudentID=htmlspecialchars($row["StudentID"], ENT_QUOTES);
							$ResultName=getStudentNameGivenStudentID($StudentID);
							$User=getEmailGivenStudentID($StudentID);
							ShowAssessmentResults($Assessment_ID,$User,$ResultName,$questioncount,$owner);
						}
					}
					
					//View All
					if(!isset($course) && !isset($groupid))
					{						
						$sql = "SELECT * FROM assessments_status where Assessment_ID='$Assessment_ID' order by User";
						$result = $db->query($sql);
						while($row = $result->fetch_assoc())
						{
							$User=htmlspecialchars($row["User"], ENT_QUOTES);
							$ResultName=getNameGivenEmail($User);
							ShowAssessmentResults($Assessment_ID,$User,$ResultName,$questioncount,$owner);
						}
					}
								
			echo "</tbody>";
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
		$("#myTable").tableHeadFixer({ 'head' : true, 'left' : 1 });
		$("#myTable").tablesorter({ sortList: [[0,0]] });
		
		//Check Window Width
		tableContainer();
		$(window).resize(function(){ tableContainer(); });
		function tableContainer()
		{
			var height=$(".mdl-layout__content").height();
			height=height-250;
			height=height+'px';
			$(".tableholder").css("max-height", height);
		}
					
	});
	
		//Remove Student Result
		$( ".removeresult" ).click(function()
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
	
				
</script>