<?php

require_once 'base.php';

clearstatcache();
    if (is_file( DP_BASE_DIR . '/includes/config.php' ) ) {

        require_once DP_BASE_DIR . '/includes/config.php';

    }
//    unset($array_file_api);
    if (!isset($GLOBALS['OS_WIN']))
        $GLOBALS['OS_WIN'] = (strstr (PHP_OS, 'WIN') !== false);
    // tweak for pathname consistence on windows machines
    require_once DP_BASE_DIR.'/includes/main_functions.php';
    require_once DP_BASE_DIR.'/includes/db_adodb.php';
    require_once DP_BASE_DIR.'/includes/db_connect.php';
    require_once DP_BASE_DIR.'/classes/ui.class.php';
    require_once DP_BASE_DIR.'/classes/permissions.class.php';
    require_once DP_BASE_DIR.'/includes/session.php';
    require_once DP_BASE_DIR.'/classes/jdatatable.class.php';
    require_once DP_BASE_DIR.'/includes/mail_config.php';
    
    // manage the session variable(s)
    dpSessionStart(array('AppUI'));
    
    // check if session has previously been initialised
    if (!isset( $_SESSION['AppUI']) || isset($_GET['logout'])) {
        if (isset($_GET['logout']) && isset($_SESSION['AppUI']->user_id)) {
            $AppUI =& $_SESSION['AppUI'];
                    $user_id = $AppUI->user_id;
            addHistory('login', $AppUI->user_id, 'logout', $AppUI->user_first_name . ' ' .  $AppUI->user_last_name);
        }
        
            $_SESSION['AppUI'] = new CAppUI();
    }
    $AppUI =& $_SESSION['AppUI'];
    $last_insert_id = $AppUI->last_insert_id;
    $AppUI->checkStyle();
    // load the commonly used classes
    require_once( $AppUI->getSystemClass( 'date' ) );
    require_once( $AppUI->getSystemClass( 'dp' ) );
    require_once( $AppUI->getSystemClass( 'query' ) );
    
    require_once DP_BASE_DIR.'/misc/debug.php';
    
    //Function for update lost action in user_access_log
    $AppUI->updateLastAction($last_insert_id);
    // load default preferences if not logged in
    if ($AppUI->doLogin()) {
        $AppUI->loadPrefs( 0 );
    }
    // Function register logout in user_acces_log
    if (isset($user_id) && isset($_GET['logout'])) {
        $AppUI->registerLogout($user_id);
        if (isset($_COOKIE['path_company'])) {
            setcookie('path_company', '', time()-4);
        }
        if (isset($_COOKIE['check_au'])) {
            setcookie('check_au', '', time()-4);
        }
        header('Content-type: application/json;charset=UTF-8');
//        echo json_encode("");
        exit();
    }
//    if (isset($_POST['check_login']) && $_POST['check_login'] == 'check_login') {
//        if (isset($_POST['user_id']) && $_POST['user_id'] > 0) {
//            $AppUI->user_id = $_POST['user_id'];
//
//            header('Content-type: application/json;charset=UTF-8');
//            echo json_encode(array('check_login'=>$AppUI->user_id));
//        } else {
//            $AppUI->user_id = -1;
//
//            header('Content-type: application/json;charset=UTF-8');
//            echo json_encode(array('check_login'=>'false'));
//        }
//        exit();
//    }
    if (isset($_GET['mo']) && $_GET['mo'] == 'oo') {
        echo $AppUI->user_id;
        exit();
    }
    if (dPgetParam($_POST, 'registration', 0)) {
        $reg = dPgetParam($_POST, 'registration', 0);
        $uistyle = dPgetConfig('host_style');
        $AppUI->setUserLocale();
        @include_once DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/locales.php';
        @include_once DP_BASE_DIR.'/locales/core.php';
        setlocale( LC_TIME, $AppUI->user_lang );
        if ($reg == 1) {
            require DP_BASE_DIR.'/modules/users/api.php';
            register_user();
        }
        exit();
    }
    
    if (isset($_REQUEST['login'])) {
        
        $uistyle = dPgetConfig('host_style');
	$AppUI->setUserLocale();
	@include_once DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/locales.php';
	@include_once DP_BASE_DIR.'/locales/core.php';
	setlocale( LC_TIME, $AppUI->user_lang );
	if (isset($_REQUEST['login'])) {
		require  DP_BASE_DIR.'/includes/do_login.php';
		doLogin();
	} else {
		require  'api.php';
	}
        
	exit();
    }
    if(isset($_REQUEST['booking_not_login'])) {
        $uistyle = dPgetConfig('host_style');
	$AppUI->setUserLocale();
	@include_once DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/locales.php';
	@include_once DP_BASE_DIR.'/locales/core.php';
	setlocale( LC_TIME, $AppUI->user_lang );
        if(isset($_REQUEST['booking_not_login'])) {
            require DP_BASE_DIR.'/modules/users/api.php';
            register_create_booking();
        }
        exit();
    }
    if(isset($_GET['get_system'])) {
        require_once ('modules/system/cconfig.class.php');
        require_once (DP_BASE_DIR."/modules/jobs/CJobSetting.class.php");

        $CConfig = new CConfigNew();
        $job_setting = new CJobSetting();

            // get system list

                    $services = $CConfig->getRecordConfigByList('service');
                    $service_arr = array();
                    if (count($services) > 0) {
                        foreach ($services as $service) {
                            $service_arr[] = array(
                                    "service_id" => $service['config_id'],
                                    "service_name" => $service['config_name']
                            );
                        }
                    }

                    $job_status = $CConfig->getRecordConfigByList('job status');
                    $status_arr = array();
                    if (count($job_status) > 0) {
                        foreach ($job_status as $status) {
                            $status_arr[] = array(
                                    "job_status_id" => $status['config_id'],
                                    "job_status_name" => $status['config_name']
                            );
                        }
                    }

                    $minTime = $job_setting->getAllRecordSetting(2);
                    $maxTime = $job_setting->getAllRecordSetting(3);
                    $time_arr_sys = explode(':', $maxTime['job_setting_value']);
                    $time_float = $time_arr_sys[0] + ($time_arr_sys[1]/60);
                    $value_arr = explode('.', ($time_float - 0.5));
                    if ($value_arr[0] < 10)
                        $h = '0'.$value_arr[0];
                    else {
                        $h = $value_arr[0];
                    }
                    $s = $value_arr[1]*6;
                    if ($s < 10)
                        $s = '0'.$s;
                    $time = $h.':'.$s;

                    $start_time = str_replace(":", "", $minTime['job_setting_value']);
                    $end_time = str_replace(":", "", $time);


            $data = array();
            $data['hour_block'] = 0.5;
            $data['start_time'] = $start_time;
            $data['end_time'] = $end_time;
            $data['services'] = $service_arr;
            $data['job_statuses'] = $status_arr;

            header('Content-type: application/json;charset=UTF-8');
            echo json_encode($data);
            exit();
    }
    if(isset($_GET['booked']) && $_GET['booked'] == 'booked_not_login') {
        require_once (DP_BASE_DIR."/modules/jobs/jobManager.class.php");
        $jobManager = new JobManager();
        
        if (isset($_GET['year']) && isset($_GET['month'])) {
        $year = dPgetParam($_GET, 'year', date('Y'));
        $month = dPgetParam($_GET, 'month', date('m'));

        $TimeBlog = dPgetSysVal('TimeBlog');

        $date_fully = array();
        $time_arr = array();

        $num_date = date('t', strtotime($year.'-'.$month.'-01'));
        for ($i = 1; $i <= $num_date; $i++) {
            $date = $year.'-'.$month.'-'.$i;
            $check_fully = $jobManager->day_is_full($date);
            $check_sun = date('D', strtotime($date));
            if ($check_fully == 1 || $check_sun == 'Sun' || $check_sun == 'Sat') {
                $date_fully[] = $date;
            } else {
                // lay ra cac timeBlog fully(tat ca cac team deu co task trong timeBlog nay)
                if (count($TimeBlog) > 0) {
                    $time_blog = array();
                    foreach ($TimeBlog as $key => $value) {
                        $check_time_full = $jobManager->time_is_full($date, $key);
                        if ($check_time_full == 1) {
                            $time_blog[] = str_replace(":", "", $key);
                        }
                    }
                    if (count($time_blog) > 0)
                        $time_arr[$date] = $time_blog;
                }
            }
        }
        $data = array();
        $data['fully_booked_dates'] = $date_fully;
        $data['not_available_timeslots'][] = $time_arr;

        header('Content-type: application/json;charset=UTF-8');
        echo json_encode($data);
        exit();
    } else {
        header('Content-type: application/json;charset=UTF-8');
        header("Status", true, 500);
        header("Message: Url error. Param must is: year=<YYYY>&month=<1-12>");
        exit();
    }

    }
//$AppUI->user_id = 1;
    // supported since PHP 4.2
    //set the default ui style
    $uistyle = $AppUI->getPref( 'UISTYLE' ) ? $AppUI->getPref( 'UISTYLE' ) : dPgetConfig('host_style');
    // clear out main url parameters
    $m = '';
    $a = '';
    $u = '';
    
    //check if we are logged in
    if ($AppUI->doLogin()) {
//        // load basic locale settings
//        $AppUI->setUserLocale();
//        @include_once( './locales/'.$AppUI->user_locale.'/locales.php' );
//        @include_once( './locales/core.php' );
//        setlocale( LC_TIME, $AppUI->user_lang );
//        $redirect = $_SERVER['QUERY_STRING']?strip_tags($_SERVER['QUERY_STRING']):'';
//        if (strpos( $redirect, 'logout' ) !== false) {
//            $redirect = '';
//        }
        
        // destroy the current session and output login page
        session_unset();
        session_destroy();
        header('Content-type: application/json;charset=UTF-8');
        header("Status", true, 500);
        header("Message: You don't login");
        echo json_encode(array('Message'=>'Login required'));
        exit();
//        exit;
    }
    $AppUI->setUserLocale();
    
    // bring in the rest of the support and localisation files
    require_once DP_BASE_DIR.'/includes/permissions.php';
    
    $def_a = 'api';
    if (!isset($_GET['m']) && !empty($ocio_config['default_view_m'])) {
        $m = $ocio_config['default_view_m'];
        $def_a = !empty($ocio_config['default_view_a']) ? $ocio_config['default_view_a'] : $def_a;
    } else {
        // set the module from the url
        $m = $AppUI->checkFileName(dPgetCleanParam( $_GET, 'm', getReadableModule() ));
    }
    
    // set the action from the url
    $a = $AppUI->checkFileName(dPgetCleanParam( $_GET, 'a', $def_a ));
    
    //
    // Controller param
    //
    define('DEFAULT_CONTROLLER', '__default');
    $api_command = dPgetCleanParam($_GET, 'api', DEFAULT_CONTROLLER);
    
    /*
     * 
     */
    
    $u = $AppUI->checkFileName(dPgetCleanParam( $_GET, 'u', '' ));
    
    // load module based locale settings
    @include_once DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/locales.php';
    @include_once DP_BASE_DIR.'/locales/core.php';
    
    setlocale( LC_TIME, $AppUI->user_lang );
    $m_config = dPgetConfig($m);
    @include_once DP_BASE_DIR.'/functions/' . $m . '_func.php';
    
    // TODO: canRead/Edit assignements should be moved into each file
    
    // check overall module permissions
    // these can be further modified by the included action files
    $perms =& $AppUI->acl();
    $canAccess = $perms->checkModule($m, 'access');
    $canRead = $perms->checkModule($m, 'view');
    $canEdit = $perms->checkModule($m, 'edit');
    $canAuthor = $perms->checkModule($m, 'add');
    $canDelete = $perms->checkModule($m, 'delete');
    
    // include the module class file - we use file_exists instead of @ so
    // that any parse errors in the file are reported, rather than errors
    // further down the track.
    $modclass = $AppUI->getModuleClass($m);
    if (file_exists($modclass))
        include_once( $modclass );
    if ($u && file_exists(DP_BASE_DIR.'/modules/'.$m.'/'.$u.'/'.$u.'.class.php'))
        include_once DP_BASE_DIR.'/modules/'.$m.'/'.$u.'/'.$u.'.class.php';
    
    // start output proper
    include DP_BASE_DIR.'/style/'.$uistyle.'/overrides.php';
    ob_start();
    
    $load_extra_bool = false;
    
    // if not loading extra, load as normal dP proper
    if (!$load_extra_bool)
        $module_file = DP_BASE_DIR.'/modules/'.$m.'/'.($u?($u.'/'):'').$a.'.php';
    
    //--- end code change by Joseph Pham Ngoc Cuong - 28 May 2009
    
    if (file_exists($module_file)) {
        require $module_file;
        
        // call __default() function of module if no controller is specified.
        if (function_exists($api_command)) {
            $api_command();
        } else {
            header('Content-type: application/json;charset=UTF-8');
            header("Status", true, 500);
            echo json_encode(array('test_server_redirect'=>'Nil'));
            exit();
//            header('Message: Function missing!');
        }
    } else {
        header('Content-type: application/json;charset=UTF-8');
        header("Status", true, 500);
        header('Message: Missing file. Possible Module "'.$m.'" missing!');
    }
    
    function test_service_redirect() {
//            if (file_exists($module_file)) {
//                require $module_file;
//
//                // call __default() function of module if no controller is specified.
//                if (function_exists($api_command)) {
//                    $api_command();
//                } else {
//                    header('Content-type: application/json');
//                    header("Status", true, 500);
//                    echo json_encode(array('test_server_redirect'=>'Nil'));
//                    exit();
//        //            header('Message: Function missing!');
//                }
//            } else {
//                header('Content-type: application/json');
//                header("Status", true, 500);
//                header('Message: Missing file. Possible Module "'.$m.'" missing!');
//            }
        $params = array('http' => array(
            'method' => 'HEAD',
            'ignore_errors' => true
        ));

        $context = stream_context_create($params);
        foreach(array('http://sit2.lionsoftwaresolutions.com/abms', 'http://localhost/aircon/abms') as $url) {
            $fp = fopen($url, 'rb', false, $context);
            $result = stream_get_contents($fp);

            if ($result === false) {
                throw new Exception("Could not read data from {$url}");
            } else if (! strstr($http_response_header[0], '301')) {
                // Do something here
                throw new Exception("111");
            }
        }
//            $url = $_SERVER['SERVER_NAME'];
//            header('Content-type: application/json');
//            echo json_encode(array('test_server_redirect'=>$url));
//            exit();
    }
 
    ob_end_flush();
    
?>
