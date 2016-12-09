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
	require(dirname(__FILE__) . '/../../core/abre_dbconnect.php');	
	require_once(dirname(__FILE__) . '/../../core/abre_functions.php');	
	require_once('permissions.php');
	
	if($pagerestrictions=="")
	{

?>

	<!-- Create Assessment -->
	<div id="createassessment" class="modal modal-fixed-footer modal-mobile-full">
		<form class="col s12" id="form-addassessment" method="post" action="modules/assessments/assessment_process.php">
		<div class="modal-content">
			<h4>Assessment</h4>
			<a class="modal-close black-text" style='position:absolute; right:20px; top:25px;'><i class='material-icons'>clear</i></a>
			<div class="row">
				<div class="input-field col s12"><input id="assessment_title" name="assessment_title" placeholder="Title of the Assessment" type="text" required></div>
			</div>
			<div class="row">
				<div class="input-field col s12"><textarea id="assessment_description" name="assessment_description" class="materialize-textarea" placeholder="Description of the Assessment" required></textarea></div>
			</div>
			<div class="row">
			<div class="input-field col s6">
				<p class='grey-text'>Grade Level</p>
				<select name='assessment_grade[]' id='assessment_grade' class="browser-default" style='height: 100px;' required='required' multiple>
					<option value='K'>K</option>
				    <option value='1'>1</option>       
				    <option value='2'>2</option>       
				    <option value='3'>3</option>       
				    <option value='4'>4</option> 
					<option value='5'>5</option>       
				    <option value='6'>6</option>       
				    <option value='7'>7</option>       
				    <option value='8'>8</option>    
				    <option value='9'>9</option>   
				    <option value='10'>10</option>
				    <option value='11'>11</option>
				    <option value='12'>12</option>
			    </select>
			</div>
			<div class="input-field col s6">
				<p class='grey-text'>Subject</p>
				<select name='assessment_subject' id='assessment_subject' class="browser-default" required>
					<option value=''></option>   
					<option value='English Language Arts'>English Language Arts</option>    
					<option value='Mathematics'>Mathematics</option>    
				    <option value='Science'>Science</option>       
				    <option value='Social Studies'>Social Studies</option>   
				    <option value='Technology'>Technology</option>       
			    </select>
			</div>
			</div>
				
			<input type="hidden" name="assessment_id" id="assessment_id">
    	</div>
	    <div class="modal-footer">
			<button type="submit" class="modal-action waves-effect btn-flat white-text" style='margin-left:5px; background-color: <?php echo sitesettings("sitecolor"); ?>'>Save</button>
			<a class="modal-close waves-effect btn-flat white-text" style='background-color: <?php echo sitesettings("sitecolor"); ?>'>Cancel</a>
		</div>
		</form>
	</div>

	<!-- Add Question -->
	<div id="assessmentquestion" class="fullmodal modal modal-fixed-footer modal-mobile-full">
		<form class="col s12" id="form-addquestion" method="post" action="">
		<div class="modal-content">
			<h4>Question</h4>
			<a class="modal-close black-text" style='position:absolute; right:20px; top:25px;'><i class='material-icons'>clear</i></a>
			
			<!--Tabs-->
			<div class="row">
			<ul class="tabs" style='background-color: <?php echo sitesettings("sitecolor"); ?>'>
		    	<li class="tab col s3"><a class="active" href="#filter">Filter</a></li>
		        <li class="tab col s3"><a href="#search">Search</a></li>
		    </ul>
			</div>
			
			<div class="row" id="filter">
				<div class="input-field col l4 s6">
					<select name='question_subject' id='question_subject' required> 
						<option value='' selected='selected' disabled>Select a Subject</option>   
						<option value='Language Arts'>Language Arts</option>
						<option value='Math'>Math</option>
						<option value='Science'>Science</option>
						<option value='History/Social Studies'>Social Studies</option>
				    </select>
				    <label>Subject</label>
				</div>
				<div class="input-field col l4 s6">
					<select name='question_grade' id='question_grade' required>
						<option value='' selected='selected'></option>   
						<option value='Grade K'>K</option>
						<option value='Grade 01'>1</option>
						<option value='Grade 02'>2</option>
						<option value='Grade 03'>3</option>
						<option value='Grade 04'>4</option>
						<option value='Grade 05'>5</option>
						<option value='Grade 06'>6</option>
						<option value='Grade 07'>7</option>
						<option value='Grade 08'>8</option>
						<option value='Grade 09'>9</option>
						<option value='Grade 10'>10</option>
						<option value='Grade 11'>11</option>
						<option value='Grade 12'>12</option>
						<option value='Grades 09-12'>9-12</option>
				    </select>
				    <label>Grade</label>
				</div>
				<div class="input-field col l4 s6">
					<select name='question_difficulty' id='question_difficulty' required>
						<option value='' selected='selected'></option>
						<option value='Low'>Low</option>
						<option value='Medium'>Medium</option>
						<option value='High'>High</option>
				    </select>
				    <label>Difficulty</label>
				</div>
				<div class="input-field col l4 s6">
					<select name='question_itemtype' id='question_itemtype' required>
						<option value='' selected='selected'></option>
						<option value='MC'>Multiple Choice</option>
						<option value='CM'>Choice Multiple</option>
						<option value='GM'>Gap Match</option>
						<option value='GR'>Graphic Gap Match</option>
						<option value='HS'>Hot Spot</option>
						<option value='HT'>Hot Text</option>
						<option value='IC'>Inline Choice</option>
						<option value='OD'>Order</option>
						<option value='OR'>Open Response</option>
						<option value='TE'>Text Entry</option>
				    </select>
				    <label>Question Type</label>
				</div>
				<div class="input-field col l4 s6">
					<select name='question_blooms' id='question_blooms' required>
						<option value='' selected='selected'></option>
						<option value='Remembering'>Remembering</option>
						<option value='Understanding'>Understanding</option>
						<option value='Applying'>Applying</option>
						<option value='Analyzing'>Analyzing</option>
						<option value='Evaluating'>Evaluating</option>
						<option value='Creating'>Creating</option>
				    </select>
				    <label>Blooms</label>
				</div>
				<div class="input-field col l4 s6">
					<select name='question_dok' id='question_dok' required>
						<option value='' selected='selected'></option>
						<option value='I'>Recall</option>
						<option value='II'>Skill/Concept</option>
						<option value='III'>Strategic Thinking</option>
						<option value='IV'>Extended Thinking</option>
				    </select>
				    <label>Depth of Knowledge</label>
				</div>
			</div>
			
			<div class="row" id="search">
				<div class="input-field col s12">
					<input id="searchquery" type="text">
					<label for="searchquery">Search</label>
				</div>
			</div>
			
			<div class="row">
			<div class="input-field col s12">
				<div id="topicLoader" class="mdl-progress mdl-js-progress mdl-progress__indeterminate" style="width:100%"></div>
				<div id="topicFiles"></div>
			</div>
			</div>
			<input type="hidden" name="AssessmentID" id="AssessmentID">
    	</div>
	    <div class="modal-footer">
		    <a class="modal-close waves-effect btn-flat white-text" style='background-color: <?php echo sitesettings("sitecolor"); ?>'>Close</a>
		</div>
		</form>
	</div>
	
	<!-- Question Preview -->
	<div id="linktotopic" class="modal modal-fixed-footer modal-mobile-full" style="width: 80%">
		<form class="col s12" id="form-addlinktotopic" method="post" action="">
		<div class="modal-content">
			<h4>Question Preview</h4>
			<a class="modal-close black-text" style='position:absolute; right:20px; top:25px;'><i class='material-icons'>clear</i></a>

				<div class='row'>
					<div class='col l3 s6'><span class='grey-text'>Subject</span><div style='margin-top:5px; color: <?php echo sitesettings("sitecolor"); ?>' id='preview_subject'></div></div>
					<div class='col l3 s6'><span class='grey-text'>Grade</span><div style='margin-top:5px; color: <?php echo sitesettings("sitecolor"); ?>' id='preview_grade'></div></div>
					<div class='col l3 s6'><span class='grey-text'>Difficulty</span><div style='margin-top:5px; color: <?php echo sitesettings("sitecolor"); ?>' id='preview_difficulty'></div></div>
					<div class='col l3 s6'><span class='grey-text'>Blooms</span><div style='margin-top:5px; color: <?php echo sitesettings("sitecolor"); ?>' id='preview_blooms'></div></div>
				</div>
				<hr>
				<div id='questionholder'></div>
				<input type="hidden" name="AssessmentID" id="AssessmentID">
				<input type="hidden" name="QuestionID" id="QuestionID">
    	</div>
	    <div class="modal-footer">
		    <a class="modal-close waves-effect btn-flat white-text" style='background-color: <?php echo sitesettings("sitecolor"); ?>'>Close</a>
		    <a class="modal-close waves-effect btn-flat white-text addquestiontoassessmentpreview" style='margin-right:5px; background-color: <?php echo sitesettings("sitecolor"); ?>'>Add</a>
		</div>
		</form>
	</div>
	
<?php
	}	
?>

<script>
	
	$(function()
	{
		//Tabs
    	$('ul.tabs').tabs();
		
		//Hide modal loader
		$("#topicLoader").hide();

		//Material dropdown
		$('select').material_select();
		
		//Add/Edit a Assessment						
		$('#form-addassessment').submit(function(event)
		{
			event.preventDefault();
			
			var form = $('#form-addassessment');
			var formMessages = $('#form-messages');
			
			$('#createassessment').closeModal({
				in_duration: 0,
				out_duration: 0,
			});
			var formData = $(form).serialize();
			$.ajax({
				type: 'POST',
				url: $(form).attr('action'),
				data: formData
			})
							
			//Show the notification
			.done(function(response) {
				//$("input").val('');
				$("#content_holder").load( "modules/assessments/assessments_display_all.php", function(){		
						
					mdlregister();
							
					var notification = document.querySelector('.mdl-js-snackbar');
					var data = { message: response };
					notification.MaterialSnackbar.showSnackbar(data);	
				
				});
			
			})						
		});
		
		//Add question to assessment
		$( ".addquestiontoassessmentpreview" ).unbind().click(function()
		{
			
			event.preventDefault();	
			$(this).hide();
			
			//Add to the assessment
			var AssessmentID = $('#AssessmentID').val();
			var QuestionID = $('#QuestionID').val();
			var address= "/modules/assessments/question_add_process.php?assessmentid="+AssessmentID+"&questionid="+QuestionID;
			$.ajax({
				type: 'POST',
				url: address,
				data: '',
			})	
			
			//Hide Add buttons
			$('#questionplus-'+QuestionID).hide();		
			
		});
    	
    	//Question Search/Filter
    	$('#question_subject, #question_grade, #question_difficulty, #question_itemtype, #question_blooms, #question_dok').change(function()
    	{
	    	var question_subject = $('#question_subject').val();
	    	question_subject = btoa(question_subject);
	    	var question_grade = $('#question_grade').val();
	    	question_grade = btoa(question_grade);
	    	var question_difficulty = $('#question_difficulty').val();
	    	question_difficulty = btoa(question_difficulty);
	    	var question_itemtype = $('#question_itemtype').val();
	    	question_itemtype = btoa(question_itemtype);
	    	var question_blooms = $('#question_blooms').val();
	    	question_blooms = btoa(question_blooms);
	    	var question_dok = $('#question_dok').val();
	    	question_dok = btoa(question_dok);
	    	var AssessmentID = $('#AssessmentID').val();

			$("#topicFiles").hide();
			$("#topicLoader").show();
			$("#topicFiles").load('modules/assessments/questions_list_questions.php?assessmentid='+AssessmentID+'&subject='+question_subject+"&grade="+question_grade+"&difficulty="+question_difficulty+"&type="+question_itemtype+"&blooms="+question_blooms+"&dok="+question_dok, function() {
				$("#topicLoader").hide();
				$("#topicFiles").show();
			});
		});
		
		//Question Search
		$("#searchquery").keyup(function()
		{
			var search_query = $('#searchquery').val();
			var AssessmentID = $('#AssessmentID').val();
	    	search_query = btoa(search_query);
	    	$("#topicFiles").hide();
			$("#topicLoader").show();
			$("#topicFiles").load('modules/assessments/questions_list_questions.php?assessmentid='+AssessmentID+'&searchquery='+search_query, function() {
				$("#topicLoader").hide();
				$("#topicFiles").show();
			});
		});
		
		//Change Tabs
		$('ul.tabs').on('click', 'a', function(e) {
			//Hide Content
		    $("#topicFiles").hide();
		    //Clear Inputs
		    $('#searchquery').val('');
		    $('select').prop('selectedIndex', 0);
			$('select').material_select(); 
		});
		
		//Fill in Text Topic Data
		$(document).on("click", ".modal-addquestion", function ()
		{
			var Assessment_ID = $(this).data('assessmentid');
		    $(".modal-content #AssessmentID").val(Assessment_ID);
		});	
	   	
	});	
		
</script>