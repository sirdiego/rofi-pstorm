#!/usr/bin/env php
<?php
if (PHP_SAPI !== 'cli') {
    exit;
}

const RECENT_PROJECTS = '/options/recentProjectDirectories.xml';

$pstorm_path = '/usr/local/bin/pstorm';
$config_directory_path = get_config_directory($pstorm_path);

if ($argc === 1) {
    show_projects($config_directory_path);
} else {
    start_project($argv[1]);
}

function get_config_directory($pstorm_path)
{
    $script = file_get_contents($pstorm_path);
    $regex = '/CONFIG_PATH = u\'(.*)\'/m';

    preg_match($regex, $script, $matches);

    return $matches[1];
}

function show_projects($config_directory_path)
{
    $file = $config_directory_path . RECENT_PROJECTS;
    $xpath = '//component[@name=\'RecentDirectoryProjectsManager\']/option[@name=\'recentPaths\']/list/option/@value';

    $optionXml = new SimpleXMLElement($file, null, true);
    $optionElements = $optionXml->xpath($xpath);

    foreach ($optionElements as $optionElement) {
        if ($optionElement->value) {
            $path = str_replace('$USER_HOME$', $_SERVER['HOME'], $optionElement->value);
            echo $path . PHP_EOL;
        }
    }
}

function start_project($project)
{
    echo $project;
    exec('bash -c "exec nohup setsid pstorm \"' . escapeshellcmd($project) . '\" &> /dev/null &"');
}
