<?php
/**
 * A user's homepage
 */
session_start();
include 'utilities.php';

connect_to_id_store();

?>

<html>

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>

<div class="jumbotron">
    <div class="container">

        <?php

        if (id_in_session())
        {
            $columnClass = 'col-md-4';

            echo '<h1>Welcome, ' . $_SESSION['username'] . '!</h1>';

            if($_SESSION['username'] == 'admin1') // temporary hard-coded admin user. will replace with admin role in future
            {
                try
                {
                    open_tokens_file($tokenFilePath);
                }
                catch (Exception $e)
                {
                    print_error_message($e->getMessage());
                }


                if(isset($_GET['tokenId']))
                {
                    try
                    {
                        write_new_token($_GET['tokenId']);

                        print_success_message('Received new XSEDE token ' . $tokenFile->tokenId .
                            '! Click <a href="' . $req_url .
                            '?gatewayName=' . $gatewayName .
                            '&email=' . $email .
                            '&portalUserName=' . $_SESSION['username'] .
                            '">here</a> to fetch a new token.');
                    }
                    catch (Exception $e)
                    {
                        print_error_message($e->getMessage());
                    }
                }
                else
                {
                    echo '<p><small>Community token currently set to ' . $tokenFile->tokenId .
                        '. Click <a href="' . $req_url .
                        '?gatewayName=' . $gatewayName .
                        '&email=' . $email .
                        '&portalUserName=' . $_SESSION['username'] .
                        '">here</a> to fetch a new token.</small></p>';
                }
            }
            else // standard user
            {
                /* temporarily remove to avoid confusion during XSEDE tutorial
                if (isset($_SESSION['tokenId']))
                {
                    echo '<p><small>XSEDE token currently active.
                    All experiments launched during this session will use your personal allocation.</small></p>';
                }
                elseif(!isset($_GET['tokenId']) && !isset($_SESSION['tokenId']))
                {
                    echo '<p><small>Currently using community allocation. Click <a href="' .
                        $req_url .
                        '?gatewayName=' . $gatewayName .
                        '&email=' . $email .
                        '&portalUserName=' . $_SESSION['username'] .
                        '">here</a> to use your personal allocation for this session.</small></p>';
                }
                elseif(isset($_GET['tokenId']))
                {
                    $_SESSION['tokenId'] = $_GET['tokenId'];

                    print_success_message('Received XSEDE token!' .
                        '<br>All experiments launched during this session will use your personal allocation.');
                }
                */
            }
        }
        else
        {
            $columnClass = 'col-md-6';

            echo '
                <h1>R Gateway with Spark, Snow/Snowfall and other HPC tools </h1>
                <p>
             		R Gateway with Spark, Snow/Snowfall and other HPC tools
                </p>
                <p><a href="https://github.com/rsandhu1/R-Gateway"
                        target="_blank">See the code <span class="glyphicon glyphicon-new-window"></span></a></p>
                <p><a href="https://github.com/rsandhu1/R-Gateway"
                    target="_blank">View documentation <span class="glyphicon glyphicon-new-window"></span></a></p>
            ';
        }

        ?>








    </div>
</div>

<div class="container">

    <div class="row">
        <?php

        if (id_in_session())
        {
            echo '
                <div class="col-md-4">
                    <h2>R Gateway with Spark, Snow/Snowfall and other HPC tools</h2>

                    <p>
                        R Gateway with Spark, Snow/Snowfall and other HPC tools.
                    </p>
                    <p><a href="https://github.com/rsandhu1/R-Gateway"
                        target="_blank">See the code <span class="glyphicon glyphicon-new-window"></span></a></p>
                    <p><a href="https://github.com/rsandhu1/R-Gateway"
                        target="_blank">View documentation <span class="glyphicon glyphicon-new-window"></span></a></p>
                </div>
            ';
        }

        ?>
        <div class="<?php echo $columnClass; ?>">
            <div class="thumbnail" style="border:none">
                <img src="resources/assets/R.png" alt="R-Gateway">
                <div class="caption">
                    <p>
                       R-Gateway
                    </p>
                    <p>
                       Gateway  with Spark, Snow/Snowfall and other HPC tools.
                    </p>
                    <p><a href="http://www.rstudio.com/"
                          target="_blank">Learn more <span class="glyphicon glyphicon-new-window"></span></a></p>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>

<?php



/**
 * Write the new token to the XML file
 * @param $tokenId
 */
function write_new_token($tokenId)
{
    global $tokenFile;
    global $tokenFilePath;

    // write new tokenId to tokens file
    $tokenFile->tokenId = $tokenId;

    //Format XML to save indented tree rather than one line
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($tokenFile->asXML());
    $dom->save($tokenFilePath);
}

unset($_POST);
