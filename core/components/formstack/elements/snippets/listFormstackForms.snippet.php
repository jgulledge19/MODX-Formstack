<?php
/**
 * @var string $item_chunk ~ item this is the Chunk the will be iterated throught
 * Available placeholder in item chunk:
 * [[+name]]
 * [[+formId]]
 * [[+count]]
 * [[+details.created]]
 * [[+details.db]]
 * [[+details.deleted]]
 * [[+details.folder]]
 * [[+details.id]]
 * [[+details.language]]
 * [[+details.name]]
 * [[+details.submissions]]
 * [[+details.submissions_unread]]
 * [[+details.updated]]
 * [[+details.viewkey]]
 * [[+details.views]]
 * [[+details.submissions_today]]
 * [[+details.last_submission_id]]
 * [[+details.last_submission_time]]
 * [[+details.url]]
 * [[+details.data_url]]
 * [[+details.summary_url]]
 * [[+details.rss_url]]
 * [[+details.encrypted]]
 * [[+details.thumbnail_url]]
 * [[+details.submit_button_title]]
 * [[+details.inactive]]
 * [[+details.timezone]]
 * [[+details.permissions]]
 * [[+details.javascript]]
 * [[+details.html]]
 * @TODO [[+field.name]]
 */
$item_chunk = $modx->getOption('item', $scriptProperties, 'formstackItem');

/** @var string $item_separator to use with a TV set || */
$item_separator = $modx->getOption('itemSeparator', $scriptProperties, "\n");

/** @var bool $show_folders ~ 1/0 */
$show_folders = $modx->getOption('showFolders', $scriptProperties, false);

/** @var int $cache_life ~ in seconds */
$cache_life = $modx->getOption('cacheLife', $scriptProperties, 3600);

$corePath = $modx->getOption('formstack.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/formstack/');
require_once $corePath . 'model/vendor/autoload.php';

$cache_key = 'formstack-forms-list';//.$item_chunk.'';

$output = '';
/**
 * @param array $config ~ these are defined in config.php
 */
$config = array(
    'access_token'  => $modx->getOption('accessToken', $scriptProperties, $modx->getOption('formstack.access_token', null, null))
);

$formStack = new \JGulledge\FormStack\API\FormStack($config);
// cache:
if(!$form_list = $modx->cacheManager->get($cache_key)) {
    $form_list = [];
    $forms = $formStack->getForms($show_folders);
    foreach ($forms as $name => $formObject) {
        if ( $count > 0) {
            $output .= $item_separator;
        }
        if (!$details = $modx->cacheManager->get('formstack-details-'.$formObject->getId())) {
            $details = $formObject->getDetails();
            $modx->cacheManager->set('formstack-details-'.$formObject->getId(), $details, $cache_life);
        }
        unset($details['fields']);

        $form_list[$formObject->getId()] =[
            'name' => $name,
            'formId' => $formObject->getId()
        ];
    }
    $modx->cacheManager->set($cache_key, $output, $cache_life);
}

if ( is_array($form_list) ) {
    $count = 0;
    foreach ($form_list as $id => $form) {
        if ( $count > 0) {
            $output .= $item_separator;
        }
        if (!$details = $modx->cacheManager->get('formstack-details-'.$id)) {
            $myForm = $formStack->loadForm($id);
            $details = $myForm->getDetails();
            $modx->cacheManager->set('formstack-details-'.$myForm->getId(), $details, $cache_life);
        }
        unset($details['fields']);

        $output .= $modx->getChunk($item_chunk,
            array(
                'name' => $form['name'],
                'formId' => $id,
                'details' => $details,
                'count' => ++$count
            )
        );
    }
}
return $output;
