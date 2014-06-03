<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// This module is based in the original Attendance Module created by
// Artem Andreev <andreev.artem@gmail.com> - 2011

/**
 * Strings for component 'attcontrol', language 'ca'
 *
 * @package   mod_attcontrol
 * @copyright  2013 José Luis Antúnez <jantunez@xtec.cat>
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['attcontrol:addinstance'] = 'Add a new AttControl activity';
$string['Aacronym'] = 'F';
$string['Afull'] = 'Falta';
$string['Eacronym'] = 'J';
$string['Efull'] = 'Justificada';
$string['Lacronym'] = 'R';
$string['Lfull'] = 'Retard';
$string['Pacronym'] = 'P';
$string['Pfull'] = 'Present';
$string['acronym'] = 'Abreviatura';
$string['add'] = 'Afegeix';
$string['addsessions'] = 'Afegeix sessions';
$string['addmultiplesessions'] = 'Afegeix múltiples sessions';
$string['addsession'] = 'Afegeix sessió';
$string['allcourses'] = 'Tots els cursos';
$string['all'] = 'Tots';
$string['allpast'] = 'Tots els passats';
$string['attcontroldata'] = 'dades d\'AttControl';
$string['attcontrolforthecourse'] = 'Control d\'assistència per al curs';
$string['attcontrolnotstarted'] = 'No s\'ha iniciat el control d\'assistència per a aquest curs';
$string['attcontrolpercent'] = 'Percentatge d\'assistència';
$string['attcontrolreport'] = 'Informe d\'assistència';
$string['attcontrolsuccess'] = 'El control d\'assistència s\'ha completat correctament';
$string['attcontrolupdated'] = 'El control d\'assistència s\'ha actualitzat correctament';
$string['attcontrol:canbelisted'] = 'Apareix a la graella';
$string['attcontrol:changepreferences'] = 'Canviar les preferències';
$string['attcontrol:changeattcontrols'] = 'Canviar els AttControls';
$string['attcontrol:export'] = 'Exportar informes';
$string['attcontrol:manageattcontrols'] = 'Administrar AttControls';
$string['attcontrol:takeattcontrols'] = 'Controlar assistència';
$string['attcontrol:view'] = 'Veure AttControls';
$string['attcontrol:viewreports'] = 'Veure Informes';
$string['attforblockdirstillexists'] = 'El directori de l\'antic mod/attforblock encara existeix - cal que l\'esborreu abans de fer l\'actualització.';
$string['attrecords'] = 'registres d\'assistència';
$string['calclose'] = 'Tanca';
$string['calmonths'] = 'Gener,Febrer,Març,Abril,Maig,Juny,Juliol,Setembre,Octubre,Novembre,Desembre';
$string['calshow'] = 'Seleccioneu data';
$string['caltoday'] = 'Avui';
$string['calweekdays'] = 'Dg,Dl,Dm,Dc,Dj,Dv,Ds';
$string['cannottakeforgroup'] = 'No podeu passar faltes del grup "{$a}"';
$string['changeattcontrol'] = 'Canviar AttControl';
$string['changeduration'] = 'Canviar durada';
$string['changesession'] = 'Canviar sessió';
$string['column'] = 'columna';
$string['columns'] = 'columnes';
$string['commonsession'] = 'Comuna';
$string['commonsessions'] = 'Comunes';
$string['countofselected'] = 'Elements seleccionats';
$string['copyfrom'] = 'Copy AttControl data from';
$string['createmultiplesessions'] = 'Crea diverses sessions';
$string['createmultiplesessions_help'] = 'Aquesta funcionaltiat permet la creació de diverses sessions amb un únic pas.

  * <strong>Data inicial</strong>: Seleccioneu la data d\'inici de la recurrència.
  * <strong>Data final</strong>: Seleccioneu la data de final de la recurrència.
  * <strong>Dies de sessió</strong>: Indiqueu quins de la setmana es durà a terme la classe.
  * <strong>Freqüència</strong>: Establiu amb quina freqüència es repetiran les classes. Si les classes són cada setmana, seleccioneu 1; si són una semana sí i una no, 2; etc.
';
$string['createonesession'] = 'Crea una sessió per al curs';
$string['days'] = 'Dia';
$string['defaults'] = 'Per defecte';
$string['defaultdisplaymode'] = 'Mode per defecte';
$string['delete'] = 'Esborra';
$string['deletelogs'] = 'Esborra la informació d\'assistència';
$string['deleteselected'] = 'Esborrar els seleccionats';
$string['deletesession'] = 'Esborra sessió';
$string['deletesessions'] = 'Esborra totes les sessions';
$string['deletingsession'] = 'Esborrant la sessió per al curs';
$string['deletingstatus'] = 'Esborrant l\'estat per al curs';
$string['description'] = 'Descripció';
$string['display'] = 'Mostra';
$string['displaymode'] = 'Mode de visualització';
$string['downloadexcel'] = 'Descarrega en format Excel';
$string['downloadooo'] = 'Descarrega en format OpenOffice/LibreOffice';
$string['downloadtext'] = 'Descarrega en format text (csv)';
$string['duration'] = 'Durada';
$string['editsession'] = 'Edita sessió';
$string['endtime'] = 'Hora de finalització';
$string['endofperiod'] = 'Final del període';
$string['enrolmentend'] = 'La inscripció de l\'usuari finalitza {$a}';
$string['enrolmentstart'] = 'La inscripció de l\'usuari inicia {$a}';
$string['enrolmentsuspended'] = 'Inscripció suspesa';
$string['errorgroupsnotselected'] = 'Seleccioneu un o més grups';
$string['errorinaddingsession'] = 'S\'ha produït un error en afegir la sessió';
$string['erroringeneratingsessions'] = 'S\'ha produït un error en generar la sessió';
$string['gradebookexplanation'] = 'Nota al llibre de qualificacions';
//$string['gradebookexplanation_help'] = 'El mòdul AttControl mostra una qualificació basada en el nombre de punts acumulats fins a la data actual sobre el total dels que s\'hauria pogut obtenir; no inclou sessions futures. Al llibre de qualificacionsgradebook, your attcontrol grade is based on your current attcontrol percentage and the number of points that can be earned over the entire duration of the course, including future class periods. As such, your attcontrol grades displayed in the attcontrol module and in the gradebook may not be the same number of points but they are the same percentage.
//
//For example, if you have earned 8 of 10 points to date (80% attcontrol) and attendance for the entire course is worth 50 points, the attcontrol module will display 8/10 and the gradebook will display 40/50. You have not yet earned 40 points but 40 is the equivalent point value to your current attcontrol percentage of 80%. The point value you have earned in the attcontrol module can never decrease, as it is based only on attcontrol to date; however, the attcontrol point value shown in the gradebook may increase or decrease depending on your future attcontrol, as it is based on attendance for the entire course.';
$string['gridcolumns'] = 'Columnes de la graella';
$string['groupsession'] = 'Grup';
$string['identifyby'] = 'Identificar estudiant per';
$string['includeall'] = 'Selecciona totes les sessions';
$string['includenottaken'] = 'Inclou les sessions no passades';
$string['indetail'] = 'En detall...';
$string['jumpto'] = 'Vés a';
$string['modulename'] = 'AttControl';
$string['modulename_help'] = 'El mòdul d\'activitat d\'AttControl permet al professor controlar l\'assistència de classe i als estudiants veure el seu propi registre.

Un professor pot crear diverses sessions i marcar l\'assistència amb diversos estats d\'assistència que poden ser adaptats a les necessitats de cada centre.

Es poden generar informes individuals i grupals.';
$string['modulenameplural'] = 'attcontrols';
$string['months'] = 'Mesos';
$string['myvariables'] = 'Les meves variables';
$string['newdate'] = 'Data nova';
$string['newduration'] = 'Durada nova';
$string['noattforuser'] = 'No hi ha cap registre d\'assistència per a aquest usuari';
$string['nodescription'] = 'Sessió de classe normal';
$string['noguest'] = 'Els convidats no poden veure AttControl';
$string['nogroups'] = 'No podeu afegir sessions de grup, ja que no hi ha grups en aquest curs.';
$string['noofdaysabsent'] = '# dies absent';
$string['noofdaysexcused'] = '# dies excusat';
$string['noofdayslate'] = '# dies tard';
$string['noofdayspresent'] = '# dies present';
$string['nosessiondayselected'] = 'No heu seleccionat un dia de sessió';
$string['nosessionexists'] = 'No hi ha sessions per a aquest curs';
$string['nosessionsselected'] = 'No heu seleccionat cap sessió';
$string['notfound'] = 'No hi ha cap activitat AttControl en aquest curs.';
$string['noupgradefromthisversion'] = 'The attcontrol module cannot upgrade from the version of attforblock you have installed. - please delete attforblock or upgrade it to the latest version before isntalling the new attcontrol module';
$string['olddate'] = 'Data anterior';
$string['period'] = 'Frequència';
$string['pluginname'] = 'AttControl';
$string['pluginadministration'] = 'Administració d\'AttControl';
$string['remarks'] = 'Comentaris';
$string['report'] = 'Informe';
$string['coursereport'] = 'Informe de curs';
$string['courseexport'] = 'Exporta l\'informe de curs';
$string['individualreport'] = 'Informe individual';
$string['individualexport'] = 'Exporta l\'informe individual';
$string['resetdescription'] = 'Recordeu que quan esborreu la sessió, s\'esborrarà tota la informació associada a aquesta.';
$string['resetstatuses'] = 'Restaura els estats al valor per defecte';
$string['restoredefaults'] = 'Restaura valors per defecte';
$string['save'] = 'Guarda assistència';
$string['session'] = 'Sessió';
$string['session_help'] = 'Sessió';
$string['sessionadded'] = 'Sessió afegida correctament';
$string['sessionalreadyexists'] = 'Ja existeix una sessió per a aquesta data';
$string['sessiondate'] = 'Data de sessió';
$string['sessiondays'] = 'Dies de sessió';
$string['sessiondeleted'] = 'Sessió esborrada correctament';
$string['sessionenddate'] = 'Data de final de sessió';
$string['sessionexist'] = 'No s\'ha pogut afegir la sessió (ja existeix)!';
$string['sessionlist'] = 'Llistat de sessions';
$string['sessions'] = 'Sessions';
$string['sessionscompleted'] = 'Sessions completes';
$string['sessionsids'] = 'IDs de sessió: ';
$string['sessionsgenerated'] = 'Les sessions s\'han generat correctament';
$string['sessionsnotfound'] = 'No hi ha sessions per a les dates seleccionades';
$string['sessionstartdate'] = 'Data d\'inici de sessió';
$string['sessiontype'] = 'Tipus de sessió';
$string['sessiontype_help'] = 'Hi ha dos tipus de sessions: comunes i de grup. El tipus de sessions a afegir depèn del tipus de mòdul d\'activitat que s\'hagi afegit.

* Al mode "sense grups" només es poden crear sessions comunes.
* Al mode "grups visibles" es poden crear tant sessions comunes com de grup.
* Al mode "grups separats" només es poden crear sessions de grup.
';
$string['sessiontypeshort'] = 'Tipus';
$string['sessionupdated'] = 'La sessió s\'ha actualitzat correctament';
$string['setallstatusesto'] = 'Posa l\'estat «{$a}» a tots els estudiants';
$string['settings'] = 'Preferències';
$string['showdefaults'] = 'Mostra per defecte';
$string['showduration'] = 'Mostra la durada';
$string['sortedgrid'] = 'Graella ordenada';
$string['sortedlist'] = 'Llista ordenada';
$string['startofperiod'] = 'Inici del període';
$string['status'] = 'Estat';
$string['statuses'] = 'Estats';
$string['statusdeleted'] = 'Estat esborrat';
$string['strftimedm'] = '%d.%m';
$string['strftimedmy'] = '%d/%m/%Y';
$string['strftimedmyhm'] = '%d/%m/%Y %H:%M'; // Line added to allow multiple sessions in the same day.
$string['strftimedmyw'] = '%d/%m/%y&nbsp;(%a)';
$string['strftimehm'] = '%H:%M'; // Line added to allow display of time.
$string['strftimeshortdate'] = '%d.%m.%Y';
$string['studentid'] = 'ID d\'estudiant';
$string['takeattcontrol'] = 'Passar llista';
$string['thiscourse'] = 'Aquest curs';
$string['tablerenamefailed'] = 'No s\'ha pogut canviar el nom de la taula attforblock a attcontrol';
$string['update'] = 'Actualitza';
$string['variable'] = 'variable';
$string['variablesupdated'] = 'S\'han actualitzat les variables correctament';
$string['versionforprinting'] = 'versió d\'impressió';
$string['viewmode'] = 'Mode de visualització';
$string['week'] = 'setmana(es)';
$string['weeks'] = 'Setmanes';
$string['youcantdo'] = 'You can\'t do anything';



$string['status1'] = "Estat 1";
$string['configstatus1'] = "Primer estat d'assistència (opció per defecte).";
$string['statusdesc1'] = "Descripció de l'estat 1";
$string['configstatusdesc1'] = "Descripció del primer estat d'assistència (opció per defecte).";

$string['status2'] = "Estat 2";
$string['configstatus2'] = "Segon estat d'assistència.";
$string['statusdesc2'] = "Descripció de l'estat 2";
$string['configstatusdesc2'] = "Descripció del segon estat d'assistència.";

$string['status3'] = "Estat 3";
$string['configstatus3'] = "Tercer estat d'assistència.";
$string['statusdesc3'] = "Descripció de l'estat 3";
$string['configstatusdesc3'] = "Descripció del tercer estat d'assistència.";

$string['status4'] = "Estat 4";
$string['configstatus4'] = "Quart estat d'assistència.";
$string['statusdesc4'] = "Descripció de l'estat 4";
$string['configstatusdesc4'] = "Descripció del quart estat d'assistència.";

$string['status5'] = "Estat 5";
$string['configstatus5'] = "Cinquè estat d'assistència.";
$string['statusdesc5'] = "Descripció de l'estat 5";
$string['configstatusdesc5'] = "Descripció del cinquè estat d'assistència.";

$string['status6'] = "Estat 6";
$string['configstatus6'] = "Sisè estat d'assistència.";
$string['statusdesc6'] = "Descripció de l'estat 6";
$string['configstatusdesc6'] = "Descripció del sisè estat d'assistència.";

$string['status7'] = "Estat 7";
$string['configstatus7'] = "Setè estat d'assistència.";
$string['statusdesc7'] = "Descripció de l'estat 7";
$string['configstatusdesc7'] = "Descripció del setè estat d'assistència.";


$string['status8'] = "Estat 8";
$string['configstatus8'] = "Vuitè estat d'assistència.";
$string['statusdesc8'] = "Descripció de l'estat 8";
$string['configstatusdesc8'] = "Descripció del vuitè estat d'assistència.";



$string['courserelations'] = "Relacions entre cursos";
$string['addrelation'] = ">";
$string['removerelation'] = "<";
$string['pagination_options'] = "Opcions de paginació";
$string['pagination_perpage'] = "Ítems per pàgina";


$string['savepreferences'] = 'Guarda les preferències';
$string['export'] = 'Exporta';

$string['student'] = 'Estudiant';
$string['studentselect'] = 'Seleccioneu un estudiant';
$string['date'] = 'Data';
$string['time'] = 'Hora';
$string['setforallstudents'] = 'Posa-ho a tots els estudiants';

$string['remark'] = 'Comentari';

$string['nostudentselected'] = "Seleccioneu un estudiant per a veure el seu informe individual.";

$string['course'] = 'Curs';
$string['attendanceitem'] = 'Ítem d\'assistència';
$string['attendancecontrol'] = 'Control d\'assistència';