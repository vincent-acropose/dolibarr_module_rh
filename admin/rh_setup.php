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
 * 	\file		admin/rh.php
 * 	\ingroup	rh
 * 	\brief		This file is an example module setup page
 * 				Put some comments here
 */
// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
    $res = @include("../../../main.inc.php"); // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/rh.lib.php';

// Translations
$langs->load("rh@rh");

// Access control
if (! $user->admin) {
    accessforbidden();
}

function clear_habilitations($db) {
    $sql = "DELETE FROM ".MAIN_DB_PREFIX."rh_hab_name";
    
    $db->query($sql);
}

function get_habilitations($db) {
    $sql = "SELECT * FROM ".MAIN_DB_PREFIX."rh_hab_name";
    $habilitations = $db->query($sql);

    $value = "";
    foreach ($habilitations as $habilitation) {
        $value .= $habilitation['rowid']." : ".$habilitation['label']."\n";
    }

    return $value;
}

function set_habilitation($db, $id, $value) {
    $sql = "INSERT INTO ".MAIN_DB_PREFIX."rh_hab_name (rowid, label) VALUES (".$id.", '".trim($value)."')";

    $db->query($sql);
}

// Parameters
$action = GETPOST('action', 'alpha');

/*
 * Actions
 */
if ($action == "set_habilitations") {
    clear_habilitations($db);

    $habilitations = GETPOST('habilitations');
    $habilitations = explode("\n", $habilitations);
    foreach ($habilitations as $habilitation) {
        $habilitation = explode(":", $habilitation);
        if ($habilitation[0] != "") {
            set_habilitation($db, $habilitation[0], $habilitation[1]);
        }
    }   
}


/*
 * View
 */
$page_name = "rhSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
    . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = rhAdminPrepareHead();
dol_fiche_head(
    $head,
    'settings',
    $langs->trans("Module1002Name"),
    0,
    "rh@rh"
);

// Setup page goes here
$form=new Form($db);
$var=false;
print '<table class="noborder" width="100%">';


_print_title("Parameters");

_print_input_form_part('habilitations',$langs->trans('ParamLabel'),$langs->trans('ParamDesc'),array("value"=>get_habilitations($db), "cols"=>10, "rows"=>10),'textarea');

print '</table>';

llxFooter();

$db->close();



function _print_title($title="")
{
    global $langs;
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans($title).'</td>'."\n";
    print '<td align="center" width="20">&nbsp;</td>';
    print '<td align="center" ></td>'."\n";
    print '</tr>';
}

function _print_on_off($confkey, $title = false, $desc ='')
{
    global $var, $bc, $langs, $conf;
    $var=!$var;
    
    print '<tr '.$bc[$var].'>';
    print '<td>'.($title?$title:$langs->trans($confkey));
    if(!empty($desc))
    {
        print '<br><small>'.$langs->trans($desc).'</small>';
    }
    print '</td>';
    print '<td align="center" width="20">&nbsp;</td>';
    print '<td align="center" width="300">';
    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="set_habilitations">';
    print ajax_constantonoff($confkey);
    print '</form>';
    print '</td></tr>';
}

function _print_input_form_part($confkey, $title = false, $desc ='', $metas = array(), $type='input', $help = false)
{
    global $var, $bc, $langs, $conf, $db;
    $var=!$var;
    
    $form=new Form($db);
    
    $defaultMetas = array(
        'name' => $confkey
    );
    
    if($type!='textarea'){
        $defaultMetas['type']   = 'text';
        $defaultMetas['value']  = $conf->global->{$confkey};
    }
    
    
    $metas = array_merge ($defaultMetas, $metas);
    $metascompil = '';
    foreach ($metas as $key => $values)
    {
        $metascompil .= ' '.$key.'="'.$values.'" ';
    }
    
    print '<tr '.$bc[$var].'>';
    print '<td>';
    
    if(!empty($help)){
        print $form->textwithtooltip( ($title?$title:$langs->trans($confkey)) , $langs->trans($help),2,1,img_help(1,''));
    }
    else {
        print $title?$title:$langs->trans($confkey);
    }
    
    if(!empty($desc))
    {
        print '<br><small>'.$langs->trans($desc).'</small>';
    }
    
    print '</td>';
    print '<td align="center" width="20">&nbsp;</td>';
    print '<td align="right" width="300">';
    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="set_'.$confkey.'">';
    if($type=='textarea'){
        print '<textarea '.$metascompil.'  >'.$metas['value'].'</textarea>';
    }
    else {
        print '<input '.$metascompil.'  />';
    }
    
    print '<input type="submit" class="butAction" value="'.$langs->trans("Modify").'">';
    print '</form>';
    print '</td></tr>';
}