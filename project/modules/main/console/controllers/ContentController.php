<?php

namespace project\modules\main\console\controllers;

use Craft;
use craft\db\Table;
use craft\errors\ShellCommandException;
use craft\helpers\FileHelper;
use craft\volumes\Local;
use project\modules\main\MainModule;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use yii\base\ErrorException;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Exception;
use const PHP_EOL;

class ContentController extends Controller
{
    public function actionTest()
    {
        // .\craft main/content/test
        echo 'Here is a console controller';
    }


    // This is an example, better call a service method in real live.
    //
    // .\craft main/content/clear-image-transform-directories
    //

    /**
     * Clear all image transform stuff
     *
     * @throws Exception
     */
    public function actionClearImageTransformDirectories()
    {

        if ($this->confirm('This will delete all image transform data', true)) {

            Craft::$app->getDb()->createCommand()
                ->truncateTable(Table::ASSETTRANSFORMINDEX)
                ->execute();

            $volumes = Craft::$app->volumes->getAllVolumes();
            foreach ($volumes as $volume) {
                $this->_clearVolume($volume);
            }

            echo 'Image Transform Directories cleared';
        }
    }

    private function _clearVolume(Local $volume)
    {
        $root = Craft::parseEnv($volume->path);
        $dirs = $this->_getTransFormDirs($root);
        foreach ($dirs as $dir) {
            $this->_deleteDir($dir);
        }
    }

    /**
     * @param $path
     * @return array
     */
    private function _getTransFormDirs($path): array
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        $dirs = [];
        foreach ($rii as $dir) {
            if ($dir->isDir() && strpos($dir->getPathname(), DIRECTORY_SEPARATOR . '_') &&
                !strpos($dir->getPathname(), '..')) {
                $dirs[] = $dir->getPathname();
            }
        }

        return $dirs;
    }

    private function _deleteDir($dir)
    {
        if (is_dir($dir)) {
            try {
                echo "Deleting {$dir}\n";
                FileHelper::clearDirectory($dir);
                rmdir($dir);
            } catch (ErrorException $e) {
                echo 'Error deleting ' . $dir . ': ' . $e->getMessage() . PHP_EOL;
            }
        }
    }

    /**
     * Adds a specified number of days to showtime attribute of screenings
     *
     * @param int $days
     * @return int
     * @throws ShellCommandException
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function actionAddDaysToShowtime($days = 30): int
    {
        if (!$this->confirm('Really add ' . $days . ' days?')) {
            return ExitCode::OK;
        }

        if ($this->confirm('Backup database?')) {
            try {
                $this->stdout('Backing up the database ... ');
                $path = Craft::$app->db->backup();
            } catch (ShellCommandException $e) {
                $this->stderr(ExitCode::getReason(ExitCode::OSERR) . PHP_EOL);
                $this->stderr($e->getMessage());
                return ExitCode::OSERR;
            } catch (\yii\base\Exception $e) {
                $this->stderr('There is no backup command configured');
                return ExitCode::USAGE;
            }
            $this->stdout('done' . PHP_EOL);
        }

        try {
            MainModule::getInstance()->content->addDaysToShowtime($days);
        } catch (Exception $e) {
            $this->stderr(ExitCode::getReason(ExitCode::UNSPECIFIED_ERROR) . PHP_EOL);
            $this->stderr($e->getMessage());
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout('Command succesfully completed');
        return ExitCode::OK;
    }

}
