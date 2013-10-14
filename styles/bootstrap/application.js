
$(document).ready( function() {
	$('body').on('hidden', '.modal', function () {
		$(this).removeData('modal');
	});

	$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });

	$('#expirationdate, #fromdate, #todate, #createstartdate, #createenddate, #expirationstartdate, #expirationenddate')
		.datepicker()
		.on('changeDate', function(ev){
			$(ev.currentTarget).datepicker('hide');
		});

	$(".chzn-select").chosen();
	$(".chzn-select-deselect").chosen({allow_single_deselect:true});

	$(".pwd").passStrength({
		url: "../op/op.Ajax.php",
		onChange: function(data, target) {
			pwsp = 100*data.score;
			$('#'+target+' div.bar').width(pwsp+'%');
			if(data.ok) {
				$('#'+target+' div.bar').removeClass('bar-danger');
				$('#'+target+' div.bar').addClass('bar-success');
			} else {
				$('#'+target+' div.bar').removeClass('bar-success');
				$('#'+target+' div.bar').addClass('bar-danger');
			}
		}
	});

	/* The typeahead functionality useѕ the rest api */
	$("#searchfield").typeahead({
		minLength: 3,
		source: function(query, process) {
			$.get('../restapi/index.php/search', { query: query, limit: 8, mode: 'typeahead' }, function(data) {
					process(data);
			});
		},
		/* updater is called when the item in the list is clicked. It is
		 * actually provided to update the input field, but here we use
		 * it to set the document location. */
		updater: function (item) {
			document.location = "../op/op.Search.php?query=" + encodeURIComponent(item.substring(1));
			return item;
		},
		/* Set a matcher that allows any returned value */
		matcher : function (item) {
			return true;
		},
		highlighter : function (item) {
			if(item.charAt(0) == 'D')
				return '<i class="icon-file"></i> ' + item.substring(1);
			else if(item.charAt(0) == 'F')
				return '<i class="icon-folder-close-alt"></i> ' + item.substring(1);
			else
				return '<i class="icon-search"></i> ' + item.substring(1);
		}
	});

	/* Document chooser */
	$("[id^=choosedocsearch]").typeahead({
		minLength: 3,
		formname: 'form1',
		source: function(query, process) {
//		console.log(this.options);
			$.get('../op/op.Ajax.php', { command: 'searchdocument', query: query, limit: 8 }, function(data) {
					process(data);
			});
		},
		/* updater is called when the item in the list is clicked. It is
		 * actually provided to update the input field where you type, but here
		 * we use it to update a second input field with the doc id. */
		updater: function (item) {
			strarr = item.split("#");
			//console.log(this.options.formname);
			$('#docid' + this.options.formname).attr('value', strarr[0]);
			return strarr[1];
		},
		/* Set a matcher that allows any returned value */
		matcher : function (item) {
			return true;
		},
		highlighter : function (item) {
			strarr = item.split("#");
			return '<i class="icon-file"></i> ' + strarr[1];
		}
	});

	/* Folder chooser */
	$("[id^=choosefoldersearch]").typeahead({
		minLength: 3,
		formname: 'form1',
		source: function(query, process) {
//		console.log(this.options);
			$.get('../op/op.Ajax.php', { command: 'searchfolder', query: query, limit: 8 }, function(data) {
					process(data);
			});
		},
		/* updater is called when the item in the list is clicked. It is
		 * actually provided to update the input field, but here we use
		 * it to set the document location. */
		updater: function (item) {
			strarr = item.split("#");
			//console.log(this.options.formname);
			$('#targetid' + this.options.formname).attr('value', strarr[0]);
			return strarr[1];
		},
		/* Set a matcher that allows any returned value */
		matcher : function (item) {
			return true;
		},
		highlighter : function (item) {
			strarr = item.split("#");
			return '<i class="icon-folder-close-alt"></i> ' + strarr[1];
		}
	});

	$('a.addtoclipboard').click(function(ev){
		ev.preventDefault();
		attr_rel = $(ev.currentTarget).attr('rel');
		attr_msg = $(ev.currentTarget).attr('msg');
		type = attr_rel.substring(0, 1) == 'F' ? 'folder' : 'document';
		id = attr_rel.substring(1);
		$.get('../op/op.Ajax.php',
			{ command: 'addtoclipboard', type: type, id: id },
			function(data) {
				console.log(data);
				$('#menu-clipboard ul').remove();
				$(data).appendTo('#menu-clipboard');
				noty({
					text: attr_msg,
					type: 'success',
					dismissQueue: true,
					layout: 'topRight',
					theme: 'defaultTheme',
					timeout: 1500,
				});
			}
		);
	});
});

function allowDrop(ev) {
	ev.preventDefault();
	return false;
}

function onDragStartDocument(ev) {
	attr_rel = $(ev.target).attr('rel');
	ev.dataTransfer.setData("id", attr_rel.split("_")[1]);
	ev.dataTransfer.setData("type","document");
}

function onDragStartFolder(ev) {
	attr_rel = $(ev.target).attr('rel');
	ev.dataTransfer.setData("id", attr_rel.split("_")[1]);
	ev.dataTransfer.setData("type","folder");
}

function onDrop(ev) {
	ev.preventDefault();
	ev.stopPropagation();
	attr_rel = $(ev.currentTarget).attr('rel');
	target_type = attr_rel.split("_")[0];
	target_id = attr_rel.split("_")[1];
	source_type = ev.dataTransfer.getData("type");
	source_id = ev.dataTransfer.getData("id");
	if(source_type == 'document') {
		url = "../out/out.MoveDocument.php?documentid="+source_id+"&targetid="+target_id;
		document.location = url;
	} else if(source_type == 'folder') {
		url = "../out/out.MoveFolder.php?folderid="+source_id+"&targetid="+target_id;
		document.location = url;
	}
//	console.log(attr_rel);
//	console.log(ev.dataTransfer.getData("type") + ev.dataTransfer.getData("id"));
}

function onAddClipboard(ev) {
	ev.preventDefault();
	source_type = ev.dataTransfer.getData("type");
	source_id = ev.dataTransfer.getData("id");
	if(source_type == 'document' || source_type == 'folder') {
		url = "../op/op.AddToClipboard.php?id="+source_id+"&type="+source_type;
		document.location = url;
	}
}
