<?php
class Zend_View_Helper_FormattedYear extends Zend_View_Helper_Abstract
{
	public function formattedYear($date=null)
	{
		if ($date != null)
		{
			$locale = new Zend_Locale('de_DE');
			Zend_Date::setOptions(array('format_type' => 'php'));
			$date = new Zend_Date($date, false, $locale);

			return $date->toString('Y');
		}		
	}

}

		