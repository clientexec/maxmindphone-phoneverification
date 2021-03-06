<?php

require_once 'modules/admin/models/PhoneVerificationPlugin.php';
require_once 'plugins/phoneverification/maxmindphone/maxmind_lib/TelephoneVerification.php';

/**
* @package Plugins
*/
class PluginMaxmindphone extends PhoneVerificationPlugin
{
    var $supportedLanguages = array("English","Spanish","French","German","Japanese");

    function getVariables()
    {
        $variables = array(
            lang('Plugin Name')   => array(
                'type'          => 'hidden',
                'description'   => '',
                'value'         => lang('Maxmind Phone Verification'),
            ),
            lang('Enabled')       => array(
                'type'          => 'yesno',
                'description'   => lang("This setting will enable the Maxmind telephone verification plugin on signup for new customers. (Phone credits are bought separate from regular credit card fraud detection services)<br><a href=http://www.maxmind.com/app/telephone_buynow?rId=clientexec target='_blank'>http://www.maxmind.com/app/telephone_buynow</a>"),
                'value'         => '0',
            ),
            lang('MaxMind License Key')       => array(
                'type'          => 'text',
                'description'   => lang('Enter your MaxMind License Key here.<br>You can obtain a license at <br><a href=http://www.maxmind.com/app/ccv_buynow?rId=clientexec target="_blank">http://www.maxmind.com/app/ccv_buynow</a>'),
                'value'         => '',
            ),
            lang('Minimum Bill Amount to Trigger Telephone Verification')       => array(
                'type'          => 'text',
                'description'   => lang('If MaxMind Telephone Verification is enabled, only trigger the verification call if the total bill amount exceeds this amount'),
                'value'         => '0',
            ),
            lang('Minimum Fraud Score to Trigger Telephone Verification')       => array(
                'type'          => 'text',
                'description'   => lang('If MaxMind Telephone Verification and Fraud Control are enabled, only trigger the verification call if the fraud score exceeds this number.'),
                'value'         => '0',
            ),
        );
    
        return $variables;
    }

    function execute($verificationCode)
    {
        // this can take a while
        @set_time_limit(0);

        $tv = new TelephoneVerification;
        $h = array();
        $h["l"] = $this->settings->get('plugin_maxmindphone_MaxMind License Key');
        $h["phone"] = $this->phoneNumber;
        $h["verify_code"] = $verificationCode;
		
		// Langauge field is now ommited in later versions of the API.
        //$h["language"] = $this->language;

        $tv->isSecure = 0;
        $tv->timeout = 30;
        $tv->useDNS = 0;
        $tv->input($h);
        if (!$tv->query()) {
            $this->failureMessages[]= $this->user->lang("We're sorry but we are unable to contact your phone number.  Please verify that it is correct or try another number.");
            return;
        }

        $this->result = $tv->output();

        return $this->result;
    }
}
