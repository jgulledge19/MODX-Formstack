<?php
/**
 *
 */

/** @var string $hooks ~ comma separated list of valid MODX Snippets  */
$hooks = $modx->getOption('hooks', $scriptProperties, false);

/** @var int $form_id ~ a valid Formstack from ID  */
$form_id = $modx->getOption('formID', $scriptProperties, 0);

/** @var string $handshake ~ a valid Formstack Secret Handshake, set in FS Settings  */
$handshake = $modx->getOption('handshake', $scriptProperties, 1);

/** @var bool $debug ~ will write to error log  */
$debug = $modx->getOption('debug', $scriptProperties, false);

if(is_object($modx->resource)) {
    if ($form_id == 0) {
        $form_id = $modx->resource->getTVValue('formstackFormID');
    }
    if ( $handshake === 1) {
        $handshake = $modx->resource->getTVValue('formstackHandshake');
        if (empty($handshake)) {
            $handshake = 1;
        }
    }
}
// This is a customer Formstack header, has it been set?
if (isset($_SERVER['HTTP_X_FS_SIGNATURE'])) {
    $postdata = file_get_contents("php://input");
    list ($method, $signature) = explode('=', $_SERVER['HTTP_X_FS_SIGNATURE'], 2);

    if ($handshake && hash_hmac($method, $postdata, $handshake) === $signature) {
        // Verified that it came from Formstack

        if ($debug) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[processFormstackWebhook] INPUT: ' . PHP_EOL . $postdata);
        }

        if ($hooks) {
            $snippets = explode(',', $hooks);

            foreach ($snippets as $snippet) {
                if ($debug) {
                    $modx->log(modX::LOG_LEVEL_ERROR, '[processFormstackWebhook]runSnippet: ' . $snippet);
                }

                $modx->runSnippet(
                    $snippet,
                    [
                        'formData' => json_decode($postdata, true),
                        'formID' => $form_id,
                    ]
                );
            }
        }
    }elseif ($debug) {
        $modx->log(modX::LOG_LEVEL_ERROR, '[processFormstackWebhook] Handshake did not match');
    }
} else {
    if ($debug) {
        $modx->log(modX::LOG_LEVEL_ERROR, '[processFormstackWebhook] Did not run, FS header not set');
    }
}
return '';