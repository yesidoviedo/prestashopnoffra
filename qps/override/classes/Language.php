<?php

class Language extends LanguageCore{

   public static function getFilesList($iso_from, $theme_from, $iso_to = false, $theme_to = false, $select = false, $check = false, $modules = false)
    {
        if (empty($iso_from)) {
            die(Tools::displayError());
        }

        $copy = ($iso_to && $theme_to) ? true : false;

        $lPath_from = _PS_TRANSLATIONS_DIR_.(string)$iso_from.'/';
        $tPath_from = _PS_ROOT_DIR_.'/themes/'.(string)$theme_from.'/';
        $pPath_from = _PS_ROOT_DIR_.'/themes/'.(string)$theme_from.'/pdf/';
        $mPath_from = _PS_MAIL_DIR_.(string)$iso_from.'/';

        if ($copy) {
            $lPath_to = _PS_TRANSLATIONS_DIR_.(string)$iso_to.'/';
            $tPath_to = _PS_ROOT_DIR_.'/themes/'.(string)$theme_to.'/';
            $pPath_to = _PS_ROOT_DIR_.'/themes/'.(string)$theme_to.'/pdf/';
            $mPath_to = _PS_MAIL_DIR_.(string)$iso_to.'/';
        }

        $lFiles = array('admin.php', 'errors.php', 'fields.php', 'pdf.php', 'tabs.php');

        // Added natives mails files
        $mFiles = array(
            'account.html', 'account.txt',
            'backoffice_order.html', 'backoffice_order.txt',
            'bankwire.html', 'bankwire.txt',
            'prepaid.html', 'prepaid.txt',
            'cheque.html', 'cheque.txt',
            'contact.html', 'contact.txt',
            'contact_form.html', 'contact_form.txt',
            'credit_slip.html', 'credit_slip.txt',
            'download_product.html', 'download_product.txt',
            'employee_password.html', 'employee_password.txt',
            'forward_msg.html', 'forward_msg.txt',
            'guest_to_customer.html', 'guest_to_customer.txt',
            'in_transit.html', 'in_transit.txt',
            'log_alert.html', 'log_alert.txt',
            'newsletter.html', 'newsletter.txt',
            'order_canceled.html', 'order_canceled.txt',
            'order_conf.html', 'order_conf.txt',
            'order_customer_comment.html', 'order_customer_comment.txt',
            'order_merchant_comment.html', 'order_merchant_comment.txt',
            'order_return_state.html', 'order_return_state.txt',
            'outofstock.html', 'outofstock.txt',
            'password.html', 'password.txt',
            'password_query.html', 'password_query.txt',
            'payment.html', 'payment.txt',
            'payment_error.html', 'payment_error.txt',
            'preparation.html', 'preparation.txt',
            'refund.html', 'refund.txt',
            'reply_msg.html', 'reply_msg.txt',
            'shipped.html', 'shipped.txt',
            'test.html', 'test.txt',
            'voucher.html', 'voucher.txt',
            'voucher_new.html', 'voucher_new.txt',
            'order_changed.html', 'order_changed.txt'
        );

        $number = -1;

        $files = array();
        $files_tr = array();
        $files_theme = array();
        $files_mail = array();
        $files_modules = array();

        // When a copy is made from a theme in specific language
        // to an other theme for the same language,
        // it's avoid to copy Translations, Mails files
        // and modules files which are not override by theme.
        if (!$copy || $iso_from != $iso_to) {
            // Translations files
            if (!$check || ($check && (string)$iso_from != 'en')) {
                foreach ($lFiles as $file) {
                    $files_tr[$lPath_from.$file] = ($copy ? $lPath_to.$file : ++$number);
                }
            }
            if ($select == 'tr') {
                return $files_tr;
            }
            $files = array_merge($files, $files_tr);

            // Mail files
            if (!$check || ($check && (string)$iso_from != 'en')) {
                $files_mail[$mPath_from.'lang.php'] = ($copy ? $mPath_to.'lang.php' : ++$number);
            }
            foreach ($mFiles as $file) {
                $files_mail[$mPath_from.$file] = ($copy ? $mPath_to.$file : ++$number);
            }
            if ($select == 'mail') {
                return $files_mail;
            }
            $files = array_merge($files, $files_mail);

            // Modules
            if ($modules) {
                $modList = Module::getModulesDirOnDisk();
                foreach ($modList as $mod) {
                    $modDir = _PS_MODULE_DIR_.$mod;
                    // Lang file
                    if (file_exists($modDir.'/translations/'.(string)$iso_from.'.php')) {
                        $files_modules[$modDir.'/translations/'.(string)$iso_from.'.php'] = ($copy ? $modDir.'/translations/'.(string)$iso_to.'.php' : ++$number);
                    } elseif (file_exists($modDir.'/'.(string)$iso_from.'.php')) {
                        $files_modules[$modDir.'/'.(string)$iso_from.'.php'] = ($copy ? $modDir.'/'.(string)$iso_to.'.php' : ++$number);
                    }
                    // Mails files
                    $modMailDirFrom = $modDir.'/mails/'.(string)$iso_from;
                    $modMailDirTo = $modDir.'/mails/'.(string)$iso_to;
                    if (file_exists($modMailDirFrom)) {
                        $dirFiles = scandir($modMailDirFrom);
                        foreach ($dirFiles as $file) {
                            if (file_exists($modMailDirFrom.'/'.$file) && $file != '.' && $file != '..' && $file != '.svn') {
                                $files_modules[$modMailDirFrom.'/'.$file] = ($copy ? $modMailDirTo.'/'.$file : ++$number);
                            }
                        }
                    }
                }
                if ($select == 'modules') {
                    return $files_modules;
                }
                $files = array_merge($files, $files_modules);
            }
        } elseif ($select == 'mail' || $select == 'tr') {
            return $files;
        }

        // Theme files
        if (!$check || ($check && (string)$iso_from != 'en')) {
            $files_theme[$tPath_from.'lang/'.(string)$iso_from.'.php'] = ($copy ? $tPath_to.'lang/'.(string)$iso_to.'.php' : ++$number);

            // Override for pdf files in the theme
            if (file_exists($pPath_from.'lang/'.(string)$iso_from.'.php')) {
                $files_theme[$pPath_from.'lang/'.(string)$iso_from.'.php'] = ($copy ? $pPath_to.'lang/'.(string)$iso_to.'.php' : ++$number);
            }

            $module_theme_files = (file_exists($tPath_from.'modules/') ? scandir($tPath_from.'modules/') : array());
            foreach ($module_theme_files as $module) {
                if ($module !== '.' && $module != '..' && $module !== '.svn' && file_exists($tPath_from.'modules/'.$module.'/translations/'.(string)$iso_from.'.php')) {
                    $files_theme[$tPath_from.'modules/'.$module.'/translations/'.(string)$iso_from.'.php'] = ($copy ? $tPath_to.'modules/'.$module.'/translations/'.(string)$iso_to.'.php' : ++$number);
                }
            }
        }
        if ($select == 'theme') {
            return $files_theme;
        }
        $files = array_merge($files, $files_theme);

        // Return
        return $files;
    }


}

?>