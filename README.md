# MODX Revolution Extension/Formstack Integration 

Using: https://github.com/jgulledge19/FormStackAPI

## System Settings

* formstack.access_token

## Template Variables 

* formstackFormID ~ Listbox (Single-Select) Input @EVAL return $modx->runSnippet('listFormstackForms',array('itemSeparator' => '||','item'=>'formstackTVItem'));
Will list all available forms based on permissions of the accessToken
* formstackFormField ~ Listbox (Single-Select) Input @EVAL return $modx->runSnippet('listFormstackFormColumns',array());
Will list all form fields of the selected form. If empty select a Formstack form and save and refresh to see fields.
* formstackHandshake ~ Text Fill in a matching Secret Key for the related Formstack Form if using a Webhook back to MODX

## Snippets

### getFormstackFormDetails 
Get any/all details of a Formstack form and send to placeholders and optionally return/echo as string 

**Properties**

* _formID_ INT a valid Formstack from ID
* _return_ A detail to return, example &return=`name` would just return the form name 
* _cacheLife_ INT the length of time to cache the details in seconds, default is 3600, one hour
* _prefix_ The prefix for the placeholders, default is form
Available placeholders where the snippet is called, if you set the prefix to a different value use it rather than form
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


### listFormstackFormColumns 
Will get a Formstack form and iterate through its fields and set field data as placeholders to be used in a Chunk. 

**Properties**

* _formID_ INT a valid Formstack from ID 
* _item_ this is the Chunk that will be iterated through, default is formstackTVFieldItem
* _itemSeparator_ ~ default is ||, this is how the Snippet will explode the data on
* _hide_ Comma separated Field types to hide from iteration, default is section
* _useParts_ bool if 1 then Formstack form fields like Name and Address will be separated out, so Name (first) and Name (last). 
If 0 then the fields will be treated as one. 
* _cacheLife_ INT the length of time to cache the details in seconds, default is 3600, one hour
 
Available placeholder in the item Chunk
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
 
 
### listFormstackForms
Snippet will list all Formstack forms that that application access token has access to view. Snippet will then 
iterate through the forms and set form data as placeholders to be used in a Chunk. 

**Properties**

* _item_ this is the Chunk that will be iterated through, default is formstackItem
* _itemSeparator_ ~ default is ||, this is how the Snippet will explode the data on
* _cacheLife_ INT the length of time to cache the details in seconds, default is 3600, one hour

Available placeholder in the item Chunk
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


### processFormstackWebhook
The snippet will do the initial processing and security screening a Formstack webhook. Then you need to write a snippet
to do some actual action that will be called very similarly like a FormIt hook. Example usage would be to place snippet 
call not cached at the top of your Template, like so:  
```[[!processFormstackWebhook? &hooks=`addFormstackToDataExtensionHook`]]<!DOCTYPE html>```

**Properties**

* _formID_ INT a valid Formstack from ID 
* _hooks_ A comma separated list of MODX Snippets that can take the Formstack form data and do something with it.
All hooks will be passed the following 
    * _formData_ an associated array (form field => input value)
    * _formID_
* _handshake_ This is the matching Secret Key for the related Formstack Form. If you have formstackHandshake TV set for the 
current resource then you don't need to set it.
* _debug_ Boolean, if 1 then will write debug messages to MODX Error log

 
