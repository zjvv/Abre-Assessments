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

		if($_SESSION['usertype']=="staff")
	    {    
			echo "
				'assessments': function(name)
				{
				    $('#navigation_top').hide();
				    $('#content_holder').hide();
				    $('#loader').show();
				    $('#titletext').text('Assessments');
				    document.title = 'My Assessments';
					$('#content_holder').load('modules/".basename(__DIR__)."/assessments_display_all.php', function() { init_page(); });
					$('#modal_holder').load('modules/".basename(__DIR__)."/modals.php');
		
					$('#navigation_top').show();
					$('#navigation_top').load('modules/".basename(__DIR__)."/menu_main.php', function() {	
						$('#navigation_top').show();
						$('.tab_1').addClass('tabmenuover');
					});	
			    },
				'assessments/settings': function(name)
				{
				    $('#navigation_top').hide();
				    $('#content_holder').hide();
				    $('#loader').show();
				    $('#titletext').text('Assessments');
				    document.title = 'My Assessments';
					$('#content_holder').load('modules/".basename(__DIR__)."/settings.php', function() { init_page(); });
			    },
			    'assessments/?:name': function(name)
			    {
				    $('#navigation_top').hide();
				    $('#content_holder').hide();
				    $('#loader').show();
				    $('#titletext').text('Assessments');
				    document.title = 'Assessment Editor';
					$('#content_holder').load('modules/".basename(__DIR__)."/assessment.php?id='+name, function() { 
						init_page();		
					});		
					$('#modal_holder').load('modules/".basename(__DIR__)."/modals.php');
					
					//Load Navigation
					$('#navigation_top').show();
					$('#navigation_top').load('modules/".basename(__DIR__)."/menu_builder.php?id='+name, function() {	
						$('#navigation_top').show();
						$('.tab_2').addClass('tabmenuover');
					});	
			    },";
		}	  
?>