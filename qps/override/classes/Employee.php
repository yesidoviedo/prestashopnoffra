<?php 

class Employee extends EmployeeCore
{


public static function getSalesMan($active_only = true)
    {
        return Db::getInstance()->executeS('
			SELECT `id_employee`, `firstname`, `lastname`
			FROM `'._DB_PREFIX_.'employee`
			'.($active_only ? ' WHERE `active` = 1' : '').'
			AND id_profile = 4 
			ORDER BY `lastname` ASC
		');
    }

}

?>