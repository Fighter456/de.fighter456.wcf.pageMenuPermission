<?php
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows, validates, saves and reads the permissions for page menu items.
 * 
 * @author 	Dennis Kraffczyk
 * @copyright 	Dennis Kraffczyk 2011
 * @website 	http://www.support.thomasmania.de/forum/?page=ProductOverview&package=de.fighter456.wcf.pageMenuPermission
 * @package 	de.fighter456.wcf.pageMenuPermission
 * @subpackage 	com.woltlab.wcf.acp.display.pageMenu
 * @licence 	Creative Commons <http://creativecommons.org/licenses/by-nd/3.0/>
 */
class PageMenuItemAddFormPermissionListener implements EventListener {
	
	/**
	 * event object
	 * @var PageMenuItemAdd|EditForm
	 */
	public $eventObj = null;
	
	/**
	 * selected permissions
	 * @var	Array<String>
	 */
	public $selectedPermissions = array();
	
	/**
	 * list of permissions
	 */
	public static $availablePermissions = array();
	
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$this->eventObj = $eventObj;

		switch ($eventName) {
			case 'readData':
				$this->readData($className == 'PageMenuItemAddForm' ? false : true);
				break;
			case 'readFormParameters':
				$this->readFormParameters();
				break;
			case 'saved':
				$this->saved();
				break;
			case 'assignVariables':
				$this->assignVariables();
				break;
		}
	}
	
	/**
	 * reads the permissions from object data and loads the cache
	 */
	protected function readData($isEdit) {
		// load cache
		self::loadCache();
		
		if (!count($_POST) && $isEdit) {
			$this->selectedPermissions = explode(',', $this->eventObj->pageMenuItem->permissions);	
		}	
	}
	
	/**
	 * reads the parameters the user entered in the form
	 */
	protected function readFormParameters() {
		if (isset($_POST['permissions']) && is_array($_POST['permissions'])) $this->selectedPermissions = ArrayUtil::trim($_POST['permissions']);	
	}
	
	/**
	 * saves the selected permissions
	 */
	protected function saved() {
		$sql = "UPDATE 	wcf".WCF_N."_page_menu_item
			SET	permissions = '".escapeString(implode(',', $this->selectedPermissions))."'
			WHERE	menuItemID = ".$this->eventObj->pageMenuItem->menuItemID;
		WCF::getDB()->sendQuery($sql);	
		
		// clear cache
		PageMenuItemEditor::clearCache();
	}
	
	/**
	 * assigns needed variables for template
	 */
	protected function assignVariables() {
		WCF::getTPL()->assign(array(
			'selectedPermissions' => $this->selectedPermissions,
			'permissions' => self::$availablePermissions
		));
		WCF::getTPL()->append('additionalFields', WCF::getTPL()->fetch('pageMenuPermissionSelection'));	
	}
	
	/**
	 * loads the group option cache
	 */
	protected static function loadCache() {
		// cache already loaded
		if (count(self::$availablePermissions)) return;
		
		// build cache
		$groupIDs = array(Group::EVERYONE, Group::GUESTS, Group::OTHER, Group::USERS);
		$groupsFileName = StringUtil::getHash(implode("-", $groupIDs));
		WCF::getCache()->addResource('groups-'.PACKAGE_ID.'-'.implode(',', $groupIDs), WCF_DIR.'cache/cache.groups-'.PACKAGE_ID.'-'.$groupsFileName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderGroupPermissions.class.php');
		
		// get cached options
		$groupOptions = WCF::getCache()->get('groups-'.PACKAGE_ID.'-'.implode(',', $groupIDs));
		
		// filter options
		$options = array();
		foreach ($groupOptions as $key => $value) {
			list($category,) = explode('.', $key);
			
		        // only boolean options are allowed
			if (!is_bool($value) || $key == 'mod.board.isSuperMod') {
				continue;
			}
			else {
				$categoryName = WCF::getLanguage()->get('wcf.acp.group.option.category.'.$category);
				$options[$categoryName][$key] = WCF::getLanguage()->get('wcf.acp.group.option.'.$key);
			}
		}
		
		self::$availablePermissions = $options;
	}
}
?>