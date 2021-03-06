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

		if($_SESSION['usertype']=="staff")
	    {    
			echo "
				'assessments': function(name)
				{
				    $('#navigation_top').hide();
				    $('#content_holder').hide();
				    $('#loader').show();
				    $('#titletext').text('Assessments');
				    document.title = 'Assessments';
					$('#content_holder').load('modules/".basename(__DIR__)."/assessments_display_all.php', function() { init_page(); });
					$('#modal_holder').load('modules/".basename(__DIR__)."/modals.php');
					ga('set', 'page', '/#assessments/');
					ga('send', 'pageview');
			    },
			    'assessments/results/?:assessmentid': function(assessmentid)
				{
				    $('#navigation_top').hide();
				    $('#content_holder').hide();
				    $('#loader').show();
				    $('#titletext').text('Assessment Results');
				    document.title = 'Assessment Results';
					$('#content_holder').load('modules/".basename(__DIR__)."/results_summary.php?assessmentid='+assessmentid, function() { init_page(); back_button('#assessments'); });
					$('#modal_holder').load('modules/".basename(__DIR__)."/modals.php');
					ga('set', 'page', '/#assessments/results/');
					ga('send', 'pageview');
			    },
				'assessments/results/summary/?:assessmentid': function(assessmentid)
				{
				    $('#navigation_top').hide();
				    $('#content_holder').hide();
				    $('#loader').show();
				    $('#titletext').text('Assessments');
				    document.title = 'Assessment Results';
					$('#content_holder').load('modules/".basename(__DIR__)."/results_resultssummary.php?assessmentid='+assessmentid, function() { init_page(); back_button('#assessments'); });
					ga('set', 'page', '/#assessments/results/summary/');
					ga('send', 'pageview');
			    },
				'assessments/settings': function(name)
				{
				    $('#navigation_top').hide();
				    $('#content_holder').hide();
				    $('#loader').show();
				    $('#titletext').text('Assessments');
				    document.title = 'My Assessments';
					$('#content_holder').load('modules/".basename(__DIR__)."/settings.php', function() { 
						init_page();
						back_button('#assessments');
					});
					ga('set', 'page', '/#assessments/settings/');
					ga('send', 'pageview');
			    },
			    'assessments/?:name': function(name)
			    {
				    $('#navigation_top').hide();
				    $('#content_holder').hide();
				    $('#loader').show();
				    $('#titletext').text('Assessment Builder');
				    document.title = 'Assessment Builder';
					$('#content_holder').load('modules/".basename(__DIR__)."/assessment_overview.php?id='+name, function() { 
						init_page();
						back_button('#assessments');
					});
					$('#modal_holder').load('modules/".basename(__DIR__)."/modals.php');
					ga('set', 'page', '/#assessments/');
					ga('send', 'pageview');
			    },";
		}	 
 
			echo "
			    'assessments/session/?:id/?:sessionid': function(id, sessionid)
			    {
				    $('#navigation_top').hide();
				    $('#content_holder').hide();
				    $('.mdl-layout__header').hide();
				    $('#loader').show();
				    $('#titletext').text('Student Assessment');
				    document.title = 'Student Assessment';
					$('#content_holder').load('modules/".basename(__DIR__)."/assessment_student.php?id='+id+'&sessionid='+sessionid, function() { 
						init_page();
						$('.mdl-layout__header').hide();
					});
					ga('set', 'page', '/#assessments/session/');
					ga('send', 'pageview');
			    },";
?>