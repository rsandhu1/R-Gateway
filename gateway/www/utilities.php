<?php
/**
 * Basic utility functions
 */

define('ROOT_DIR', __DIR__);

/**
 * Define configuration constants
 */

//const USER_STORE = 'WSO2','XML','USER_API';
const USER_STORE = 'WSO2';


$req_url = 'https://gw111.iu.xsede.org:8443/credential-store/acs-start-servlet';
$gatewayName = 'R-Gateway';
$email = 'admin@gw120.iu.xsede.org';
$tokenFilePath = 'tokens.xml';
$tokenFile = null;



/**
 * Import user store utilities
 */
switch (USER_STORE)
{
    case 'WSO2':
        require_once 'wsis_utilities.php'; // WS02 Identity Server
        break;
    case 'XML':
        require_once 'xml_id_utilities.php'; // XML user database
        break;
    case 'USER_API':
        require_once 'userapi_utilities.php'; // Airavata UserAPI
        break;
}

/**
 * Print success message
 * @param $message
 */
function print_success_message($message)
{
    echo '<div class="alert alert-success">' . $message . '</div>';
}

/**
 * Print warning message
 * @param $message
 */
function print_warning_message($message)
{
    echo '<div class="alert alert-warning">' . $message . '</div>';
}

/**
 * Print error message
 * @param $message
 */
function print_error_message($message)
{
    echo '<div class="alert alert-danger">' . $message . '</div>';
}

/**
 * Print info message
 * @param $message
 */
function print_info_message($message)
{
    echo '<div class="alert alert-info">' . $message . '</div>';
}

/**
 * Redirect to the given url
 * @param $url
 */
function redirect($url)
{
    echo '<meta http-equiv="Refresh" content="0; URL=' . $url . '">';
}

/**
 * Return true if the form has been submitted
 * @return bool
 */
function form_submitted()
{
    return isset($_POST['Submit']);
}

/**
 * Compare the submitted credentials with those stored in the database
 * @param $username
 * @param $password
 * @return bool
 */
function id_matches_db($username, $password)
{
    global $idStore;

    if($idStore->authenticate($username, $password))
    {
        return true;
    }else{
        return false;
    }
}


/**
 * Store username in session variables
 * @param $username
 */
function store_id_in_session($username)
{
    $_SESSION['username'] = $username;
    $_SESSION['loggedin'] = true;
}

/**
 * Return true if the username stored in the session
 * @return bool
 */
function id_in_session()
{
    return isset($_SESSION['username']) && isset($_SESSION['loggedin']);
}

/**
 * Verify user is already logged in. If not, redirect to login.
 */
function verify_login()
{
    if (id_in_session())
    {
        return;
    }
    else
    {
        print_error_message('User is not logged in!');
        redirect('index.php');
    }
}

/**
 * Connect to the ID store
 */
function connect_to_id_store()
{
    global $idStore;

    switch (USER_STORE)
    {
        case 'WSO2':
            $idStore = new WSISUtilities(); // WS02 Identity Server
            break;
        case 'XML':
            $idStore = new XmlIdUtilities(); // XML user database
            break;
        case 'USER_API':
            $idStore = new UserAPIUtilities(); // Airavata UserAPI
            break;
    }

    try
    {
        $idStore->connect();
    }
    catch (Exception $e)
    {
        print_error_message('<p>Error connecting to ID store.
            Please try again later or report a bug using the link in the Help menu</p>' .
            '<p>' . $e->getMessage() . '</p>');
    }
}

/**
 * Create navigation bar
 * Used for all pages
 */
function create_nav_bar()
{
    $menus = array
    (
        'Editor' => array
        (
            array('label' => 'R Editor', 'url' => 'editor.php'),
            array('label' => 'Search History', 'url' => 'history.php')
        ),
        'Help' => array
        (
            array('label' => 'Report Issue', 'url' => '#'),
            array('label' => 'Request Feature', 'url' => '#')
        )
    );

    $selfExplode = explode('/', $_SERVER['PHP_SELF']);



    // nav bar and left-aligned content

    echo '<nav class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                       <span class="sr-only">Toggle navigation</span>
                       <span class="icon-bar"></span>
                       <span class="icon-bar"></span>
                       <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php" title="R-Gateway">R-Gateway</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">';


    foreach ($menus as $label => $options)
    {
        isset($_SESSION['loggedin']) && $_SESSION['loggedin']? $disabled = '' : $disabled = ' class="disabled"';

        echo '<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $label . '<span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">';

        foreach ($options as $option)
        {
            $id = strtolower(str_replace(' ', '-', $option['label']));

            $option['url'] == $selfExplode[2]? $active = ' class="active"' : $active = '';

            echo '<li' . $active . $disabled . '><a href="' . $option['url'] . '" id=' . $id . '>' . $option['label'] . '</a></li>';
        }

        echo '</ul>
        </li>';
    }


    echo '</ul>

        <ul class="nav navbar-nav navbar-right">';





    // right-aligned content

    if (isset($_SESSION['username']))
    {
        (USER_STORE === "USER_API" && !isset($_SESSION['excede_login'])) ? $link = "user_profile.php" : $link = "index.php";
        echo '<li><a href="' . $link . '"><span class="glyphicon glyphicon-user"></span> ' . $_SESSION['username'] . '</a></li>';
    }

    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'])
    {
        echo '<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Log out</a></li>';
    }
    elseif ($selfExplode[2] == 'login.php')
    {
        echo '<li><a href="create_account.php"><span class="glyphicon glyphicon-user"></span> Create account</a></li>';
    }
    elseif ($selfExplode[2] == 'create_account.php')
    {
        echo '<li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Log in</a></li>';
    }
    elseif ($selfExplode[2] == 'index.php')
    {
        echo '<li><a href="create_account.php"><span class="glyphicon glyphicon-user"></span> Create account</a></li>';
        echo '<li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Log in</a></li>';
    }


    echo    '</ul>
    </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
    </nav>';
}

/**
 * Create head tag
 * Used for all pages
 */
function create_head()
{
    header( 'Cache-Control: no-store, no-cache, must-revalidate' );
    header( 'Cache-Control: post-check=0, pre-check=0', false );
    header( 'Pragma: no-cache' );

    echo'
        <head>
            <title>PHP Reference Gateway</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="icon" href="resources/assets/favicon.ico" type="image/x-icon">
            <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
            <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

            <!-- Jira Issue Collector - Report Issue -->
            <script type="text/javascript"
                    src="https://gateways.atlassian.net/s/31280375aecc888d5140f63e1dc78a93-T/en_USmlc07/6328/46/1.4.13/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=en-US&collectorId=b1572922"></script>

            <!-- Jira Issue Collector - Request Feature -->
            <script type="text/javascript"
                src="https://gateways.atlassian.net/s/31280375aecc888d5140f63e1dc78a93-T/en_USmlc07/6328/46/1.4.13/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=en-US&collectorId=674243b0"></script>


            <script type="text/javascript">
                window.ATL_JQ_PAGE_PROPS = $.extend(window.ATL_JQ_PAGE_PROPS, {
                    "b1572922":
                    {
                        "triggerFunction": function(showCollectorDialog) {
                            //Requries that jQuery is available!
                            jQuery("#report-issue").click(function(e) {
                                e.preventDefault();
                                showCollectorDialog();
                            });
                        }
                    },
                    "674243b0":
                    {
                        "triggerFunction": function(showCollectorDialog) {
                            //Requries that jQuery is available!
                            jQuery("#request-feature").click(function(e) {
                                e.preventDefault();
                                showCollectorDialog();
                            });
                        }
                    }
                });
            </script>

        </head>
    ';
}


/**
 * Open the XML file containing the community token
 * @param $tokenFilePath
 * @throws Exception
 */
function open_tokens_file($tokenFilePath)
{
    global $tokenFile;

    if (file_exists($tokenFilePath))
    {
        $tokenFile = simplexml_load_file($tokenFilePath);
    }
    else
    {
        throw new Exception('Error: Cannot connect to tokens database!');
    }


    if (!$tokenFile)
    {
        throw new Exception('Error: Cannot open tokens database!');
    }
}
