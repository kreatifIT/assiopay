<?php

$form = rex_config_form::factory('assiopay');
echo rex_view::title($this->getProperty('page')['title']);
$field = $form->addSelectField('assiopay_use_sandbox', null, ["class" => "form-control"]);
$select = new rex_select();
$select->addOptions([1 => 'Yes', 0 => 'No']);
$field->setSelect($select);
$field->setLabel('Use Sandbox Mode');

$form->addFieldset('Sandbox Credentials');

$field = $form->addTextField('assiopay_sandbox_endpoint', null, ["class" => "form-control"]);
$field->setLabel('Endpoint-URL');

$field = $form->addTextField('assiopay_sandbox_mail', null, ["class" => "form-control"]);
$field->setLabel('Email');

$field = $form->addTextField('assiopay_sandbox_password', null, ["class" => "form-control"]);
$field->setLabel('Password');

$form->addFieldset('Live Credentials');
$field = $form->addTextField('assiopay_live_endpoint', null, ["class" => "form-control"]);
$field->setLabel('Endpoint-URL');
$field = $form->addTextField('assiopay_live_mail', null, ["class" => "form-control"]);
$field->setLabel('Email');

$field = $form->addTextField('assiopay_live_password', null, ["class" => "form-control"]);
$field->setLabel('Password');

$formOutput = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit kga-panel', false);
$fragment->setVar('body', $formOutput, false);
echo $fragment->parse('core/page/section.php');
