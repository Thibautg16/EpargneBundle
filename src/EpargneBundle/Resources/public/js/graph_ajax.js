/**
 * bundles/EpargneCompte/js/graph_ajax.js
 *
 * Copyright 2015 GILLARDEAU Thibaut (aka Thibautg16)
 *
 * Authors :
 *  - Gillardeau Thibaut (aka Thibautg16)
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://www.apache.org/licenses/LICENSE-2.0.txt
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */ 

function EpargneGraph(idCompte){
	var graph = document.getElementById('lst_graph').value;
	var debPeriode = document.getElementById('debPeriode').value
	var finPeriode = document.getElementById('finPeriode').value
	
	$('#zone').css('display','inherit');
	$('#zone').html('En cours de chargement - Merci de patienter')	
		
	$.ajax({
		type:"POST",
		url:document.getElementById('chemin').value,
		data:{idCompte:idCompte, graph:graph, debPeriode:debPeriode, finPeriode:finPeriode},
		error: function(jqXHR, textStatus, errorThrown) { $('#zone').html('error')},
		success: function(htmlResponse) { $('#zone').show("fast"); $('#zone').html(htmlResponse);}
	});
}

$(document).ready(function() {
    $( "#debPeriode" ).datepicker({
          changeMonth: true,
          changeYear: true,
		  dateFormat: "dd-mm-yy"
    });
    $( "#finPeriode" ).datepicker({
          changeMonth: true,
          changeYear: true,
		  dateFormat: "dd-mm-yy"
    });
});