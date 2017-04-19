<?php
ini_set('display_errors', 1);
/**
 * @var string $item_chunk ~ item this is the Chunk the will be iterated throught
 * Available placeholder in item chunk:
 * [[+count]]
 * [[+fieldID]]
 * [[+label]]
 * [[+hide_label]]
 * [[+description]]
 * [[+name]]
 * [[+type]]
 * [[+options]] - for select this is array and placeholders won't iterate
 * [[+required]]
 * [[+uniq]]
 * [[+hidden]]
 * [[+readonly]]
 * [[+colspan]]
 * [[+sort]]
 * [[+logic]]
 * [[+calculation]]
 * [[+default]]
 * [[+text_size]]
 * [[+maxlength]]
 * [[+placeholder]]
 */
$item_chunk = $modx->getOption('item', $scriptProperties, 'formstackTVFieldItem');

/** @var int $form_id ~ a valid Formstack from ID  */
$form_id = $modx->getOption('formID', $scriptProperties, 0);

if(is_object($modx->resource) && $form_id == 0) {
    $form_id = $modx->resource->getTVValue('formstackFormID');
}

/** @var bool $use_parts ~ use parts or all as one field */
$use_parts = $modx->getOption('useParts', $scriptProperties, true);

/** @var string $hide comma separated Field types to hide, default: section */
$hide = explode(',',$modx->getOption('hide', $scriptProperties, 'section'));

/** @var string $item_separator to use with a TV set || */
$item_separator = $modx->getOption('itemSeparator', $scriptProperties, "||");

/** @var int $cache_life ~ in seconds */
$cache_life = $modx->getOption('cacheLife', $scriptProperties, 3600);

if (empty($form_id)) {
    return 'A Formstack Form ID has not been passed';
}

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

// cache:
$details = $modx->cacheManager->get('formstack-details-'.$form_id);
if (!$details ) {
    /** @var \JGulledge\FormStack\API\FormStack $formStack */
    $formStack = new \JGulledge\FormStack\API\FormStack($config);

    /** @param \JGulledge\FormStack\API\Forms */
    $myForm = $formStack->loadForm($form_id);
    //$myForm->setDebug();
    $details = $myForm->getDetails();

    $modx->cacheManager->set('formstack-details-'.$form_id, $details, $cache_life);
}

/**
 * @param $modx
 * @param $item_chunk
 * @param $column
 * @param $count
 *
 * @return mixed
 */
function makeItem($modx, $item_chunk, $column, $count) {
    $column['fieldID'] = $column['id'];
    $column['count'] = $count;
    unset($column['id']);

    return $modx->getChunk($item_chunk, $column);
}

if ( is_array($details) ) {
    $count = 0;
    foreach ($details['fields'] as $column) {
        if ( in_array($column['type'], $hide)) {
            continue;
        }
        if ( $use_parts) {
            switch ($column['type']) {
                case 'address':
                    $parts = [
                        "address",
                        "address2",
                        "city",
                        "state",
                        "zip"
                    ];
                    break;
                case 'name':
                    $parts = [
                        'first',
                        'last'
                    ];
                    break;
                default:
                    $parts = [0];
            }

            foreach ($parts as $part) {
                if ($count > 0) {
                    $output .= $item_separator;
                }
                $column['part'] = ($part ? $part : '');
                $output .= makeItem($modx, $item_chunk, $column, ++$count);
            }
        } else {
            if ($count > 0) {
                $output .= $item_separator;
            }
            $column['part'] = '';
            $output .= makeItem($modx, $item_chunk, $column, ++$count);
        }

    }
}
return $output;
