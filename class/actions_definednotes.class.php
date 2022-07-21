<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
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

/**
 * \file    class/actions_definednotes.class.php
 * \ingroup definednotes
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsDefinedNotes
 */
class ActionsDefinedNotes
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */

	function createDictionaryFieldlist($parameters, &$object, &$action, $hookmanager) {

        $dictionnariesTablePrefix = '';
		if(intval(DOL_VERSION) < 16) $dictionnariesTablePrefix = MAIN_DB_PREFIX;
		if($parameters['tabname'] != $dictionnariesTablePrefix.'c_predefinednotes') return 0;
		
		if(GETPOST('action', 'alphanohtml')=='edit') {
			echo '<td colspan="3"></td>';
			return 1;
		}
		
		return $this->editDictionaryFieldlist($parameters, $object, $action, $hookmanager);

	}

	function editDictionaryFieldlist($parameters, &$object, &$action, $hookmanager) {

		global $conf,$db, $langs;

		 $dictionnariesTablePrefix = '';
		if(intval(DOL_VERSION) < 16) $dictionnariesTablePrefix = MAIN_DB_PREFIX;
		if($parameters['tabname'] != $dictionnariesTablePrefix.'c_predefinednotes') return 0;

		echo '<td><input class="flat quatrevingtpercent" value="'.htmlentities($object->label).'" name="label" type="text"></td>';
		dol_include_once('/core/class/doleditor.class.php');
		$doleditor = new DolEditor('content',$object->content, '', 200, 'dolibarr_notes');
		echo '<td>'.$doleditor->Create(1).'</td>';

		$form=new Form($db);
		echo '<td>'.$form->selectarray('element',array(
				'all'=>$langs->trans('All')
				,'propal'=>$langs->trans('Proposal')
				,'commande'=>$langs->trans('Order')
				,'facture'=>$langs->trans('Invoice')
				,'shipping'=>$langs->trans('Shipping')

		),$object->element).'</td>';

		return 1;

	}

	function formobjectoptions($parameters, &$object, &$action, $hookmanager)
	{
		if($action == 'create'
				&& ($object->element == 'propal' || $object->element == 'commande' || $object->element == 'facture' || $object->element == 'shipping')
				&& in_array('globalcard',explode(':',$parameters['context']))) {

			global $langs;

			require_once DOL_DOCUMENT_ROOT . '/core/lib/functions.lib.php';

			$langs->load('definednotes@definednotes');
			$predefined_note_public_concat = GETPOST('predefined_note_public_concat', 'alphanohtml');
			$predefined_note_private_concat = GETPOST('predefined_note_private_concat', 'alphanohtml');

			?>
			<script type="text/javascript">
				$(document).ready(function() {
					$("#predefined_note_public_concat").click(function() {
						if($(this).prop('checked')) {
							$("#predefined_public_note_langs").html("<?php print $langs->transnoentities('PredefinedNotePublic2'); ?>");
						} else {
							$("#predefined_public_note_langs").html("<?php print $langs->transnoentities('PredefinedNotePublic'); ?>");
						}
					});
					$("#predefined_note_private_concat").click(function() {
                                                if($(this).prop('checked')) {
                                                        $("#predefined_private_note_langs").html("<?php print $langs->transnoentities('PredefinedNotePrivate2'); ?>");
                                                } else {
                                                        $("#predefined_private_note_langs").html("<?php print $langs->transnoentities('PredefinedNotePrivate'); ?>");
                                                }
                                        });
				});
			</script>
			<?php

			$array = $this->getArrayOfNote($object);

			if(empty($array)) return 0;

			$form=new Form($object->db);

			if($object->element!='product') {
//var_dump($predefined_note_public_concat);exit;
				echo '<tr><td id="predefined_public_note_langs">';
				if(!empty($predefined_note_public_concat)) echo $langs->trans('PredefinedNotePublic2');
				else echo $langs->trans('PredefinedNotePublic');
				echo '</td><td>';
				echo $form->selectarray('predefined_note_public', $array,GETPOST('predefined_note_public', 'int'),1).'&nbsp;'
					.'<input type="checkbox" id="predefined_note_public_concat" name="predefined_note_public_concat" '
					.(!empty($predefined_note_public_concat) ? 'checked="checked"' : '').' value="1" />'
					.img_help(1, $langs->trans('DefinedNotesCheckboxConcatPublic'));
				echo '</td></tr>';

			}

			echo '<tr><td id="predefined_private_note_langs">';
			if(!empty($predefined_note_private_concat)) echo $langs->trans('PredefinedNotePrivate2');
			else echo $langs->trans('PredefinedNotePrivate');
			echo '</td><td>';
			echo $form->selectarray('predefined_note_private', $array,GETPOST('predefined_note_private', 'int'),1).'&nbsp;'
				.'<input type="checkbox" id="predefined_note_private_concat" name="predefined_note_private_concat" '
				.(!empty($predefined_note_private_concat) ? 'checked="checked"' : '').' value="1" />'
				.img_help(1, $langs->trans('DefinedNotesCheckboxConcatPrivate'));
			echo '</td></tr>';

		}

	}


	function getArrayOfNote(&$object) {
		global $conf;
		$db = &$object->db;
		$Tab=array();

		$res = $db->query("SELECT rowid, label FROM ".MAIN_DB_PREFIX."c_predefinednotes WHERE active=1 AND entity=".$conf->entity." AND element IN ('".$object->element."','all')");
		if($res!==false) {

			while($obj = $db->fetch_object($res)) {

				$Tab[$obj->rowid] = $obj->label;

			}

		}

		return $Tab;
	}

}
