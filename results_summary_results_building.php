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
		
		//Find the District Assessment results
		$CompleteResults=getAllScoresByAssessment($Assessment_ID);
		$CompleteCount=count($CompleteResults);
		
		$TotalIEPStudents=0;
		$TotalELLStudents=0;
		$TotalGiftedStudents=0;
		
		for ($row = 0; $row < $CompleteCount; $row++)
		{
			$PossiblePoints=$CompleteResults[$row]["PossiblePoints"];
			$TotalStudentScore=$TotalStudentScore+$CompleteResults[$row]["Score"];
					
			if($CompleteResults[$row]["IEP"]=="Y") {
				$TotalIEPStudents++;
				$TotalIEPScore=$TotalIEPScore+$CompleteResults[$row]["Score"];
			}
			if($CompleteResults[$row]["ELL"]!="N") {
				$TotalELLStudents++;
				$TotalELLScore=$TotalELLScore+$CompleteResults[$row]["Score"];
			}
			if($CompleteResults[$row]["Gifted"]=="Y") {
				$TotalGiftedStudents++;
				$TotalGiftedScore=$TotalGiftedScore+$CompleteResults[$row]["Score"];
			}
		}
		
		//Calculations
		if($TotalIEPStudents!=0){
			$DistrictIEPAverage=round(($TotalIEPScore/($TotalIEPStudents*$PossiblePoints))*100);
			$DistrictIEPAverage="$DistrictIEPAverage%";
			if($TotalIEPStudents!=1){ $IEPVerb="Students"; }else{ $IEPVerb="Student"; }
		}else{ $DistrictIEPAverage="N/A"; $IEPVerb="Students"; }

		if($TotalELLStudents!=0){
			$DistrictELLAverage=round(($TotalELLScore/($TotalELLStudents*$PossiblePoints))*100);
			$DistrictELLAverage="$DistrictELLAverage%";
			if($TotalELLStudents!=1){ $ELLVerb="Students"; }else{ $ELLVerb="Student"; }
		}else{ $DistrictELLAverage="N/A"; $ELLVerb="Students"; }
		
		if($TotalGiftedStudents!=0){
			$DistrictGiftedAverage=round(($TotalGiftedScore/($TotalGiftedStudents*$PossiblePoints))*100);
			$DistrictGiftedAverage="$DistrictGiftedAverage%";
			if($TotalGiftedStudents!=1){ $GiftedVerb="Students"; }else{ $GiftedVerb="Student"; }
		}else{ $DistrictGiftedAverage="N/A"; $GiftedVerb="Students"; }
		
		if($CompleteCount!=0){
			$DistrictOverallAverage=round(($TotalStudentScore/($CompleteCount*$PossiblePoints))*100);
			$DistrictOverallAverage="$DistrictOverallAverage%";
			if($CompleteCount!=1){ $OverallVerb="Students"; }else{ $OverallVerb="Student"; }
		}else{ $DistrictOverallAverage="N/A"; $OverallVerb="Students"; }
		
		//District Overall
		echo "<div class='row'>";	
			echo "<div class='col m3 s12'><div class='mdl-card mdl-shadow--2dp' style='width:100%; color:#fff; padding-top:45px; background-color:"; echo getSiteColor(); echo "'>";
				echo "<span class='center-align truncate' style='font-size:70px; line-height:80px;'>$DistrictIEPAverage</span>";
				echo "<span class='center-align truncate'>District IEP Average<br>($TotalIEPStudents $IEPVerb)</span>";
			echo "</div></div>";
				
			echo "<div class='col m3 s12'><div class='mdl-card mdl-shadow--2dp' style='width:100%; color:#fff; padding-top:45px; background-color:"; echo getSiteColor(); echo "'>";
				echo "<span class='center-align truncate' style='font-size:70px; line-height:80px;'>$DistrictELLAverage</span>";
				echo "<span class='center-align truncate'>District ELL Average<br>($TotalELLStudents $ELLVerb)</span>";
			echo "</div></div>";
				
			echo "<div class='col m3 s12'><div class='mdl-card mdl-shadow--2dp' style='width:100%; color:#fff; padding-top:45px; background-color:"; echo getSiteColor(); echo "'>";
				echo "<span class='center-align truncate' style='font-size:70px; line-height:80px;'>$DistrictGiftedAverage</span>";
				echo "<span class='center-align truncate'>District Gifted Average<br>($TotalGiftedStudents $GiftedVerb)</span>";
			echo "</div></div>";
				
			echo "<div class='col m3 s12'><div class='mdl-card mdl-shadow--2dp' style='width:100%; color:#fff; padding-top:45px; background-color:"; echo getSiteColor(); echo "'>";
				echo "<span class='center-align truncate' style='font-size:70px; line-height:80px;'>$DistrictOverallAverage</span>";
				echo "<span class='center-align truncate'>District Overall Average<br>($CompleteCount $OverallVerb)</span>";
			echo "</div></div>";	
		echo "</div>";			
			
		//Teacher Dropdown
		echo "<div class='row' style='margin:0;'>";
			echo "<div class='col l4 s12'>";
				if(isset($_GET["building"]))
				{
					$building=$_GET["building"];
					
					echo "<select id='buildingteacher' multiple>";
						echo "<option value='' disabled selected>Choose a Teacher/Teachers</option>";
						$query = "SELECT * FROM Abre_Staff where SchoolCode='$building' order by LastName";
						$dbreturn = databasequery($query);
						foreach ($dbreturn as $value)
						{	
							$LastName=$value['LastName'];
							$FirstName=$value['FirstName'];
							$StaffID=$value['StaffID'];
							
							echo "<option value='$StaffID'>$LastName, $FirstName</option>";
						}
					echo "</select>";
				}
				echo "</div>";	
			echo "</div>";
		echo "</div>";
			
			
		//Results Area
		echo "<div class='row'>";
			echo "<div class='col s12'><div id='p2' class='mdl-progress mdl-js-progress mdl-progress__indeterminate landingloadergridtcards' style='width:100%;'></div></div>";
			echo "<div id='teachercompareresults'></div>";
		echo "</div>";
		
	}

?>
		
<script>
			
	//Responsive fixed table header
	$(function()
	{
		
		$('select').material_select();
		$(".landingloadergridtcards").hide();
		$("#teachercompareresults").hide();
		
		function TeacherResults(Teachers)
		{
			$("#teachercompareresults").load('modules/Abre-Assessments/results_summary_results_teachercards.php?assessmentid=<?php echo $Assessment_ID; ?>&teachers='+Teachers, function(){ 
				$(".landingloadergridtcards").hide();
				$("#teachercompareresults").show();
			});
		}
		
    	//Filter Change
    	$('#buildingteacher').change(function()
    	{
	    	$(".landingloadergridtcards").show();
	    	$("#teachercompareresults").hide();
	    	var Teachers = $(this).val();
			TeacherResults(Teachers);
		});
					
	});
				
</script>