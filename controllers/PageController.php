<?php
class CheckLinks_PageController extends Omeka_Controller_AbstractActionController
{
    public function checklinksAction($type = 'Item')
  	{
    	$db = get_db();
 			$recheck = $this->getParam('recheck');
    	$content = "<a class='add button small green' href='" . WEB_ROOT . "/admin/checklinks?recheck=1'>" . __('Re-check') . "</a>";
    	if ($recheck) {
      	set_time_limit(0);
      	$db = get_db();
      	$db->query("DELETE FROM `$db->CheckLinks`");
/*
        $query = "SELECT id, text, record_type, record_id FROM `$db->ElementTexts` ORDER BY record_type, id";
      	$texts = $db->query($query)->fetchAll();
*/
        $texts = [];
      	$query = "SELECT id, text, 'Simple-Page' record_type, id record_id FROM `$db->SimplePagesPages` ORDER BY id";
      	$ss = $db->query($query)->fetchAll();

      	$query = "SELECT id, description text, 'Exhibit' record_type, id record_id FROM `$db->Exhibits` ORDER BY id";
      	$exhibits = $db->query($query)->fetchAll();

      	$query = "SELECT id, text text, 'Exhibit-Block' record_type, page_id record_id FROM `$db->ExhibitPageBlocks` ORDER BY id";
      	$exhibitBlocks = $db->query($query)->fetchAll();

      	$query = "SELECT id, caption text, 'Exhibit-Block-Attachment' record_type, block_id record_id FROM `$db->ExhibitBlockAttachments` ORDER BY id";
      	$exhibitBlocksAttachments = $db->query($query)->fetchAll();

      	$texts = array_merge($ss, $texts, $exhibits, $exhibitBlocks, $exhibitBlocksAttachments);

      	$nbLinks = 0;
      	$nbLinksProblem = 0;

      	foreach ($texts as $i => $text) {
        	preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $text['text'], $matches);
        	foreach ($matches as $i => $match) {
          	if (isset($match[0]) && strlen($match[0]) > 1) {
            	$return_code = $this->checkUrl($match[0]);
               	if (! in_array($return_code, array(200, 301, 302, 303))) {
                  $nbLinksProblem++;
                 	$class = 'rc-' . $return_code;
                 	$link = $match[0];
                  $type = strtolower($text['record_type']);
                  switch ($type) {
                    case 'item' :
                    case 'collection' :
                    case 'file' :
                      $urlEditPart = 'edit/';
                      break;
                    case 'simple-page' :
                      $urlEditPart = 'index/edit/id/';
                      break;
                    case 'exhibit' :
                      $urlEditPart = 'edit/';
                      break;
                    case 'exhibit-block' :
                    case 'exhibit-block-attachment' :
                      $type = 'exhibit';
                      $urlEditPart = 'edit-page/';
                      break;
                  }
                  $editLink = $db->quote(WEB_ROOT . "/admin/" . $type . "s/" . $urlEditPart . $text['record_id']);
                  $query = "INSERT INTO `$db->CheckLinks` VALUES (null, '$link', '$return_code', " . $db->quote($text['record_type']) . ", $editLink)";
                  $db->query($query);
                }
            	$nbLinks++;
          	}
        	}
      	}
      	$time = round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2);
        $content .= "<br /><br /><br /><div>$nbLinks liens vérifiés.</div>";
        $content .= "<div>$nbLinksProblem liens problématique(s).</div>";
        $content .= "<div>Temps d'exécution : $time secondes</div><br /><br />";
    	}
    	$content .= "<table><tr><td class='cl-title'>Lien</td><td class='cl-title'>Code</td><td class='cl-title'>Type</td><td class='cl-title'>Édition</td></tr>";
      $query = "SELECT * FROM `$db->CheckLinks` ORDER BY id";
      $links = $db->query($query)->fetchAll();
      foreach ($links as $id => $link) {
        $content .= "<tr><td>" . $link['link'] . "</td><td>" . $link['code'] . "</td><td>" . $link['type'] . "</td><td><a target='_blank' href='" . $link['editlink'] . "'>Editer</a></td></tr>" ;
      }
      $content .= "</table>";
    	$this->view->content = $content;
    }

  	private function prettifyForm($form) {
  		// Prettify form
  		$form->setDecorators(array(
  				'FormElements',
  				array('HtmlTag', array('tag' => 'table')),
  				'Form'
  		));
  		$form->setElementDecorators(array(
  				'ViewHelper',
  				'Errors',
  				array(array('data' => 'HtmlTag'), array('tag' => 'td')),
  				array('Label', array('tag' => 'td', 'style' => 'text-align:right;float:right;')),
  				array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
  		));
  		return $form;
  	}

  	private function checkUrl($url) {
     	$headers = @get_headers($url, 1);
     	if (! $headers) {
       	return "URL invalide";
     	}
     	return substr($headers[0], 9, 3);
  	}
}

