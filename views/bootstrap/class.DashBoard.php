<?php
/**
 * Implementation of DashBoard view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for DashBoard view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_DashBoard extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$orderby = $this->params['orderby'];
		$enableFolderTree = $this->params['enableFolderTree'];
		$showtree = $this->params['showtree'];
		$cachedir = $this->params['cachedir'];

		$this->htmlStartPage(getMLText("dashboard"));

		$this->globalNavigation($folder);
		$this->contentStart();

		$this->contentHeading("Willkommen im Onlineportal");
?>
		<div class="row-fluid">
		<div class="span12">
		  <?php $this->contentHeading('Gruppen'); ?>
		  <div class="well">
			  Hier eine Übersicht der Gruppen, auf die der Anwender zugreifen darf.
			</div>
		</div>
		<div>
		<div class="row-fluid">
		<div class="span4">
		  <?php $this->contentHeading('Lesezeichen'); ?>
		  <div class="well">
			  <table class="table"><thead>
				<tr>
				<th></th>
				<th>Name</th>
				<th>Besitzer</th>
				<th>Status</th>

				</tr>
				</thead>
				<tbody>
				<tr><td><a href="../op/op.Download.php?documentid=403&version=1"><img class="mimeicon" width="40"src="../op/op.Preview.php?documentid=403&version=1&width=40" title="application/pdf"></a></td><td><a href="out.ViewDocument.php?documentid=403&showtree=1">walking-paper-4hxq62d9.pdf</a></td>
				<td>Admin</td><td>freigegeben</td></tr>
				</tbody>
				</table>
			</div>
		  <?php $this->contentHeading('Neue Dokumente'); ?>
		  <div class="well">
			</div>
		  <?php $this->contentHeading('Dokumente zur Prüfung'); ?>
		  <div class="well">
			</div>
		  <?php $this->contentHeading('Dokumente zur Genehmigung'); ?>
		  <div class="well">
			</div>
		</div>
		<div class="span4">
		  <?php $this->contentHeading('Neue Beiträge im Wiki'); ?>
		  <div class="well">
			  <table class="table"><thead>
				<tr>
				<th></th>
				<th>Name</th>
				<th>Besitzer</th>
				<th>Geändert</th>

				</tr>
				</thead>
				<tbody>
				<tr><td><a href="../op/op.Download.php?documentid=403&version=1"><img class="mimeicon" width="40"src="../op/op.Preview.php?documentid=403&version=1&width=40" title="application/pdf"></a></td><td><a href="out.ViewDocument.php?documentid=403&showtree=1">Konzept Bebauung Waldstr.</a></td>
				<td>H. Huber</td><td>28.11.2013</td></tr>
				</tbody>
				</table>
			</div>
		  <?php $this->contentHeading('Zuletzt bearbeitet'); ?>
		  <div class="well">
			</div>
		</div>
		<div class="span4">
		  <?php $this->contentHeading('Neue Beiträge im Diskussionsforum'); ?>
		  <div class="well">
			</div>
		</div>
		</div>
		
<?

		$this->htmlEndPage();
	} /* }}} */
}

?>
