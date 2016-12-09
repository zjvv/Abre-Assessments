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
	require_once(dirname(__FILE__) . '/../../core/abre_verification.php');
	require(dirname(__FILE__) . '/../../configuration.php');
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
	require_once('functions.php');
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{
	

		$Assessment_ID=htmlspecialchars($_GET["id"], ENT_QUOTES);
		$sqllookup = "SELECT * FROM assessments where ID='$Assessment_ID'";
		$result2 = $db->query($sqllookup);
		$setting_preferences=mysqli_num_rows($result2);
		while($row = $result2->fetch_assoc())
		{
			
			$Title=htmlspecialchars($row["Title"], ENT_QUOTES);
			$Grade=htmlspecialchars($row["Grade"], ENT_QUOTES);
			$Subject=htmlspecialchars($row["Subject"], ENT_QUOTES);

			echo "<div class='page_container'>";
				echo "<div class='row'><div class='center-align' style='padding:20px;'><h3 style='font-weight:600;'>$Title</h3><h6 style='color:#777;'>$Subject &#183; Grade Level: $Grade</h6></div></div>";
			
				echo "<ul class='collapsible popout questionsort' data-collapsible='accordion'>";
			
					$sqllookup2 = "SELECT * FROM assessments_questions where Assessment_ID='$Assessment_ID' order by Question_Order";
					$result3 = $db->query($sqllookup2);
					$unitcount=mysqli_num_rows($result3);
					$questioncount=0;
					while($row2 = $result3->fetch_assoc())
					{
						$questioncount++;
						$questionid=htmlspecialchars($row2["ID"], ENT_QUOTES);
						$Bank_ID=htmlspecialchars($row2["Bank_ID"], ENT_QUOTES);
						
						//Get Information about the question
						/*
						$token=getCerticaToken();
						$ch = curl_init();
						$filter="ia_itemid+eq+'96700'";
						curl_setopt($ch, CURLOPT_URL, "https://api.certicasolutions.com/items?".'$filter='."$filter");
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: IC-TOKEN Credential=$token"));
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$result = curl_exec($ch);
						$json = json_decode($result,true);
						var_dump($json);
						$items = $json['items'];
						foreach ($items as $value)
						{

						}
						*/

						echo "<li style='position:relative' id='item-$questionid' class='topicholder'>";
							echo "<div class='collapsible-header unit' data-bankid='$Bank_ID'>";
					    		echo "<i class='material-icons' style='font-size: 36px; color:".sitesettings("sitecolor")."'>fiber_manual_record</i>";

								echo "<span style='position:absolute; right:0; z-index:1000; cursor:move;' class='mdl-color-text--grey-700 handle'>";
										echo "<i class='material-icons'>reorder</i>";	
								echo "</span>";
									
								echo "<span class='title truncate' style='margin-right:40px;'><b>Question</b></span>";
							echo "</div>";
							
							echo "<div class='collapsible-body mdl-color--white' style='padding:25px'>";
							
								//Display the question
								echo "<div id='questionplayer-$Bank_ID' style='display:none'><div class='mdl-progress mdl-js-progress mdl-progress__indeterminate' style='width:100%'></div></div>";
								echo "<hr>";
								echo "<div class='toolbar' style='padding-top:20px; text-align:right;'>";
									echo "<div class='removequestion'><a href='modules/assessments/question_remove_process.php?questionid=".$questionid."' class='mdl-color-text--grey-700' id='delete'><i class='material-icons'>delete</i></a></div>";
									echo "<div class='mdl-tooltip' data-mdl-for='delete'>Delete</div>";
								echo "</div>";
								
							echo "</div>";
						echo "</li>";
				
					}
		
				echo "</ul>";
			
			if($unitcount==0){ echo "<div class='center-align'>Click the '+' in the bottom right to add a question to this assessment."; }
			
			echo "</div>";
			
			include "question_button.php";
			
		}
		
	}

?>
	
	<script>
		
		$(function()
		{
			
			//Remove Topic from Curriculum
			$( ".removequestion" ).click(function() {
				event.preventDefault();
				var result = confirm("Remove this question?");
				if (result) {
					$(this).closest(".topicholder").hide();
					var address = $(this).find("a").attr("href");
					$.ajax({
						type: 'POST',
						url: address,
						data: '',
					})
																	
					//Show the notification
					.done(function(response) {	
						$("#content_holder").load( "modules/assessments/assessment.php?id="+<?php echo $Assessment_ID; ?>, function(){
							mdlregister();
							
							var notification = document.querySelector('.mdl-js-snackbar');
							var data = { message: response };
							notification.MaterialSnackbar.showSnackbar(data);
							
						});
					})
					
				}
			});

			//Call Accordion
			$('.collapsible').collapsible({ });			

			//Close Menu when collapsible closed
			$( ".menuui" ).unbind().click(function(event)
			{
 				$(".collapsible-header").removeClass(function(){
 					return "active";
  				});
  				$(".collapsible").collapsible({accordion: true});
				$(".collapsible").collapsible({accordion: false});
 			});
 			
			//Load the question
			$(".collapsible-header").unbind().click(function(event)
			{
				var Bank_ID= $(this).data('bankid');
 				$('#questionplayer-'+Bank_ID).hide();
 				$('.toolbar').hide();
 				
 				$.get( "modules/assessments/question_viewer.php", { id: Bank_ID } )
			    .done(function( data ) {
				    $('#questionplayer-'+Bank_ID).show();
				    $('.toolbar').show();
			    	$("#questionplayer-"+Bank_ID).html( data );
			  	});
			  	
 			});
			
			//Sortable settings
			$( ".questionsort" ).sortable({
				axis: 'y',
				handle: '.handle',
				update: function(event, ui){
					var data = $(this).sortable('serialize');
					$.ajax({
			            data: data,
			            type: 'POST',
			            url: '/modules/assessments/questions_save_order.php'
			        });
				}
			});
			
		});
		
	</script>