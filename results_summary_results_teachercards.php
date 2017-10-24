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
		
		//Get Passed Variables
		$Assessment_ID=htmlspecialchars($_GET["assessmentid"], ENT_QUOTES);
		$Teachers=htmlspecialchars($_GET["teachers"], ENT_QUOTES);
		
		//Loop through each teacher to show card
		$EachTeacherCode = explode(',', $Teachers);
		if(!empty($Teachers))
		{
			foreach($EachTeacherCode as $TeacherCode)
			{
				
				//Find the Teachers Name
				$StaffName=getStaffNameGivenStaffID($TeacherCode);
				
				//Get all Students for a teacher
				$StaffRoster=getTeacherRosterScoreBreakdown($TeacherCode,$Assessment_ID);
				$StudentCount=count($StaffRoster);
				
				$TotalStudentScore=0;
				$TotalIEPStudents=0; $TotalIEPScore=0;
				$TotalELLStudents=0; $TotalELLScore=0;
				$TotalGiftedStudents=0; $TotalGiftedScore=0;
				
				for ($row = 0; $row < $StudentCount; $row++)
				{
					
					$PossiblePoints=$StaffRoster[$row]["PossiblePoints"];
					$TotalStudentScore=$TotalStudentScore+$StaffRoster[$row]["Score"];
					
				    if($StaffRoster[$row]["IEP"]=="Y") {
				         $TotalIEPStudents++;
				         $TotalIEPScore=$TotalIEPScore+$StaffRoster[$row]["Score"];
				    }
				    if($StaffRoster[$row]["ELL"]!="N") {
				         $TotalELLStudents++;
				         $TotalELLScore=$TotalELLScore+$StaffRoster[$row]["Score"];
				    }
				    if($StaffRoster[$row]["Gifted"]=="Y") {
				         $TotalGiftedStudents++;
				         $TotalGiftedScore=$TotalGiftedScore+$StaffRoster[$row]["Score"];
				    }
				}
				
				if($TotalIEPStudents!=0){ 
					$IEPAverage=round(($TotalIEPScore/($TotalIEPStudents*$PossiblePoints))*100);
					$IEPAverageVerb=" - $IEPAverage%"; $IEPPercentage="$IEPAverage%"; }else{ $IEPAverageVerb=""; $IEPPercentage="0%"; }
				
				if($TotalELLStudents!=0){
					$ELLAverage=round(($TotalELLScore/($TotalELLStudents*$PossiblePoints))*100);
					$ELLAverageVerb=" - $ELLAverage%"; $ELLPercentage="$ELLAverage%"; }else{ $ELLAverageVerb=""; $ELLPercentage="0%"; }
				
				if($TotalGiftedStudents!=0){ 
					$GiftedAverage=round(($TotalGiftedScore/($TotalGiftedStudents*$PossiblePoints))*100);
					$GiftedAverageVerb=" - $GiftedAverage%"; $GiftedPercentage="$GiftedAverage%"; }else{ $GiftedAverageVerb=""; $GiftedPercentage="0%"; }
				
				if($StudentCount!=0){ 
					$StudentAverage=round(($TotalStudentScore/($StudentCount*$PossiblePoints))*100);
					$StudentsAverageVerb=" - $StudentAverage%"; $StudentPercentage="$StudentAverage%"; }else{ $StudentsAverageVerb=""; $StudentPercentage="0%"; }
	
				echo "<div class='col l4 s12'>";
				echo "<div class='card'>";
					echo "<h5 class='truncate' style='padding:25px 25px 5px 25px;'>$StaffName</h5>";
					echo "<hr>";
					echo "<div style='padding:15px 25px 20px 25px;'>";
						echo "<span><b>IEP Students ($TotalIEPStudents)</b> $IEPAverageVerb</span><br>";
						echo "<div class='progress'><div class='determinate' style='width: $IEPPercentage'></div></div>";
						echo "<span><b>ELL Students ($TotalELLStudents)</b> $ELLAverageVerb</span><br>";
						echo "<div class='progress'><div class='determinate' style='width: $ELLPercentage'></div></div>";
						echo "<span><b>Gifted Students ($TotalGiftedStudents)</b> $GiftedAverageVerb</span><br>";
						echo "<div class='progress'><div class='determinate' style='width: $GiftedPercentage'></div></div>";
						echo "<span><b>Overall ($StudentCount)</b> $StudentsAverageVerb</span>";
						echo "<div class='progress'><div class='determinate' style='width: $StudentPercentage'></div></div>";
					echo "</div>";
					echo "<hr>";
					echo "<div style='padding:15px 25px 20px 25px;'>";
						echo "<span><a class='waves-effect waves-light btn modal-teacherdetails detailsmodal' data-teachercode='$TeacherCode' href='#teacherdetails' style='background-color:"; echo sitesettings("sitecolor"); echo "'>Details</a></span>";	
					echo "</div>";
				echo "</div>";
				echo "</div>";
			}
		}
		
		
	}

?>
		
<script>
			
	//Responsive fixed table header
	$(function()
	{
    	
		//Details Button Click
		$( ".detailsmodal" ).unbind().click(function(event)
		{
			event.preventDefault();
			var TeacherCode= $(this).data('teachercode');
			$('#teacherdetails').openModal({
				in_duration: 0,
				out_duration: 0,
				ready: function()
				{
					$("#teacherdetailsfilldiv").hide();
					$("#teacherdetailsloader").show();
					$("#teacherdetailsfilldiv").load( "modules/<?php echo basename(__DIR__); ?>/teacherdetailsview.php?staffid="+TeacherCode+"&assessmentid="+<?php echo $Assessment_ID; ?>, function(){
						$("#teacherdetailsloader").hide();			
						$("#teacherdetailsfilldiv").show();
					});
      			},
			});
 		});
    	
	});
				
</script>