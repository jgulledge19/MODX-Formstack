<?php
ini_set('display_errors', 1);
/** @var int $form_id ~ a valid Formstack from ID  */
$form_id = $modx->getOption('formID', $scriptProperties, $modx->getOption('formId', $scriptProperties, 0));

/** @var string $return ~ detail to return */
$return = $modx->getOption('return', $scriptProperties, null);

/** @var int $cache_life ~ in seconds */
$cache_life = $modx->getOption('cacheLife', $scriptProperties, 3600);

/** @var string $placeholder_prefix ~ defaults to form */
$placeholder_prefix = $modx->getOption('prefix', $scriptProperties, 'form');

/**
 * Set Available placeholders:
 * [[+form.created]]
 * [[+form.db]]
 * [[+form.deleted]]
 * [[+form.folder]]
 * [[+form.id]]
 * [[+form.language]]
 * [[+form.name]]
 * [[+form.submissions]]
 * [[+form.submissions_unread]]
 * [[+form.updated]]
 * [[+form.viewkey]]
 * [[+form.views]]
 * [[+form.submissions_today]]
 * [[+form.last_submission_id]]
 * [[+form.last_submission_time]]
 * [[+form.url]]
 * [[+form.url_path]]
 * [[+form.data_url]]
 * [[+form.summary_url]]
 * [[+form.rss_url]]
 * [[+form.encrypted]]
 * [[+form.thumbnail_url]]
 * [[+form.submit_button_title]]
 * [[+form.inactive]]
 * [[+form.timezone]]
 * [[+form.permissions]]
 * [[+form.javascript]]
 * [[+form.html]]
 * @TODO [[+field.name]]
 */

$corePath = $modx->getOption('formstack.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/formstack/');
require_once $corePath . 'model/vendor/autoload.php';

$details = $modx->cacheManager->get('formstack-details-'.$form_id);
if (!$details ) {
    /**
     * @param array $config ~ these are defined in config.php
     */
    $config = array(
        'access_token'  => $modx->getOption('accessToken', $scriptProperties, $modx->getOption('formstack.access_token', null, null))
    );
    /** @var \JGulledge\FormStack\API\FormStack $formStack */
    $formStack = new \JGulledge\FormStack\API\FormStack($config);

    /** @param \JGulledge\FormStack\API\Forms */
    $myForm = $formStack->loadForm($form_id);
    //$myForm->setDebug();
    $details = $myForm->getDetails();

    $modx->cacheManager->set('formstack-details-'.$form_id, $details, $cache_life);
}

unset($details['fields']);
// create a short name:
$details['url_path'] = ltrim(parse_url($details['url'], PHP_URL_PATH), '/forms/');

$modx->setPlaceholders($details, $placeholder_prefix);

$output = '';

if ( !empty($return) && isset($details[$return])) {
    $output = $details[$return];
}

return $output;