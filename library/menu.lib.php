<?php 
/* -----------------------------------------------------
 * File name  : menu.class.php	
 * Created by : aryonp@gmail.com
 * -----------------------------------------------------
 * Purpose	  : Generate menu using iteration and lock it
 * according user's permission.				   						                 			
 * -----------------------------------------------------
 */

// Select all entries from the menu table
$result=mysql_query("SELECT id, label, link, parent FROM menu ORDER BY parent, sort, label");
// Create a multidimensional array to conatin a list of items and parents
$menuData = array(
    'items' => array(),
    'parents' => array()
);
// Builds the array lists with data from the menu table
while ($menuItem = mysql_fetch_assoc($result))
{
    // Creates entry into items array with current menu item id ie. $menuData['items'][1]
    $menuData['items'][$menuItem['id']] = $menuItem;
    // Creates entry into parents array. Parents array contains a list of all items with children
    $menuData['parents'][$menuItem['parent']][] = $menuItem['id'];
}

// Menu builder function, parentId 0 is the root
function buildMenu($parent, $menuData)
{
   $html = "";
   if (isset($menuData['parents'][$parent]))
   {
      $html .= "
      <ul>\n";
       foreach ($menuData['parents'][$parent] as $itemId)
       {
          if(!isset($menuData['parents'][$itemId]))
          {
             $html .= "<li>\n&nbsp; <a href='".$menuData['items'][$itemId]['link']."'>".$menuData['items'][$itemId]['label']."</a>\n</li> \n";
          }
          if(isset($menuData['parents'][$itemId]))
          {
             $html .= "
             <li>\n&nbsp; <a href='".$menuData['items'][$itemId]['link']."'>".$menuData['items'][$itemId]['label']."</a> \n";
             $html .= buildMenu($itemId, $menuData);
             $html .= "</li> \n";
          }
       }
       $html .= "</ul> \n";
   }
   return $html;
}
echo buildMenu(0, $menuData);

?>