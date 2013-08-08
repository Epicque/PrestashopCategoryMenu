<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class EpicMenuTop extends Module
{
	public function __construct()
	{
		$this->name = 'EpicMenuTop';
		$this->tab = 'front_office_features';
		$this->version = '0.1';
		$this->author = 'Edouard Picque';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Epic Menu Top');
		$this->description = $this->l('A simple, clean top category menu for prestashop');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall this awesome module?');
	}

	// Add the hooks functions
	public function install()
	{
		return parent::install() && $this->registerHook('DisplayTop') && $this->registerHook('DisplayHeader') &&  $this->registerHook('DisplayFooter');
	}

	public function uninstall()
	{
	  return parent::uninstall();
	}
	
	/* Used to get first level Categories */ 
	public function getCategories(){
		/* load less infos than Category controller */
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$sql = 'SELECT c.id_category, cl.name, cl.description 
				FROM `'._DB_PREFIX_.'category` c
				INNER JOIN `'._DB_PREFIX_.'category_lang` cl
				ON (c.`id_category` = cl.`id_category`)
				WHERE cl.`id_lang` = '.(int)$id_lang.' AND c.`id_parent` BETWEEN 1 AND 2 AND c.active=1';

		return Db::getInstance()->executeS($sql);
	}

	/* Used to get the children */
	public function getMenuChildren($id_category)
	{
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$sql = 'SELECT c.id_category, cl.name, cl.description 
				FROM `'._DB_PREFIX_.'category` c
				INNER JOIN `'._DB_PREFIX_.'category_lang` cl
				ON (c.`id_category` = cl.`id_category`)
				WHERE cl.`id_lang` = '.(int)$id_lang.' AND c.`id_parent`='.$id_category.' AND c.active=1';

		return Db::getInstance()->executeS($sql);
	}

	/* Used to display the Menu Categories */
	public function displayMenuCategories()
	{
		$html_menu = "";
		$categoriesM = $this->getCategories();

		foreach ($categoriesM as $category) {
			$link = new Link(null);
			if($category['id_category']==$_GET['id_category'])
				$class_open ="class='menu-top-open'";
			else
				$class_open = '';
			/* Different display if there's a subcategory */
           	if($subCat=$this->getMenuChildren($category['id_category']))
           	{
           		$html_menu.="
				<li $class_open>
            	<a href='#'>".$category['name']."</a>
	            	<div class='menu-top-sub'>
	                	<div class='inner-menu'>";
		           		foreach ($subCat as $subCategory) {
		           		    $html_menu.="
				                <h4><a href='".$link->getCategoryLink($subCategory['id_category'])."'>".$subCategory['name']."</a></h4>
				                ";
				                /* Add a sub sub category if there's one */
				                if($subSubCat=$this->getMenuChildren($subCategory['id_category'])){
				                	$html_menu.= "<ul>";
					                  	foreach ($subSubCat as $subSubCategory) {
					                  			$html_menu.="
							                        <li><a href='".$link->getCategoryLink($subSubCategory['id_category'])."'>".$subSubCategory['name']."</a></li>";
					                  		}
				               		$html_menu.="</ul>";
				                }
		              	}
	              	$html_menu.="
	              		</div>
					</div>
				</li>
				";
           	}
           	else
            	$html_menu.="
				<li $class_open>
            		<a href='".$link->getCategoryLink($category['id_category'])."'>".$category['name']."</a>
            	</li>";
		}

		return $html_menu;
	}
	/* The menu hook function */
	public function hookDisplayTop($param)
	{
			$this->context->smarty->assign(
			array(
					'categories' => $this->displayMenuCategories()
				)
			);
		return $this->display(__FILE__, 'EpicMenuTop.tpl');
	}
	/* Add some CSS and JS */
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'css/EpicMenuTop.css', 'all');
	}
	public function hookDisplayFooter()
	{
			$this->context->smarty->assign(
			array(
				'JSlink' => $this->_path.'js/EpicMenu.js'
				)
			);
			return $this->display(__FILE__, 'scripts.tpl');
	}
/* End of Class */ 
}