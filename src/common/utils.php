<?php
if (!function_exists('sd_api_accepts')) :
    function sd_api_accepts($headers, $content)
    {
        return 0 !== count(array_filter($headers['accept'], fn ($value) => str_contains(strtolower($value), $content)));
    }
endif;

if (!function_exists('sd_register_block')) :
    function sd_register_block($teams, string $function, array $attributes = array()): string
    {
        $uid = esc_attr(uniqid());
        $stale = false;
        $teamarray = is_array($teams) ? $teams : array($teams);
        foreach ($teamarray as $team) {
            if (isset($team)) {
                if ($team->isStale) {
                    $stale = true;
                    break;
                }
            } else {
                $stale = true;
            }
        }
?>
        <script type="text/javascript">
            (function() {
                sdRegisterBlock({
                    uid: '<?php echo $uid ?>',
                    url: '<?php echo get_site_url(null, '/wp-json/sportsdata/v1') ?>',
                    function: <?php echo "'$function'" ?>,
                    attributes: <?php echo json_encode($attributes) ?>,
                    <?php if (is_object($teams)) { ?>
                        <?php echo  "teamkey: '$teams->key',"; ?>
                    <?php } ?>
                    hash: {
                        <?php foreach ($teamarray as $team) {
                            if (isset($team)) {
                                echo "\"$team->key\": '$team->hash'";
                            }
                        } ?>
                    },
                    force: <?php echo get_query_var('force_refresh') === 'true' ? "true" : "false" ?>,
                    isStale: <?php echo ($stale) ? "true" : "false" ?>
                });
            })();
        </script>
<?php
        return $uid;
    }
endif;

if (!function_exists('console_log')) :
    function console_log($message)
    {

        $message = htmlspecialchars(stripslashes($message));
        //Replacing Quotes, so that it does not mess up the script
        $message = str_replace('"', "-", $message);
        $message = str_replace("'", "-", $message);

        echo "<script>console.log('{$message}')</script>";
    }
endif;
