<?php
namespace Avito\Export\Feed\Agent;

use Avito\Export\Watcher;

class Restart extends Watcher\Agent\Refresh
{
    public static function process(string $setupType, int $setupId) : bool
    {
        $processor = Watcher\Agent\Factory::makeProcessor('restart', $setupType, $setupId);

        return $processor->run(Watcher\Engine\Controller::ACTION_FULL, [
            'USE_TMP' => true,
        ]);
    }
}
