<?php
namespace Horde\Hordectl;
/**
 * Horde Installation finder
 * 
 * Abstract access to an installation's registry and backends
 * Don't pollute global namespace
 */
class HordeInstallationFinder
{
    public function __construct()
    {

    }

    /**
     * Find a horde installation
     * 
     * Currently, only implemented by assuming we are in a composer setup.
     * This allows us to hardcode a path.
     * More options are desirable.
     * 
     * @return string Path to installation's HORDE_BASE dir
     */
    public function find()
    {
        $usualSuspects = [
            dirname(__DIR__, 4) . '/web/horde',
            // current dir is hordedir and it is a modern installation
            getcwd() . '/vendor/horde/horde',
            // current dir and older installation
            getcwd() . '/web/horde',

        ];
        foreach ($usualSuspects as $candidate) {
            if (file_exists($candidate . '/lib/Application.php')) {
                return $candidate;
            }
        }
        throw new \Exception("No Horde found");
    }
}