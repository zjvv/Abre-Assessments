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
	require_once(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
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
				//Page Number
				if(isset($_GET["pagenumber"])){ $pagenumber=$_GET["pagenumber"]; }else{ $pagenumber=1; }
				
				if(isset($_GET["searchquery"]))
				{ 
					$question_searchquery=$_GET["searchquery"]; 
					$question_searchquery=base64_decode($question_searchquery); 
					$question_searchquery = str_replace(' ', '+', $question_searchquery); 
				}	
				if(isset($_GET["subject"]))
				{ 
					$question_subject=$_GET["subject"]; 
					$question_subject=base64_decode($question_subject); 
					$question_subject = str_replace(' ', '+', $question_subject); 
				}
				if(isset($_GET["grade"]))
				{ 
					$question_grade=$_GET["grade"];
					$question_grade=base64_decode($question_grade); 
					$question_grade = str_replace(' ', '+', $question_grade); 
				}
				if(isset($_GET["difficulty"]))
				{
					$question_difficulty=$_GET["difficulty"];
					$question_difficulty=base64_decode($question_difficulty); 
					$question_difficulty = str_replace(' ', '+', $question_difficulty); 
				}
				if(isset($_GET["type"]))
				{ 
					$question_type=$_GET["type"];
					$question_type=base64_decode($question_type); 
					$question_type = str_replace(' ', '+', $question_type); 
				}
				if(isset($_GET["blooms"]))
				{ 
					$question_blooms=$_GET["blooms"];
					$question_blooms=base64_decode($question_blooms); 
					$question_blooms = str_replace(' ', '+', $question_blooms); 
				}
				if(isset($_GET["dok"]))
				{ 
					$question_dok=$_GET["dok"];
					$question_dok=base64_decode($question_dok); 
					$question_dok = str_replace(' ', '+', $question_dok); 
				}
				if(isset($_GET["language"]))
				{ 
					$question_language=$_GET["language"];
					$question_language=base64_decode($question_language); 
					$question_language = str_replace(' ', '+', $question_language);
				}
				if(isset($_GET["standard"]))
				{ 
					$question_standard=$_GET["standard"];
					$question_standard=base64_decode($question_standard); 
					$question_standard = str_replace(' ', '+', $question_standard);
				}
				if(isset($_GET["assessmentid"])){ $assessment_id=$_GET["assessmentid"]; }
				
				//Get token
				$token=getCerticaToken();
			
				$ch = curl_init();
				
				if(isset($question_subject))
				{
					$filter="IA_Subject+eq+'$question_subject'";
					$skip=$pagenumber-1;
					if($question_grade!=""){ $filter=$filter."+and+IA_GradeLevel+eq+'$question_grade'"; }
					if($question_difficulty!=""){ $filter=$filter."+and+IA_Difficulty+eq+'$question_difficulty'"; }
					if($question_type!=""){ $filter=$filter."+and+IA_teitype+eq+'$question_type'"; }
					if($question_blooms!=""){ $filter=$filter."+and+IA_bloomstaxonomy+eq+'$question_blooms'"; }
					if($question_dok!=""){ $filter=$filter."+and+IA_DOK+eq+'$question_dok'"; }
					if($question_language!=""){ $filter=$filter."+and+IA_Lang+eq+'$question_language'"; }
					if($question_standard!=""){ $filter=$filter."+and+STD_Code+eq+'$question_standard'"; }
					if($question_standard=="" or $question_standard!=""){ $filter=$filter."+and+(std_document+eq+'CC'+or+std_document+eq+'OH')"; }
					curl_setopt($ch, CURLOPT_URL, "https://api.certicasolutions.com/items?".'$skip='."$skip".'&$filter='."$filter".'&$orderby='."IA_ItemId");
				}
				
				if(isset($question_searchquery))
                {
                    if (strpos($question_searchquery, 'C') !== false) {
                        $filter="ia_vendorid+eq+'$question_searchquery'&itembank=local";
                    }
                    else
                    {
                        //$filter="(pa_passagetitle+eq+'$question_searchquery'+or+ia_vendorid+eq+'$question_searchquery')+and+(std_document+eq+'CC'+or+std_document+eq+'OH')";
                        $filter="(ia_vendorid+eq+'$question_searchquery')+and+(std_document+eq+'CC'+or+std_document+eq+'OH')";

                    }
                    curl_setopt($ch, CURLOPT_URL, "https://api.certicasolutions.com/items?".'$filter='."$filter".'&$orderby='."IA_ItemId");
                }
				
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: IC-TOKEN Credential=$token"));
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				$json = json_decode($result,true);
				$items = $json['items'];
				
				//Show Results
				$returncount = $json['totalItems'];
				
				//Determine number of pages
				$numofpages = ceil($returncount/100);
				if($returncount==0)
				{
					echo "<h5 class='center-align'>No questions match this criteria.</h5><br>";
				}
				else
				{
					if($numofpages>1){ echo "<h5 class='center-align'>Page $pagenumber of $returncount questions</h5><br>"; }
				}
				
				foreach ($items as $value)
				{
					
			    	$subject = $value['ia_subject'];
			    	$grade = $value['ia_gradelevel'];
			    	$blooms = $value['ia_bloomstaxonomy'];
			    	$dok = $value['ia_dok'];
			    	$difficulty = $value['ia_difficulty'];
			    	$type = $value['ia_teitype'];
			    	$language = $value['ia_lang'];
			    	$standards = $value['standards'];
			    	if(isset($value['standards'][0]['std_code'])){ $standardcode=$value['standards'][0]['std_code']; }else{ $standardcode=""; }
			    	$question_id = $value['ia_itemid'];
			    	$vendor_id = $value['ia_vendorid'];
			    	
			    	//Icon
			 		if($subject=="Math"){ $icon="pie_chart"; }
					if($subject=="Science"){ $icon="wb_sunny"; }
					if($subject=="Language Arts"){ $icon="description"; }
					if($subject=="History/Social Studies"){ $icon="public"; }   
					
					//Type
					if($type=="MC"){ $type="Multiple Choice"; }
					if($type=="CM"){ $type="Choice Multiple"; }
					if($type=="GM"){ $type="Gap Match"; }
					if($type=="GR"){ $type="Graphic Gap Match"; }
					if($type=="HS"){ $type="Hot Spot"; }
					if($type=="HT"){ $type="Hot Text"; }
					if($type=="IC"){ $type="Inline Choice"; }
					if($type=="OD"){ $type="Order"; }
					if($type=="OR"){ $type="Open Response"; }
					if($type=="TE"){ $type="Text Entry"; }
					
					//Check to see if question already added to assessment
					$resultcheck = $db->query("SELECT *  FROM assessments_questions WHERE Assessment_ID='$assessment_id' and Bank_ID='$question_id'");
					$assessmentcount=mysqli_num_rows($resultcheck);
					$addbutton="false";
					if($assessmentcount==0){ $addbutton="true"; }
			    	
					echo "<table style='width:100%;'>";
					
						echo "<tr class='attachwrapper'><td style='border:1px solid #e1e1e1; width:70px; background-color:".sitesettings("sitecolor")."''><i class='material-icons' style='padding:18px; margin:0; color:#fff; font-size: 24px; line-height:0;'>$icon</i></td><td style='background-color:#F5F5F5; border-left:1px solid #e1e1e1; border-top:1px solid #e1e1e1; border-bottom:1px solid #e1e1e1; padding:10px;'>";
							echo "<p class='mdl-color-text--black' style='font-weight:500;'>$subject Question - $vendor_id</p>";
							echo "<div class='chip'>$grade</div><div class='chip'>$type</div><div class='chip'>$difficulty</div><div class='chip'>$blooms</div><div class='chip'>$standardcode</div><div class='chip'>$language</div>";
							
							
							echo "</td><td style='background-color:#F5F5F5; border:1px solid #e1e1e1; padding:12px 10px 10px 22px; width:70px;'><a href='#' data-question='$question_id' data-vendor='$vendor_id' data-assessment='$assessment_id' data-subject='$subject' data-grade='$grade' data-blooms='$blooms' data-difficulty='$difficulty' data-type='$type' data-standard='$standardcode' data-addbutton='$addbutton' class='previewquestion' style='color: ".sitesettings("sitecolor")."'><i class='material-icons'>visibility</i></a></td>";
							
							if($assessmentcount==0){
								echo "</td><td style='background-color:#F5F5F5; border:1px solid #e1e1e1; padding:12px 10px 10px 22px; width:70px;'><a href='#' data-link='/modules/".basename(__DIR__)."/question_add_process.php?assessmentid=$assessment_id&questionid=$question_id&vendorid=$vendor_id&type=$type&difficulty=$difficulty&standard=$standardcode' style='color: ".sitesettings("sitecolor")."' class='addquestiontoassessment' id='questionplus-$question_id'><i class='material-icons'>add_circle</i></a></td>";
							}
							else
							{
								echo "</td><td style='background-color:#F5F5F5; border:1px solid #e1e1e1; padding:12px 10px 10px 22px; width:70px;'></td>";
							}
						echo "</tr>";
					echo "</table>";
			    	
				}
				curl_close($ch);
				
				//Paging
				if($returncount>100)
				{
					$previouspage=$pagenumber-1;
					$nextpage=$pagenumber+1;
					echo "<div class='row'><br>";
					echo "<ul class='pagination center-align'>";
						if($pagenumber!=1){ echo "<li class='pagebutton' data-page='$previouspage'><a href='#'><i class='material-icons'>chevron_left</i></a></li>"; }
						
						if($pagenumber>5)
						{ 
							if($numofpages>$pagenumber+5){ $pagingstart=$pagenumber-5; $pagingend=$pagenumber+5;  }else{ $pagingstart=$pagenumber-5; $pagingend=$numofpages; }
						}
						else
						{
							if($numofpages>=10){ $pagingstart=1; $pagingend=10; }else{ $pagingstart=1; $pagingend=$numofpages; }
						}
						
					    for ($x = $pagingstart; $x <= $pagingend; $x++) {
							if($pagenumber==$x)
							{
								echo "<li class='active pagebutton' style='background-color: ".sitesettings("sitecolor").";' data-page='$x'><a href='#'>$x</a></li>";
							}
							else
							{
								echo "<li class='waves-effect pagebutton' data-page='$x'><a href='#'>$x</a></li>";
							}
						}
						
					    if($pagenumber!=$numofpages){ echo "<li class='waves-effect pagebutton' data-page='$nextpage'><a href='#'><i class='material-icons'>chevron_right</i></a></li>"; }
					echo "</ul>";
					echo "</div>";
				}
					
			?>
			
			<script>
				
				$(function()
				{
			
					//Add question to assessment
					$( ".addquestiontoassessment" ).unbind().click(function()
					{
						
						event.preventDefault();
						$(this).hide();
						var address= $(this).data('link');
						$.ajax({
							type: 'POST',
							url: address,
							data: '',
						})
						
					});
					
					//Preview the assessment question
					$( ".previewquestion" ).unbind().click(function() {
						
						event.preventDefault();
						$('#previewmeta').show();
						
						$(".modal-content #questionholder").html("<div style='padding:20px;'>Loading Question...</div>");
						
						var AddButton = $(this).data('addbutton');
						if(AddButton==false)
						{
							$(".addquestiontoassessmentpreview").css("display", "none");
						}
						else
						{
							$(".addquestiontoassessmentpreview").css("display", "block");
						}
						
						var Question = $(this).data('question');
						$(".modal-content #QuestionID").val(Question);
						
						var Vendor = $(this).data('vendor');
						$(".modal-content #preview_questionid").html(Vendor);
						$(".modal-content #VendorID").val(Vendor);
						
						var Type = $(this).data('type');
						$(".modal-content #preview_questiontype").html(Type);
						$(".modal-content #Type").val(Type);
						
						var Subject = $(this).data('subject');
						$(".modal-content #preview_subject").html(Subject);
						
						var Grade = $(this).data('grade');
						$(".modal-content #preview_grade").html(Grade);
						
						var Blooms = $(this).data('blooms');
						$(".modal-content #preview_blooms").html(Blooms);
						
						var Difficulty = $(this).data('difficulty');
						$(".modal-content #preview_difficulty").html(Difficulty);
						$(".modal-content #Difficulty").val(Difficulty);
						
						var Standard = $(this).data('standard');
						$(".modal-content #StandardCode").val(Standard);
						
						$(".modal-content #questionholder").load( "modules/<?php echo basename(__DIR__); ?>/question_viewer.php?id="+Question, function(){
						});
						
						$('#linktotopic').openModal({
							in_duration: 0,
							out_duration: 0,
						});
					});
					
				});
				
				
			</script>
			
	<?php
		
		}
		
	?>