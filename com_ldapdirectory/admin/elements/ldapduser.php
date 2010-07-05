<?php
class JElementldapduser extends JElement
{
   var   $_name = 'ldapduser';

   function fetchElement($name, $value, &$node, $control_name)
   {
      $database =& JFactory::getDBO();
      
       $query = "SELECT * FROM #__users";
       $database->setQuery( $query );
      
       $items = $database->loadObjectList();
   
       $mitems     = array();
   
       foreach ( $items as $item )
       {
           $mitems[] = JHTMLSelect::option( $item->id, $item->name );
       }
   
       return JHTML::_("select.genericlist", $mitems, $control_name.'['.$name.']', 'class=""inputbox"', 'value', 'text', $value, $control_name.$name );   
   }
}