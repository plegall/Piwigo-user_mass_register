<?php
if (!defined("PHPWG_ROOT_PATH"))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

// +-----------------------------------------------------------------------+
// | Functions                                                             |
// +-----------------------------------------------------------------------+

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

load_language('plugin.lang', UMR_PATH);

// +-----------------------------------------------------------------------+
// | Tabs                                                                  |
// +-----------------------------------------------------------------------+

$page['tab'] = 'home';

// tabsheet
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('umr');

$tabsheet->add('home', l10n('User Mass Register'), UMR_ADMIN . '-home');
$tabsheet->select($page['tab']);
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// | Actions                                                               |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']))
{
  $lines = explode("\n", $_POST['users']);

  $emails_to_create = array();
  $emails_rejected = array();
  $emails_already_exist = array();
  $emails_created = array();
  $emails_on_error = array();
  $username_for = array();
  
  foreach ($lines as $line)
  {
    $fields = explode(';', $line);
    $email = trim($fields[0]);

    if (isset($fields[1]))
    {
      $username_for[$email] = trim($fields[1]);
    }

    $email = trim($email);

    // this test requires PHP 5.2+
    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
    {
      $emails_to_check[] = $email;
      
      if (!get_userid_by_email($email))
      {
        $emails_to_create[] = $email;
      }
      else
      {
        $emails_existing[] = $email;
      }
    }
    elseif (!empty($email))
    {
      $emails_rejected[] = $email;
    }
  }

  foreach ($emails_to_create as $email)
  {    
    // find a username
    if (isset($username_for[$email]))
    {
      $username = $username_for[$email];
    }
    else
    {
      list($username,) = explode('@', $email);
    }
    
    $base_username = $username;
    $i = 2;
    while (get_userid($username))
    {
      $username = $base_username.($i++);
    }

    // find a password
    $user_id = register_user($username, generate_key(16), $email, true, $errors, false);
    if (!empty($errors))
    {
      $emails_on_error[] = $email;
    }
    else
    {
      $emails_created[] = $email;

      switch_lang_to(get_default_language());
      $generate_link = generate_password_link($user_id, true);
      $email_params = pwg_generate_set_password_mail($username, $generate_link['password_link'], $conf['gallery_title'], $generate_link['time_validation']);
      pwg_mail($email, $email_params);
      switch_lang_back();
    }
  }

  $emails_for_form = array();
  
  if (!empty($emails_created))
  {
    array_push(
      $page['infos'],
      sprintf(
        l10n('%d users registered'),
        count($emails_created)
        )
      );
  }

  if (!empty($emails_on_error))
  {
    array_push(
      $page['errors'],
      sprintf(
        l10n('%d registrations on error: %s'),
        count($emails_on_error),
        implode(', ', $emails_on_error)
        )
      );

    $emails_for_form = array_merge($emails_for_form, $emails_on_error);
  }
  
  if (!empty($emails_rejected))
  {
    array_push(
      $page['errors'],
      sprintf(
        l10n('%d email addresses rejected: %s'),
        count($emails_rejected),
        implode(', ', $emails_rejected)
        )
      );

    $emails_for_form = array_merge($emails_for_form, $emails_rejected);
  }

  if (!empty($emails_existing))
  {
    array_push(
      $page['warnings'],
      sprintf(
        l10n('%d email addresses already exist: %s'),
        count($emails_existing),
        implode(', ', $emails_existing)
        )
      );
  }  
}

// +-----------------------------------------------------------------------+
// | form                                                                  |
// +-----------------------------------------------------------------------+

// define template file
$template->set_filename('umr_content', realpath(UMR_PATH . 'admin.tpl'));

// template vars
$template->assign(array(
  'UMR_PATH'=> get_root_url() . UMR_PATH, // used for images, scripts, ... access
  'UMR_ABS_PATH'=> realpath(UMR_PATH),    // used for template inclusion (Smarty needs a real path)
  'UMR_ADMIN' => UMR_ADMIN,
  ));

if (isset($emails_for_form) and !empty($emails_for_form))
{
  $template->assign('EMAILS', implode("\n", $emails_for_form));
}

// +-----------------------------------------------------------------------+
// | sending html code                                                     |
// +-----------------------------------------------------------------------+

// send page content
$template->assign_var_from_handle('ADMIN_CONTENT', 'umr_content');

?>